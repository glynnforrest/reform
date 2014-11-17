<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Hidden;
use Reform\Helper\Html;

/**
 * HiddenTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HiddenTest extends RowTestCase
{
    protected function createRow()
    {
        return new Hidden('token');
    }

    public function testInput()
    {
        $expected = Html::input('hidden', 'token');
        $this->assertSame($expected, $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $expected = Html::input('hidden', 'token');
        $this->assertSame($expected, $this->row->render($this->renderer));
    }

    public function testRowWithValue()
    {
        $token = '12345';
        $this->row->setValue($token);
        $expected = Html::input('hidden', 'token', $token);
        $this->assertSame($expected, $this->row->render($this->renderer));
    }
}
