<?php
namespace Workana\AsyncJobs\Tests\Normalizer;

use Workana\AsyncJobs\AsyncAction;
use Workana\AsyncJobs\Normalizer\Accesor;
use Workana\AsyncJobs\Normalizer\AsyncActionNormalizer;
use Workana\AsyncJobs\Tests\Test;

class AsyncActionNormalizerTest extends Test
{
    /**
     * @var AsyncActionNormalizer
     */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new AsyncActionNormalizer();
    }

    public function testNormalizeWithOptions()
    {
        $action = new AsyncAction('Foo', 'Bar');
        $action->withDelay(10);
        Accesor::set($action, 'retries', 3);
        $action->withMaxRetries(5);
        $action->withPreferredQueueName('foobar');

        $normalizedAction = $this->sut->normalize($action);

        $this->assertArrayHasKey('options', $normalizedAction);
        $this->assertEquals([
            'delay' => 10,
            'retries' => 3,
            'maxRetries' => 5,
            'preferredQueueName' => 'foobar',
        ], $normalizedAction['options']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The following options are missing: retries, preferredQueueName
     */
    public function testDenormalizeWithoutAllOptions()
    {
        $data = [
            'class' => 'Namespace\Of\Class',
            'actionClass' => AsyncAction::class,
            'method' => 'foo',
            'parameters' => [],
            'options' => [
                'delay' => 0,
                'maxRetries' => 5,
            ]
        ];

        $this->sut->denormalize($data, AsyncAction::class);
    }

    public function testDenormalizeAsyncAction()
    {
        $data = [
            'class' => 'Namespace\Of\Class',
            'actionClass' => AsyncAction::class,
            'method' => 'foo',
            'parameters' => [],
            'options' => [
                'retries' => 3,
                'delay' => 0,
                'maxRetries' => 5,
                'preferredQueueName' => null
            ],
        ];

        $action = $this->sut->denormalize($data, AsyncAction::class);
        $this->assertInstanceOf(AsyncAction::class, $action);
        $this->assertEquals(0, $action->getDelay());
        $this->assertEquals(5, $action->getMaxRetries());
        $this->assertNull($action->getPreferredQueueName());
    }

    private function getNormalizedCanonicalAsyncAction()
    {
        return [
            'class' => 'Namespace\Of\Class',
            'actionClass' => AsyncAction::class,
            'method' => 'foo',
            'parameters' => [],
            'options' => [
                'retries' => 3,
                'delay' => 0,
                'maxRetries' => 5,
                'preferredQueueName' => null
            ]
        ];
    }

    public function allActionKeysDataProvider()
    {
        return [
            ['class'],
            ['actionClass'],
            ['method'],
            ['options']
        ];
    }

    /**
     * @dataProvider allActionKeysDataProvider
     */
    public function testDenormalizeAsyncActionMissingKey($key)
    {
        $data = $this->getNormalizedCanonicalAsyncAction();
        unset($data[$key]);

        $expectedMessage = strtr('The element with key ":key" was not found', [
            ':key' => $key
        ]);

        $this->setExpectedException(\Assert\InvalidArgumentException::class, $expectedMessage);
        $this->sut->denormalize($data, AsyncAction::class);
    }
}