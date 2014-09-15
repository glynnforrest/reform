<?php

namespace Reform\Tests\Validation\Rule;

use Reform\Validation\Rule\Range;

/**
 * RangeTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RangeTest extends \PHPUnit_Framework_TestCase
{

    public function dataProvider()
    {
        return array(
            array(1, 5, 1, true),
            array(0, 5, 1, true),
            array(0, 5, 0, true),
            array(-3, 5, 1, true),
            array(-3, 5, -3, true),
            array(0, 0, 0, true),
            array(-9, -2, -3, true),
        );
    }

    /**
     * @dataProvider dataProvider()
     */
    public function testRule($min, $max, $value, $pass)
    {
        $rule = new Range($min, $max);
        $result = $this->getMock('Reform\Validation\Result');
        if ($pass) {
            $this->assertTrue($rule->validate($result, 'value', $value));
        } else {
            $this->assertFalse($rule->validate($result, 'value', $value));
        }
    }

}