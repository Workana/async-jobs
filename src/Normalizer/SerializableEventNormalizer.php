<?php
namespace Workana\AsyncJobs\Normalizer;

use Bernard\Normalizer\AbstractAggregateNormalizerAware;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Workana\AsyncJobs\Parameter;
use Workana\AsyncJobs\SerializableEvent;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class SerializableEventNormalizer extends AbstractAggregateNormalizerAware  implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $parameters = Accesor::get($object, 'parameters');

        return [
            'class' => get_class($object),
            'parameters' => array_map(function($param) {
                return $this->aggregate->normalize($param);
            }, $parameters),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof SerializableEvent);
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $denormalizedParams = [];
        foreach ($data['parameters'] as $name => $paramData) {
            $denormalizedParams[$name] = $this->aggregate->denormalize($paramData, Parameter::class);
        }

        $event = Accesor::newInstanceWithoutConstructor($data['class']);

        Accesor::set($event, 'parameters', $denormalizedParams);

        return $event;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, SerializableEvent::class, true);
    }
}