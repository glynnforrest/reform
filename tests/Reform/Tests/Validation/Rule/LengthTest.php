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

    /**
     * @dataProvider dataProvider()
     */
    public function testMinimumOnly($min, $max, $value, $pass)
    {
        $rule = new Length($min);
        $result = $this->getMock('Reform\Validation\Result');
        if ($pass) {
            $this->assertTrue($rule->validate($result, 'value', $value));
        } else {
            $this->assertFalse($rule->validate($result, 'value', $value));
        }
    }

    public function testBetweenMessage()
    {
        $rule = new Length(4, 6);
        $result = $this->getMock('Reform\Validation\Result');
        $result->expects($this->exactly(2))
               ->method('addError')
               ->with('foo', 'Foo must be between 4 and 6 characters long.');

        $rule->validate($result, 'foo', 'bar');
        $rule->validate($result, 'foo', 'bar');
    }

    public function testMinimumMessage()
    {
        $rule = new Length(4);
        $result = $this->getMock('Reform\Validation\Result');
        $result->expects($this->exactly(2))
               ->method('addError')
               ->with('foo', 'Foo must be at least 4 characters long.');

        $rule->validate($result, 'foo', 'bar');
        $rule->validate($result, 'foo', 'bar');
    }

    public function testBetweenConfigurableMessage()
    {
        $rule = new Length(4, 6, 'between :min and :max please');
        $result = $this->getMock('Reform\Validation\Result');
        $result->expects($this->exactly(2))
               ->method('addError')
               ->with('foo', 'between 4 and 6 please');

        $rule->validate($result, 'foo', 'bar');
        $rule->validate($result, 'foo', 'bar');
    }

    public function testMinimumConfigurableMessage()
    {
        $rule = new Length(4, null, 'more than :min please');
        $result = $this->getMock('Reform\Validation\Result');
        $result->expects($this->exactly(2))
               ->method('addError')
               ->with('foo', 'more than 4 please');

        $rule->validate($result, 'foo', 'bar');
        $rule->validate($result, 'foo', 'bar');
    }

}