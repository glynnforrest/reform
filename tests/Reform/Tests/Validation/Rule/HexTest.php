<?php

namespace Reform\Tests\Validation\Rule;

use Reform\Validation\Rule\Hex;

/**
 * HexTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HexTest extends RuleTest
{
    protected $rule;

    public function setup()
    {
        $this->rule = new Hex();
    }

    public function dataProvider()
    {
        return array(
            array(0, true),
            array('f', true),
            array('f0', true),
            array('ccc', true),
            array('CCC', true),
            array('C2C2C2', true),
            array('G', false),
            array('g', false),
            array('9839g', false),
            array(-1, false),
        );
    }
}
