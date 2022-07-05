<?php

namespace Guzzle6Http\Test\Handler;

use Guzzle6Http\Handler\EasyHandle;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Guzzle6Http\Handler\EasyHandle
 */
class EasyHandleTest extends TestCase
{
    public function testEnsuresHandleExists()
    {
        $easy = new EasyHandle;
        unset($easy->handle);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('The EasyHandle has been released');
        $easy->handle;
    }
}
