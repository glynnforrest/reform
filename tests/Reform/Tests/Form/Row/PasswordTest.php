<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Password;

/**
 * PasswordTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PasswordTest extends RowTestCase
{
    protected function createRow()
    {
        return new Password('email');
    }

    public function testInput()
    {
        $this->expectInput(1, 'password');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputWithValue()
    {
        $password = 's3cr3t';
        $this->row->setValue($password);
        $this->expectInput(1, 'password', null);

        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }
}
