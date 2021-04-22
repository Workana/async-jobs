<?php
namespace Workana\AsyncJobs\Tests;

use PHPUnit\Framework\TestCase;
use Mockery as m;

abstract class Test extends TestCase
{
    public function tearDown(): void
    {
        m::close();
    }
}