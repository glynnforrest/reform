<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\DateTimeRow;

/**
 * DateTimeRowTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeRowTest extends RowTestCase
{
    protected function createRow()
    {
        return new DateTimeRow('datetime');
    }

    public function testInput()
    {
        $this->renderer->expects($this->exactly(2))
                       ->method('input')
                       ->withConsecutive(
                           array('date', 'datetime[date]'),
                           array('time', 'datetime[time]')
                       )
                       ->will($this->returnValue('input'));
        $this->assertSame('inputinput', $this->row->input($this->renderer));
    }

    public function testInputWithValue()
    {
        $datetime = new \DateTime('2003/10/09 12:45');
        $this->row->setValue($datetime);
        $this->renderer->expects($this->exactly(2))
                       ->method('input')
                       ->withConsecutive(
                           array('date', 'datetime[date]', '2003-10-09'),
                           array('time', 'datetime[time]', '12:45')
                       )
                       ->will($this->returnValue('input'));

        $this->assertSame('inputinput', $this->row->input($this->renderer));
    }

    public function testRow()
    {
        $this->expectRow();
        $this->assertSame('row', $this->row->render($this->renderer));
    }

    public function testSubmitChangesValueToDateTime()
    {
        $values = array(
            'datetime[date]' => '2015/06/04',
            'datetime[time]' => '18:25'
        );
        $this->assertNull($this->row->getValue());
        $this->row->submitForm($values);
        $this->assertEquals(new \DateTime('2015-06-04 18:25'), $this->row->getValue());
    }

    public function testSubmitReturnsNull()
    {
        $values = array(
            'time' => ''
        );
        $this->assertNull($this->row->getValue());
        $this->row->submitForm($values);
        $this->assertNull($this->row->getValue());
    }
}
