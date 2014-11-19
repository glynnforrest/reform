<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Honeypot;

/**
 * HoneypotTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotTest extends RowTestCase
{
    protected $input_html = '<input type="text" id="honeypot" name="honeypot" value="" style="display: none;" />';
    protected $input_visible_html = '<input type="text" id="honeypot" name="honeypot" value="" />';

    protected function createRow()
    {
        return new Honeypot('honeypot');
    }

    public function testInput()
    {
        $this->assertSame($this->input_html, $this->row->input($this->renderer));
    }

    public function testInputVisible()
    {
        $this->row->setVisible();
        $this->assertSame($this->input_visible_html, $this->row->input($this->renderer));
    }

    public function testInputWithValue()
    {
        $this->row->setValue('Dumb spammer');
        $input =  '<input type="text" id="honeypot" name="honeypot" value="Dumb spammer" style="display: none;" />';
        $this->assertSame($input, $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $html = '<label for="honeypot" id="honeypot" style="display: none;">Honeypot</label>';
        $html .= $this->input_html;
        $this->assertSame($html, $this->row->render($this->renderer));
    }

    public function testRowVisible()
    {
        $this->row->setVisible();
        $html = '<label for="honeypot" id="honeypot">Honeypot</label>';
        $html .= $this->input_visible_html;
        $this->assertSame($html, $this->row->render($this->renderer));
    }
}
