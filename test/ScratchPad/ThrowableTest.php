<?php

namespace ScratchPad;

use PHPUnit\Framework\TestCase;

class ThrowableTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function test()
    {
        $this->expectErrorMessage('Call to a member function func() on null');

        try
        {
            $a = null;
            $a->func();
        }
        catch (\Throwable $t)
        {
            throw $t;
        }
    }
}
