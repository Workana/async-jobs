<?php
namespace Workana\AsyncJobs\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use Normalt\Normalizer\AggregateNormalizer;
use Normalt\Normalizer\AggregateNormalizerAware;
use Workana\AsyncJobs\Configuration;
use Workana\AsyncJobs\Util\ClassUtils;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use InvalidArgumentException;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class QueueableEntityNormalizer implements NormalizerInterface, DenormalizerInterface, AggregateNormalizerAware
{
    public const GEDMO_SOFTDELETE_FILTER_NAME = 'soft-deleteable';

    /**
     * @var AggregateNormalizer
     */
    private $aggregate;

    /**
     * @param AggregateNormalizer $aggregate
     */
    public function setAggregateNormalizer(AggregateNormalizer $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Configuration
     */
    private $config;

    public function __construct(
        EntityManagerInterface $entityManager,
        Configuration  $config
    ) {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if (is_null($object->getId())) {
            throw new InvalidArgumentException('Entity lacks of a valid id for serialization (has been persisted yet?)');
        }

        return [
            'class' => ClassUtils::getRealClass($object),
            'id' => $object->getId(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof QueueableEntityInterface);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (empty($data['id']) || empty($data['class'])) {
            return null;
        }

        $shouldHandleSoftDelete = $this->shouldDisableGedmoSoftDeletableBehaviour();

        if ($shouldHandleSoftDelete)  {
            //Disable softdelete filter context applies
            $this->toggleSoftDeleteFilter();
        }

        $entity =  $this->entityManager->getReference($data['class'], $data['id']);

        if ($shouldHandleSoftDelete)  {
            //Make sure entity is loaded if it is beign proxied
            if ($entity instanceof Proxy) {
                $entity->__load();
            }

            //Finally restore softdelete filter to previous state
            $this->toggleSoftDeleteFilter();
        }

        return $entity;
    }

    /**
     * This toggle -alongside environment check- is used to temporary disable and restore soft delete status after
     * retrieving entity
     *
     * @return void
     */
    private function toggleSoftDeleteFilter()
    {
        $filters = $this->entityManager->getFilters();

        $filters->isEnabled(self::GEDMO_SOFTDELETE_FILTER_NAME) ?
            $filters->disable(self::GEDMO_SOFTDELETE_FILTER_NAME) :
            $filters->enable(self::GEDMO_SOFTDELETE_FILTER_NAME);
    }

    /**
     * Checks if Gedmo SoftDelete behaviour should be handled on denormalization
     *
     * @return bool
     */
    private function shouldDisableGedmoSoftDeletableBehaviour()
    {
        $filters = $this->entityManager->getFilters();
        return (
            $filters->has(self::GEDMO_SOFTDELETE_FILTER_NAME) &&
            $filters->isEnabled(self::GEDMO_SOFTDELETE_FILTER_NAME) &&
            ! $this->config->isEnabledGedmoSoftDeleteBehaviour()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, QueueableEntityInterface::class, true);
    }
}