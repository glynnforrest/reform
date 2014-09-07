<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Hidden;
use Reform\Helper\Html;

/**
 * HiddenTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HiddenTest extends \PHPUnit_Framework_TestCase
{

    protected function getRow($name, $label = null, $attributes = array())
    {
        return new Hidden($name, $label, $attributes);
    }

    public function testInput()
    {
        $r = $this->getRow('token');
        $expected = Html::input('hidden', 'token');
        $this->assertSame($expected, $r->input());
    }

    public function testRow()
    {
        $r = $this->getRow('token');
        $expected = Html::input('hidden', 'token');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $token = '12345';
        $r = $this->getRow('token');
        $r->setValue($token);
        $expected = Html::input('hidden', 'token', $token);
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = $this->getRow('token');
        $r->setError('Token is invalid.');
        $expected = Html::input('hidden', 'token');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValueAndError()
    {
        $token = '123456789';
        $r = $this->getRow('token');
        $r->setValue($token);
        $r->setError('Token is invalid');
        $expected = Html::input('hidden', 'token', $token);
        $this->assertSame($expected, $r->render());
    }

}
