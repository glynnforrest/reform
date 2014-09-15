<?php

namespace Reform\Tests\Validation\Rule;

use Reform\Validation\Rule\Integer;

/**
 * IntegerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class IntegerTest extends RuleTest
{

    protected $rule;

    public function setup()
    {
        $this->rule = new Integer();
    }

    public function dataProvider()
    {
        return array(
            array(0, false),
            array(1, true),
            array(-1, true),
            array('3', true),
            array('-5', true),
            array('foo', false),
            array(4.5, false),
        );
    }

}