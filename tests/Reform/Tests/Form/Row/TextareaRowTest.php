<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\TextareaRow;

/**
 * TextareaRowTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextareaRowTest extends RowTestCase
{
    protected function createRow()
    {
        return new TextareaRow('comment');
    }

    public function testInput()
    {
        $this->expectInput(1, 'textarea');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputWithValue()
    {
        $comment = 'Hello world';
        $this->row->setValue($comment);
        $this->expectInput(1, 'textarea', $comment);

        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }
}
