<?php

namespace Reform\Tests\Validation\Rule;

use Reform\Validation\Rule\Matches;

/**
 * MatchesTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MatchesTest extends \PHPUnit_Framework_TestCase
{
    protected $rule;

    public function setup()
    {
        $this->rule = new Matches('second');
    }

    public function testMatchingValues()
    {
        $result = $this->getMock('Reform\Validation\Result');
        $this->assertTrue($this->rule->validate($result, 'first', 'foo', array('second' => 'foo')));
    }

    public function testNotMatchingValues()
    {
        $result = $this->getMock('Reform\Validation\Result');
        $this->assertFalse($this->rule->validate($result, 'first', 'foo', array('second' => 'bar')));
    }

    public function testOtherFieldNotInInput()
    {
        $result = $this->getMock('Reform\Validation\Result');
        $this->assertFalse($this->rule->validate($result, 'first', 'foo'));
    }
}
