<?php
namespace Workana\AsyncJobs\Normalizer;

use Normalt\Normalizer\AggregateNormalizer;
use Normalt\Normalizer\AggregateNormalizerAware;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ScalarNormalizer implements NormalizerInterface, DenormalizerInterface, AggregateNormalizerAware
{
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

    private $castFunctionMap = [
        'integer' => 'intval',
        'string' => 'strval',
        'boolean' => 'boolval',
        'float' => 'floatval',
        'double' => 'doubleval',
    ];

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param array|scalar $value  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($value, $format = null, array $context = array())
    {
        return $value;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_null($data) || is_array($data) || is_scalar($data);
    }

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    data to restore
     * @param string $type   the expected class to instantiate
     * @param string $format  format the given data was extracted from
     * @param array  $context options available to the denormalizer
     *
     * @return object
     */
    public function denormalize($data, $type, $format = null, array $context = array())
    {
        if ($type == 'NULL') {
            return null;
        } elseif ($type === 'array') {
            return $data;
        } else {
            $castFunc = $this->castFunctionMap[$type];

            return $castFunc($data);
        }
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer.
     *
     * @param mixed  $data   Data to denormalize from
     * @param string $type   The class to which the data should be denormalized
     * @param string $format The format being deserialized from
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return ($type == 'NULL') || ($type === 'array') || array_key_exists($type, $this->castFunctionMap);
    }
}