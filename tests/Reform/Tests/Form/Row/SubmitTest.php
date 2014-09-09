<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Submit;
use Reform\Helper\Html;

/**
 * SubmitTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubmitTest extends \PHPUnit_Framework_TestCase
{

    protected function getRow($name, $label = null, $attributes = array())
    {
        return new Submit($name, $label, $attributes);
    }

    public function testInput()
    {
        $r = $this->getRow('submit-form');
        //Form Row should add a sensible title to the submit button
        $expected = Html::input('submit', 'submit-form', 'Submit form');
        $this->assertSame($expected, $r->input());
    }

    public function testInputValueCanBeOverridden()
    {
        $r = $this->getRow('submit-form');
        $r->setValue('SAVE');
        $expected = Html::input('submit', 'submit-form', 'SAVE');
        $this->assertSame($expected, $r->input());
    }

    public function testRow()
    {
        $r = $this->getRow('_save');
        $expected = Html::input('submit', '_save', 'Save');
        //update this after row_html is implemented
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $r = $this->getRow('_save');
        $r->setValue('GO');
        $expected = Html::input('submit', '_save', 'GO');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = $this->getRow('_save');
        $r->setError('An error occurred.');
        $expected = Html::input('submit', '_save', 'Save');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValueAndError()
    {
        $r = $this->getRow('_save');
        $r->setValue('SEND');
        $r->setError('An error occurred.');
        $expected = Html::input('submit', '_save', 'SEND');
        $this->assertSame($expected, $r->render());
    }

}
