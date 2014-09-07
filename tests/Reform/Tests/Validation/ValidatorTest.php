<?php

namespace Reform\Tests\Validation;

use Reform\Validation\Validator;
use Reform\Validation\Rule;

/**
 * ValidatorTest
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testAddRule()
    {
        $v = new Validator();
        $rule = $this->getMock('Reform\Validation\Rule\AbstractRule');
        $this->assertSame($v, $v->addRule('one', $rule));
        $this->assertSame(array('one' => array($rule)), $v->getRules());
    }

    public function testValidateReturnsResult() {
        $v = new Validator();
        $this->assertInstanceOf('\Reform\Validation\Result', $v->validate(array()));
    }

    public function testValidateNoRules() {
        $v = new Validator();
        $this->assertTrue($v->validate(array())->isValid());
    }

    public function testValidateSingleRulePassing()
    {
        $v = new Validator();
        $rule = $this->getMock('Reform\Validation\Rule\AbstractRule');
        $this->assertSame($v, $v->addRule('name', $rule));
        $rule->expects($this->once())
             ->method('validate')
             ->will($this->returnValue(true));
        $this->assertTrue($v->validate(array('name' => 'foo'))->isValid());
    }

    public function testValidateForm()
    {
        $v = new Validator();
        $v->addRule('foo', new Rule\Required());
        $v->addRule('foo', new Rule\Alpha());
        $v->addRule('bar', new Rule\Alpha());

        //failure - nothing submitted
        $result = $v->validateForm(array());
        $this->assertFalse($result->isValid());

        //failure - foo is required
        $result = $v->validateForm(array('bar' => 'bar'));
        $this->assertFalse($result->isValid());

        //pass
        $result = $v->validateForm(array('foo' => 'foo'));
        $this->assertTrue($result->isValid());

        //fail - bar is not alphabetical
        $result = $v->validateForm(array('foo' => 'foo', 'bar' => 0));
        $this->assertFalse($result->isValid());
    }

}