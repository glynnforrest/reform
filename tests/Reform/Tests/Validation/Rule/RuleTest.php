<?php

namespace Reform\Tests\Validation\Rule;

use Reform\Validation\Rule\Rule;

/**
 * RuleTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class RuleTest extends \PHPUnit_Framework_TestCase
{

    protected $rule;

    abstract public function dataProvider();

    /**
     * @dataProvider dataProvider()
     */
    public function testRule($value, $pass)
    {
        $result = $this->getMock('Reform\Validation\Result');
        if ($pass) {
            $this->assertTrue($this->rule->validate($result, 'value', $value));
        } else {
            $this->assertFalse($this->rule->validate($result, 'value', $value));
        }
    }

}