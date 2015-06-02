<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\NumberRow;

/**
 * NumberRowTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NumberRowTest extends RowTestCase
{
    protected function createRow()
    {
        return new NumberRow('num');
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
