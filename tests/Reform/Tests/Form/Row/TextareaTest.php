<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Textarea;

/**
 * TextareaTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextareaTest extends RowTestCase
{
    protected function createRow()
    {
        return new Textarea('comment');
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
