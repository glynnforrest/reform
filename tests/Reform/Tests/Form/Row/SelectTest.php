<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Select;
use Reform\Helper\Html;

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * SelectTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SelectTest extends \PHPUnit_Framework_TestCase
{

    protected function getRow($name, $label = null, $attributes = array())
    {
        return new Select($name, $label, $attributes);
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

        $html = Html::select('variables', $nice_array);
        $this->assertSame($html, $r->input());
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

    public function testInput()
    {
        $r = $this->getRow('decision');
        $html = Html::select('decision', array());
        $this->assertSame($html, $r->input());
    }

    public function testInputWithChoices()
    {
        $r = $this->getRow('decision');
        $r->setChoices(array('Yes' => 'yes', 'No' => 'no'));
        $html = Html::select('decision', array('Yes' => 'yes', 'No' => 'no'));
        $this->assertSame($html, $r->input());
    }

    public function testInputWithValue()
    {
        $r = $this->getRow('decision');
        $r->setValue('yes');
        $html = Html::select('decision', array());
        $this->assertSame($html, $r->input());
    }

    public function testInputWithValueAndChoices()
    {
        $r = $this->getRow('decision');
        $r->setChoices(array('Yes' => 'yes', 'No' => 'no'));
        $r->setValue('no');
        $html = Html::select('decision', array('Yes' => 'yes', 'No' => 'no'), 'no');
        $this->assertSame($html, $r->input());
    }

    public function testInputWithStrangeTypes()
    {
        $r = $this->getRow('decision');
        $choices = array(1.1 => 1, 2 => 2, '3' => 3, 4);
        $r->setChoices($choices);
        $html = Html::select('decision', $choices);
        $this->assertSame($html, $r->input());
    }

    public function testRow()
    {
        $r = $this->getRow('decision');
        $r->setChoices(array('yes', 'no'));
        $expected = Html::label('decision', 'Decision');
        $expected .= Html::select('decision', array('Yes' => 'yes', 'No' => 'no'));
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $r = $this->getRow('decision');
        $r->setValue('yes');
        $r->setChoices(array('yes', 'no'));
        $expected = Html::label('decision', 'Decision');
        $expected .= Html::select('decision', array('Yes' => 'yes', 'No' => 'no'), 'yes');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = $this->getRow('decision');
        $error = 'No choice given.';
        $r->setError($error);
        $expected = Html::label('decision', 'Decision');
        $expected .= Html::select('decision', array());
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValueAndError()
    {
        $r = $this->getRow('decision');
        $r->setValue('no');
        $error = 'Bad move, pal.';
        $r->setError($error);
        $r->setChoices(array('yes', 'no'));
        $expected = Html::label('decision', 'Decision');
        $expected .= Html::select('decision', array('Yes' => 'yes', 'No' => 'no'), 'no');
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

    public function testMultiple()
    {
        $r = $this->getRow('fruits');
        $this->assertSame($r, $r->setMultiple());
        $r->setChoices(array('banana', 'apple', 'orange'));
        $r->setValue(array('apple', 'orange'));
        $expected = Html::label('fruits', 'Fruits');
        $expected .= Html::select('fruits[]', array('Banana' => 'banana', 'Apple' => 'apple', 'Orange' => 'orange'), array('apple', 'orange'), true);
        $this->assertSame($expected, $r->render());
    }

    public function testSubmitForm()
    {
        //this is how a form submission will be flattened
        $values = array(
            'foo' => 'bar',
            'fruits[0]' => 'apple',
            'fruits[1]' => 'orange',
            'baz' => 'bar'
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
            'baz' => 'bar'
        );
        $r = $this->getRow('fruits[favourite]');
        $r->setMultiple();
        $r->submitForm($values);
        $this->assertSame(array('apple', 'orange', 'banana', 'strawberry'), $r->getValue());
    }

}
