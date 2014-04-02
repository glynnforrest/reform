<?php

namespace Reform\Tests\Validation;

require_once __DIR__ . '/../../../bootstrap.php';

use Reform\Validation\Validator;

/**
 * ValidatorTest
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testCheckReturnsValidator()
    {
        $v = new Validator();
        $rule = $this->getMock('Reform\Validation\Rule\AbstractRule');
        $this->assertSame($v, $v->check('one', $rule));
    }

    public function testValidationReturnsResult() {
        $v = new Validator();
        $this->assertInstanceOf('\Reform\Validation\Result', $v->validate(array()));
    }

    public function testValidationNoRules() {
        $v = new Validator();
        $this->assertTrue($v->validate(array())->isValid());
    }

    public function testValidationSingleRulePassing()
    {
        $v = new Validator();
        $rule = $this->getMock('Reform\Validation\Rule\AbstractRule');
        $v->check('name', $rule);
        $rule->expects($this->once())
             ->method('validate')
             ->will($this->returnValue(true));
        $this->assertTrue($v->validate(array('name' => 'foo'))->isValid());
    }

}