<?php

namespace Reform\Tests\Validation\Rule;

require_once __DIR__ . '/../../../../bootstrap.php';

use Reform\Validation\Rule\Length;

/**
 * LengthTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LengthTest extends \PHPUnit_Framework_TestCase
{

    protected $rule;

    public function dataProvider()
    {
        return array(
            array(0, 0, '', true),
            array(0, 2, 'a', true),
            array(2, 2, 'aa', true),
            array(2, 4, 'foo', true)
        );
    }

    /**
     * @dataProvider dataProvider()
     */
    public function testRule($min, $max, $value, $pass)
    {
        $rule = new Length($min, $max);
        $result = $this->getMock('Reform\Validation\Result');
        if ($pass) {
            $this->assertTrue($rule->validate($result, 'value', $value));
        } else {
            $this->assertFalse($rule->validate($result, 'value', $value));
        }
    }

}