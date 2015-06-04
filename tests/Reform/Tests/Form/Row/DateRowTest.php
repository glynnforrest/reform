<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\DateRow;

/**
 * DateRowTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateRowTest extends RowTestCase
{
    protected function createRow()
    {
        return new DateRow('birthday');
    }

    public function testInput()
    {
        $this->expectInput(1, 'date');
        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testInputWithValue()
    {
        $date = new \DateTime('2003/10/09');
        $this->row->setValue($date);
        $this->expectInput(1, 'date', '2003-10-09');

        $this->assertSame('input', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }

    public function testSubmitChangesValueToDateTime()
    {
        $values = array(
            'birthday' => '2015/06/04'
        );
        $this->assertNull($this->row->getValue());
        $this->row->submitForm($values);
        $this->assertEquals(new \DateTime('2015/06/04'), $this->row->getValue());
    }
}
