<?php

namespace Reform\Tests\Validation\Rule;

require_once __DIR__ . '/../../../../bootstrap.php';

use Reform\Validation\Rule\AlphaNumeric;

/**
 * AlphaNumericTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AlphaNumericTest extends RuleTest
{

    protected $rule;

    public function setup()
    {
        $this->rule = new AlphaNumeric();
    }

    public function dataProvider()
    {
        return array(
            array(0, true),
            array(1, true),
            array(-1, false),
            array('3', true),
            array(-5, false),
            array('foo', true),
            array(4.5, false),
            array('user1', true)
        );
    }

}