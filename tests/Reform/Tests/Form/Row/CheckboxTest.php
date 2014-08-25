<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Checkbox;
use Reform\Helper\Html;

/**
 * CheckboxTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CheckboxTest extends RowTestCase
{

    protected function getRow($name, $label = null, $attributes = array())
    {
        return new Checkbox($name, $label, $attributes);
    }

    public function testInputUnchecked()
    {
        $r = $this->getRow('remember-me');
        //the checkbox will always have the value of 'checked'
        $html = Html::input('checkbox', 'remember-me', 'checked');
        $this->assertSame($html, $r->input());
    }

    public function testInputChecked()
    {
        $r = $this->getRow('remember-me');
        $r->setValue('some-truthy-value');
        $html = Html::input('checkbox', 'remember-me', 'checked', array('checked'));
        $this->assertSame($html, $r->input());
    }

    public function testInputSetChecked()
    {
        $r = $this->getRow('remember-me');
        $r->setValue('some-truthy-value');
        $html = Html::input('checkbox', 'remember-me', 'checked', array('checked'));
        $this->assertSame($html, $r->input());
    }

    public function testInputSetUnchecked()
    {
        $r = $this->getRow('remember-me');
        $r->setValue('some-truthy-value');
        $r->setValue(null);
        $html = Html::input('checkbox', 'remember-me', 'checked');
        $this->assertSame($html, $r->input());
    }

    public function testInputPlusAddAttributes()
    {
        $r = $this->getRow('remember-me');
        $r->setValue('yes');
        $r->addAttributes(array('id' => 'checkbox-id'));
        $html = Html::input('checkbox', 'remember-me', 'checked', array('checked', 'id' => 'checkbox-id'));
        $this->assertSame($html, $r->input());
    }

    public function testInputPlusSetAttributes()
    {
        $r = $this->getRow('remember-me');
        $r->setValue('yes');
        $r->setAttributes(array('id' => 'checkbox-id'));
        $html = Html::input('checkbox', 'remember-me', 'checked', array('checked', 'id' => 'checkbox-id'));
        $this->assertSame($html, $r->input());
    }

    public function testInputSetCheckedPreserveAttributes()
    {
        $r = $this->getRow('remember-me');
        $r->setAttributes(array('class' => 'checkbox'));
        $this->assertSame(array('class' => 'checkbox'), $r->getAttributes());
        $r->setValue('yes');
        $this->assertSame(array('class' => 'checkbox'), $r->getAttributes());

        $html = Html::input('checkbox', 'remember-me', 'checked', array('class' => 'checkbox', 'checked'));
        $this->assertSame($html, $r->input());
    }

    public function testInputSetUncheckedPreserveAttributes()
    {
        $r = $this->getRow('remember-me');
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
        $r = $this->getRow('remember-me');
        $expected = Html::label('remember-me', 'Remember me');
        $expected .= Html::input('checkbox', 'remember-me', 'checked');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $r = $this->getRow('remember-me');
        $r->setValue('truthy value');
        $expected = Html::label('remember-me', 'Remember me');
        $expected .= Html::input('checkbox', 'remember-me', 'checked', array('checked'));
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = $this->getRow('agree');
        $error = 'You must tick the checkbox.';
        $r->setError($error);
        $expected = Html::label('agree', 'Agree');
        $expected .= Html::input('checkbox', 'agree', 'checked');
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValueAndError()
    {
        $r = $this->getRow('agree');
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
        $r = $this->getRow('remember-me');
        $r->setValue('yes');
        $html = Html::label('remember-me', 'Remember me');
        $html .= Html::input('checkbox', 'remember-me', 'checked', array('checked'));
        $this->assertSame($html, $r->render());
        $this->assertSame('yes', $r->getValue());
    }

}
