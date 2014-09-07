<?php

namespace Reform\Tests\Form\Row;

use Reform\Helper\Html;
use Reform\Validation\Rule;

/**
 * RowTestCase
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class RowTestCase extends \PHPUnit_Framework_TestCase
{

    abstract protected function getRow($name, $label = null, $attributes = array());

    public function testGetAndSetValue()
    {
        $r = $this->getRow('username');
        $this->assertSame(null, $r->getValue());
        $this->assertSame($r, $r->setValue('user1'));
        $this->assertSame('user1', $r->getValue());
    }

    public function testGetAndSetLabel()
    {
        $r = $this->getRow('password');
        $this->assertSame('Password', $r->getLabel());
        $this->assertSame($r, $r->setLabel('pass-word'));
        $this->assertSame('pass-word', $r->getLabel());
    }

    public function sensibleLabelStringProvider()
    {
        return array(
            array('password', 'Password'),
            array('user-id', 'User id'),
            array('EmailAddress', 'Email address'),
            array('date_format', 'Date format'),
            array('_save', 'Save')
        );
    }

    /**
     * @dataProvider sensibleLabelStringProvider()
     *
     * A sensible string is applied to the label on instantiation, but
     * not on calling setLabel().
     */
    public function testSensibleLabelString($name, $label)
    {
        $r = $this->getRow($name);
        $this->assertSame($label, $r->getLabel());
        $this->assertSame($r, $r->setLabel($name));
        $this->assertSame($name, $r->getLabel());
    }

    public function testLabelHtml()
    {
        $r = $this->getRow('pass-word');
        $html = Html::label('pass-word', 'Pass word');
        $this->assertSame($html, $r->label());
    }

    public function testGetAndSetError()
    {
        $r = $this->getRow('username');
        $this->assertNull($r->getError());
        $msg = 'Username is required.';
        $this->assertSame($r, $r->setError($msg));
        $this->assertSame($msg, $r->getError());
    }

    public function testErrorHtml()
    {
        $r = $this->getRow('username');
        $this->assertNull($r->getError());
        $msg = 'Username is required.';
        $this->assertSame($r, $r->setError($msg));
        $html = '<small class="error">Username is required.</small>';
        $this->assertSame($html, $r->error());
    }

    public function testGetAndSetAttributes()
    {
        $r = $this->getRow('username', null, array('id' => 'username-input'));
        $this->assertSame(array('id' => 'username-input'), $r->getAttributes());

        $this->assertSame($r, $r->setAttributes(array('class' => 'input')));
        $this->assertSame(array('class' => 'input'), $r->getAttributes());
    }

    public function testAddAttributes()
    {
        $r = $this->getRow('username');
        $this->assertSame(array(), $r->getAttributes());

        $this->assertSame($r, $r->addAttributes(array('id' => 'username-input')));
        $this->assertSame(array('id' => 'username-input'), $r->getAttributes());

        $this->assertSame($r, $r->addAttributes(array('class' => 'input')));
        $expected_attributes = array(
            'id' => 'username-input',
            'class' => 'input'
        );
        $this->assertSame($expected_attributes, $r->getAttributes());
    }

    public function testRules()
    {
        $r = $this->getRow('username');
        $this->assertSame(array(), $r->getRules());

        $rule1 = new Rule\Required();
        $this->assertSame($r, $r->addRule($rule1));
        $this->assertSame(array($rule1), $r->getRules());

        $rule2 = new Rule\Required();
        $this->assertSame($r, $r->addRule($rule2));
        $this->assertSame(array($rule1, $rule2), $r->getRules());

        $this->assertSame($r, $r->setRules(array($rule2)));
        $this->assertSame(array($rule2), $r->getRules());
    }

    public function testDisableRulesWithAddRule()
    {
        $r = $this->getRow('foo');

        $r->addRule(new Rule\Required());
        $r->disableRules();
        $this->setExpectedException('Reform\Exception\BuildValidationException');
        $r->addRule(new Rule\Required());
    }

    public function testDisableRulesWithSetRules()
    {
        $r = $this->getRow('foo');

        $r->setRules(array(new Rule\Required()));
        $r->disableRules();
        $this->setExpectedException('Reform\Exception\BuildValidationException');
        $r->setRules(array(new Rule\Required()));
    }

    public function testSubmitForm()
    {
        $r = $this->getRow('foo');
        $this->assertSame(null, $r->getValue());
        $r->submitForm(['foo' => 'foo']);
        $this->assertSame('foo', $r->getValue());
    }

}
