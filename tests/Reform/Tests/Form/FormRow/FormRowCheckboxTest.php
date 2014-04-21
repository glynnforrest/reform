<?php

namespace Reform\Tests\Form\FormRow;

use Reform\Form\FormRow;
use Reform\Helper\Html;

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * FormRowCheckboxTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FormRowCheckboxTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $r = new FormRow('checkbox', 'checkbox');
        $this->assertSame('checkbox', $r->getType());
    }

    public function testInputUnchecked()
    {
        $r = new FormRow('checkbox', 'remember-me');
        //the checkbox will always have the value of 'checked'
        $html = Html::input('checkbox', 'remember-me', 'checked');
        $this->assertSame($html, $r->input());
    }

    public function testInputChecked()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setValue('some-truthy-value');
        $html = Html::input('checkbox', 'remember-me', 'checked', array('checked'));
        $this->assertSame($html, $r->input());
    }

    public function testInputSetChecked()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setValue('some-truthy-value');
        $html = Html::input('checkbox', 'remember-me', 'checked', array('checked'));
        $this->assertSame($html, $r->input());
    }

    public function testInputSetUnchecked()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setValue('some-truthy-value');
        $r->setValue(null);
        $html = Html::input('checkbox', 'remember-me', 'checked');
        $this->assertSame($html, $r->input());
    }

    public function testInputPlusAddAttributes()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setValue('yes');
        $r->addAttributes(array('id' => 'checkbox-id'));
        $html = Html::input('checkbox', 'remember-me', 'checked', array('checked', 'id' => 'checkbox-id'));
        $this->assertSame($html, $r->input());
    }

    public function testInputPlusSetAttributes()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setValue('yes');
        $r->setAttributes(array('id' => 'checkbox-id'));
        $html = Html::input('checkbox', 'remember-me', 'checked', array('checked', 'id' => 'checkbox-id'));
        $this->assertSame($html, $r->input());
    }

    public function testInputSetCheckedPreserveAttributes()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setAttributes(array('class' => 'checkbox'));
        $this->assertSame(array('class' => 'checkbox'), $r->getAttributes());
        $r->setValue('yes');
        $this->assertSame(array('class' => 'checkbox'), $r->getAttributes());

        $html = Html::input('checkbox', 'remember-me', 'checked', array('class' => 'checkbox', 'checked'));
        $this->assertSame($html, $r->input());
    }

    public function testInputSetUncheckedPreserveAttributes()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setValue('yes');
        $r->setAttributes(array('class' => 'checkbox'));
        $this->assertSame(array('class' => 'checkbox'), $r->getAttributes());
        $r->setValue(null);
        $this->assertSame(array('class' => 'checkbox'), $r->getAttributes());

        $html = Html::input('checkbox', 'remember-me', 'checked', array('class' => 'checkbox'));
        $this->assertSame($html, $r->input());
    }

    public function testRow()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $expected = Html::label('remember-me', 'Remember me');
        $expected .= Html::input('checkbox', 'remember-me', 'checked');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setValue('truthy value');
        $expected = Html::label('remember-me', 'Remember me');
        $expected .= Html::input('checkbox', 'remember-me', 'checked', array('checked'));
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = new FormRow('checkbox', 'agree');
        $error = 'You must tick the checkbox.';
        $r->setError($error);
        $expected = Html::label('agree', 'Agree');
        $expected .= Html::input('checkbox', 'agree', 'checked');
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValueAndError()
    {
        $r = new FormRow('checkbox', 'agree');
        $r->setValue('truthy value');
        $error = 'You must tick the checkbox.';
        $r->setError($error);
        $expected = Html::label('agree', 'Agree');
        $expected .= Html::input('checkbox', 'agree', 'checked', array('checked'));
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

    public function testStillGetCheckboxValueAfterRender()
    {
        $r = new FormRow('checkbox', 'remember-me');
        $r->setValue('yes');
        $html = Html::label('remember-me', 'Remember me');
        $html .= Html::input('checkbox', 'remember-me', 'checked', array('checked'));
        $this->assertSame($html, $r->render());
        $this->assertSame('yes', $r->getValue());
    }

}
