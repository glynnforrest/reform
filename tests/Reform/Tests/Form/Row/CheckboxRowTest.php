<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\CheckboxRow;

/**
 * CheckboxRowTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CheckboxRowTest extends RowTestCase
{
    protected function createRow()
    {
        return new CheckboxRow('accept');
    }

    public function testInputUnchecked()
    {
        //the checkbox will always have the value of 'checked'
        $this->expectInput(1, 'checkbox', 'checked');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputChecked()
    {
        $this->row->setValue('some-truthy-value');
        $this->expectInput(1, 'checkbox', 'checked', array('checked'));
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputSetUnchecked()
    {
        $this->row->setValue('some-truthy-value');
        $this->row->setValue(null);
        $this->expectInput(1, 'checkbox', 'checked');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputPlusAddAttributes()
    {
        $this->row->setValue('yes');
        $this->row->addAttributes(array('id' => 'checkbox-id'));
        $this->expectInput(1, 'checkbox', 'checked', array('checked', 'id' => 'checkbox-id'));
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputPlusSetAttributes()
    {
        $this->row->setValue('yes');
        $this->row->setAttributes(array('id' => 'checkbox-id'));
        $this->expectInput(1, 'checkbox', 'checked', array('checked', 'id' => 'checkbox-id'));
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputSetCheckedPreserveAttributes()
    {
        $this->row->setAttributes(array('class' => 'checkbox'));
        $this->assertSame(array('class' => 'checkbox'), $this->row->getAttributes());
        $this->row->setValue('yes');
        $this->assertSame(array('class' => 'checkbox'), $this->row->getAttributes());

        $this->expectInput(1, 'checkbox', 'checked', array('checked', 'class' => 'checkbox'));
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputSetUncheckedPreserveAttributes()
    {
        $this->row->setValue('yes');
        $this->row->setAttributes(array('class' => 'checkbox'));
        $this->assertSame(array('class' => 'checkbox'), $this->row->getAttributes());
        $this->row->setValue(null);
        $this->assertSame(array('class' => 'checkbox'), $this->row->getAttributes());

        $this->expectInput(1, 'checkbox', 'checked', array('class' => 'checkbox'));
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testStillGetCheckboxValueAfterRender()
    {
        $this->row->setValue('yes');
        $this->expectInput(1, 'checkbox', 'checked', array('checked'));
        $this->assertSame('input', $this->row->input($this->renderer));
        $this->assertSame('yes', $this->row->getValue());
    }

    public function testSubmitEmptyForm()
    {
        $this->row->submitForm(array());
        $this->expectInput(1, 'checkbox', 'checked', array());
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testFalse()
    {
        $this->row->setValue(false);
        $this->expectInput(1, 'checkbox', 'checked', array());
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testEmptyString()
    {
        $this->row->setValue('');
        $this->expectInput(1, 'checkbox', 'checked', array());
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }
}
