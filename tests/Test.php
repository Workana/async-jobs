<?php
namespace Workana\AsyncJobs\Tests;

use PHPUnit_Framework_TestCase as PHPUnitTest;
use Mockery as m;

abstract class Test extends PHPUnitTest
{
    public function tearDown()
    {
        m::close();
    }
}