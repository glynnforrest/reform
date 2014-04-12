<?php

namespace Reform\Tests\Validation\Rule;

require_once __DIR__ . '/../../../../bootstrap.php';

use Reform\Validation\Rule\Required;

/**
 * RequiredTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RequiredTest extends RuleTest
{

    protected $rule;

    public function setup()
    {
        $this->rule = new Required();
    }

    public function dataProvider()
    {
        return array(
            array(0, true),
            array('0', true),
            array(1, true),
            array(-1, true),
            array('3', true),
            array(-5, true),
            array('foo', true),
            array('user1', true),
            array('', false),
            array(null, false),
            array(array(), false)
        );
    }

}