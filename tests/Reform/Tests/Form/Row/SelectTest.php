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
class SelectTest extends RowWithChoicesTestCase
{

    protected function getRow($name, $label = null, $attributes = array())
    {
        return new Select($name, $label, $attributes);
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

}
