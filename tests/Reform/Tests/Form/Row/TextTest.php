<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Text;

/**
 * TextTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextTest extends RowTestCase
{
    protected function createRow()
    {
        return new Text('email');
    }

    public function testInput()
    {
        $this->expectInput(1, 'text');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputWithValue()
    {
        $email = 'me@glynnforrest.com';
        $this->row->setValue($email);
        $this->expectInput(1, 'text', $email);

        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }
}
