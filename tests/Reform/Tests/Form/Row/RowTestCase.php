<?php

namespace Reform\Tests\Form\Row;

/**
 * RowTestCase
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class RowTestCase extends \PHPUnit_Framework_TestCase
{
    protected $row;
    protected $renderer;

    public function setup()
    {
        $this->renderer = $this->getMock('Reform\Form\Renderer\RendererInterface');
        $this->row = $this->createRow();
    }

    abstract protected function createRow();

    protected function expectInput($amount, $type, $value = null, $attributes = array())
    {
        $this->renderer->expects($this->exactly($amount))
                       ->method('input')
                       ->with($type, $this->row->getName(), $value, $attributes)
                       ->will($this->returnValue('input'));
    }

    protected function expectRow()
    {
        $this->renderer->expects($this->once())
                       ->method('row')
                       ->with($this->row)
                       ->will($this->returnValue('row'));
    }
}
