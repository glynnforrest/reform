<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Choice;

/**
 * ChoiceTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ChoiceTest extends RowTestCase
{
    protected function getRow($name, $label = null, $attributes = array())
    {
        return new Choice($name, $label, $attributes);
    }

    public function testGetAndSetChoices()
    {
        $r = $this->getRow('gender');
        $this->assertSame($r, $r->setChoices(array('male', 'female')));
        $this->assertSame(array('Male' => 'male', 'Female' => 'female'),
        $r->getChoices());

        $this->assertSame($r, $r->setChoices(array()));
        $this->assertSame(array(), $r->getChoices());
    }

    public function testSetChoicesAddsSensibleLabels()
    {
        $r = $this->getRow('variables');
        $this->assertSame($r, $r->setChoices(array('first_name', 'last_name')));

        $nice_array = array('First name' => 'first_name', 'Last name' => 'last_name');
        $this->assertSame($nice_array, $r->getChoices());

        $this->expectSelect('variables', $nice_array);
        $this->assertSame('select', $r->input($this->renderer));
    }

    public function testSetChoicesDoesNotChangeFloatKeys()
    {
        $r = $this->getRow('var');
        $this->assertSame($r, $r->setChoices(array('0.1' => 'foo', '0.2' => 'bar')));
        $this->assertSame(array('0.1' => 'foo', '0.2' => 'bar'),
        $r->getChoices());
    }

    public function testAddChoices()
    {
        $r = $this->getRow('decision');
        $r->setChoices(array('yes', 'no'));
        $this->assertSame($r, $r->addChoices(array('maybe')));
        $this->assertSame(array('Yes' => 'yes', 'No' => 'no', 'Maybe' => 'maybe'),
        $r->getChoices());
    }

    public function testMultiple()
    {
        $r = $this->getRow('fruits');
        $this->assertSame($r, $r->setMultiple());
        $r->setChoices(array('banana', 'apple', 'orange'));
        $r->setValue(array('apple', 'orange'));
        $this->expectSelect('fruits[]', array('Banana' => 'banana', 'Apple' => 'apple', 'Orange' => 'orange'), array('apple', 'orange'), true);
        $this->assertSame('select', $r->input($this->renderer));
    }

    public function testSubmitForm()
    {
        //this is how a form submission will be flattened
        $values = array(
            'foo' => 'bar',
            'fruits[0]' => 'apple',
            'fruits[1]' => 'orange',
            'baz' => 'bar',
        );
        $r = $this->getRow('baz');
        $r->submitForm($values);
        $this->assertSame('bar', $r->getValue());
    }

    public function testSubmitFormMultiple()
    {
        //this is how a form submission will be flattened
        $values = array(
            'foo' => 'bar',
            'fruits[favourite][0]' => 'apple',
            'fruits[favourite][1]' => 'orange',
            //more than 1 digit indexes
            'fruits[favourite][11]' => 'banana',
            'fruits[favourite][204]' => 'strawberry',
            'baz' => 'bar',
        );
        $r = $this->getRow('fruits[favourite]');
        $r->setMultiple();
        $r->submitForm($values);
        $this->assertSame(array('apple', 'orange', 'banana', 'strawberry'), $r->getValue());
    }

    protected function expectSelect($name, array $values = array(), $selected = null, $multiple = false)
    {
        $this->renderer->expects($this->once())
                       ->method('select')
                       ->with($name, $values, $selected, $multiple)
                       ->will($this->returnValue('select'));
    }

    protected function createRow()
    {
        return new Choice('decision');
    }

    public function testInput()
    {
        $this->expectSelect('decision');
        $this->assertSame('select', $this->row->input($this->renderer));
    }

    public function testInputWithChoices()
    {
        $this->row->setChoices(array('Yes' => 'yes', 'No' => 'no'));
        $this->expectSelect('decision', array('Yes' => 'yes', 'No' => 'no'));
        $this->assertSame('select', $this->row->input($this->renderer));
    }

    public function testInputWithValue()
    {
        $this->row->setValue('yes');
        $this->expectSelect('decision', array(), 'yes');
        $this->assertSame('select', $this->row->input($this->renderer));
    }

    public function testInputWithValueAndChoices()
    {
        $this->row->setChoices(array('Yes' => 'yes', 'No' => 'no'));
        $this->row->setValue('no');
        $this->expectSelect('decision', array('Yes' => 'yes', 'No' => 'no'), 'no');
        $this->assertSame('select', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->row->setChoices(array('yes', 'no'));
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }
}
