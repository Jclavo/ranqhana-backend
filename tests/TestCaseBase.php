<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCaseBase extends BaseTestCase
{
    protected function factoryCreate($class, $attributes = [], $times = null)
    {
        return factory($class, $times)->create($attributes);
    }

    protected function factoryMake($class, $attributes = [], $times = null)
    {
        return factory($class, $times)->make($attributes);
    }

    protected function factoryRaw($class, $attributes = [], $times = null)
    {
        return factory($class, $times)->raw($attributes);
    }

}