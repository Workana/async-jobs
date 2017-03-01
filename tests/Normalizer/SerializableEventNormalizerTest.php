<?php
namespace Workana\AsyncJobs\Tests\Normalizer;

use Workana\AsyncJobs\AsyncAction;
use Workana\AsyncJobs\Normalizer\Accesor;
use Workana\AsyncJobs\Normalizer\AsyncActionNormalizer;
use Workana\AsyncJobs\Normalizer\SerializableEventNormalizer;
use Workana\AsyncJobs\SerializableEvent;
use Workana\AsyncJobs\Tests\Test;

class SerializableEventNormalizerTest extends Test
{
    /**
     * @var SerializableEventNormalizer
     */
    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new SerializableEventNormalizer();
    }

    public function testSupportsNormalization()
    {
        $event = new SerializableEvent();
        $this->assertTrue($this->sut->supportsNormalization($event));
    }

    public function testSupportsDenormalization()
    {
        $event = new SerializableEvent();
        $normalized =  $this->sut->normalize($event);
        $this->assertTrue($this->sut->supportsDenormalization($normalized, 'Workana\AsyncJobs\SerializableEvent'));
    }


}