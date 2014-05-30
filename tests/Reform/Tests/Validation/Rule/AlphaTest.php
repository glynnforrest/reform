<?php

namespace Reform\Tests\Validation\Rule;

require_once __DIR__ . '/../../../../bootstrap.php';

use Reform\Validation\Rule\Alpha;

/**
 * AlphaTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AlphaTest extends RuleTest
{

    protected $rule;

    public function setup()
    {
        $this->rule = new Alpha();
    }

    public function dataProvider()
    {
        return array(
            array(0, false),
            array(1, false),
            array(-1, false),
            array('3', false),
            array(-5, false),
            array('foo', true),
            array(4.5, false),
            array('user1', false)
        );
    }

}
