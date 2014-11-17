<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Submit;

/**
 * SubmitTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubmitTest extends RowTestCase
{
    protected function createRow()
    {
        return new Submit('submit-row');
    }

    public function testInput()
    {
        //Form Row should add a sensible value to the submit button
        $this->expectInput(1, 'submit', 'Submit row');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputValueCanBeOverridden()
    {
        $this->row->setValue('SAVE');
        $this->expectInput(1, 'submit', 'SAVE');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }
}
