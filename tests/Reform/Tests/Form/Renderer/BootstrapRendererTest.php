<?php

namespace Reform\Tests\Form\Renderer;

use Reform\Form\Renderer\BootstrapRenderer;

/**
 * BootstrapRendererTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BootstrapRendererTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->renderer = new BootstrapRenderer();
    }

    public function testTextInput()
    {
        $expected = '<input type="text" id="username" name="username" value="" class="form-control" />';
        $this->assertSame($expected, $this->renderer->input('text', 'username'));
    }

    public function testTextInputWithValue()
    {
        $expected = '<input type="text" id="username" name="username" value="glynn" class="form-control" />';
        $this->assertSame($expected, $this->renderer->input('text', 'username', 'glynn'));
    }

    public function testTextInputWithAttributes()
    {
        $expected = '<input type="text" id="username" name="username" value="glynn" data-foo="bar" class="form-control" />';
        $this->assertSame($expected, $this->renderer->input('text', 'username', 'glynn', array('data-foo' => 'bar')));
    }

    public function testTextInputWithSuppliedClass()
    {
        $expected = '<input type="text" id="username" name="username" value="glynn" class="foo bar form-control" />';
        $this->assertSame($expected, $this->renderer->input('text', 'username', 'glynn', array('class' => 'foo bar')));
    }

    public function testSubmitWithSuppliedClass()
    {
        $expected = '<input type="submit" id="save" name="save" value="SAVE" class="foo bar btn btn-primary" />';
        $this->assertSame($expected, $this->renderer->input('submit', 'save', 'SAVE', array('class' => 'foo bar')));
    }
}
