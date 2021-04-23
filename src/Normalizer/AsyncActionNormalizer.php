<?php
namespace Workana\AsyncJobs\Normalizer;

use Assert\Assertion;
use InvalidArgumentException;
use Normalt\Normalizer\AggregateNormalizer;
use Normalt\Normalizer\AggregateNormalizerAware;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

use Workana\AsyncJobs\AsyncAction;
use Workana\AsyncJobs\Parameter;

/**
 * Normalizer for AsyncAction Job
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AsyncActionNormalizer implements NormalizerInterface, DenormalizerInterface, AggregateNormalizerAware
{
    use JobOptionsExtractor;

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
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'actionClass' => get_class($object),
            'class' => $object->getClass(),
            'method' => $object->getMethod(),
            'parameters' => array_map(function($param) {
                return $this->aggregate->normalize($param);
            }, $object->getParameters()),
            'options' => $this->extractOptions($object),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof AsyncAction);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        Assertion::choicesNotEmpty($data, ['actionClass', 'class', 'method', 'options']);

        if (!is_a($data['actionClass'], AsyncAction::class, true)) {
            throw new InvalidArgumentException("Invalid action class: {$data['actionClass']}");
        }

        $action = Accesor::newInstanceWithoutConstructor($data['actionClass']);
        
        $denormalizedParams = array_map(function($paramData) {
            return $this->aggregate->denormalize($paramData, Parameter::class);
        }, $data['parameters']);

        Accesor::set($action, 'class', $data['class']);
        Accesor::set($action, 'method', $data['method']);
        Accesor::set($action, 'parameters', $denormalizedParams);

        $this->hydrateOptions($action, $data['options']);

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, AsyncAction::class, true);
    }
}