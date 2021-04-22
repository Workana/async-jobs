<?php
namespace Workana\AsyncJobs\Tests\Executor;

use Workana\AsyncJobs\Tests\Test;
use Workana\AsyncJobs\AsyncAction;
use Workana\AsyncJobs\Executor\AsyncActionExecutor;
use Psr\Container\ContainerInterface ;
use Mockery as m;

class AsyncActionExecutorTest extends Test
{
    public function testInvoke()
    {
        $param1 = 'a';
        $param2 = 123;
        $param3 = ['a', 'b', 101 => 'c'];
        $action = new AsyncAction('Path\\To\\Class', 'methodName', [$param1, $param2, $param3]);

        $mockedTarget = m::mock('stdClass');
        $mockedTarget->shouldReceive('methodName')
            ->once()
            ->with($param1, $param2, $param3);

        $mockedContainer = m::mock(ContainerInterface::class);
        $mockedContainer->shouldReceive('get')
            ->once()
            ->with('Path\\To\\Class')
            ->andReturn($mockedTarget);

        $executor = new AsyncActionExecutor($mockedContainer);
        $executor($action);
    }
}
