<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Number;

/**
 * NumberTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NumberTest extends RowTestCase
{
    protected function createRow()
    {
        return new Number('num');
    }

    public function testInput()
    {
        $this->expectInput(1, 'number');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputWithValue()
    {
        $num = 9;
        $this->row->setValue($num);
        $this->expectInput(1, 'number', $num);

        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }
}
