<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Password;
use Reform\Helper\Html;

/**
 * PasswordTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PasswordTest extends \PHPUnit_Framework_TestCase
{

    protected function getRow($name, $label = null, $attributes = array())
    {
        return new Password($name, $label, $attributes);
    }

    public function testInput()
    {
        $r = $this->getRow('password');
        $expected = Html::input('password', 'password');
        $this->assertSame($expected, $r->input());
    }

    public function testRow()
    {
        $r = $this->getRow('password');
        $expected = Html::label('password', 'Password');
        $expected .= Html::input('password', 'password');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $password = 'hunter2';
        $r = $this->getRow('password');
        $r->setValue($password);
        $expected = Html::label('password', 'Password');
        $expected .= Html::input('password', 'password');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = $this->getRow('password');
        $error = 'Password is incorrect.';
        $r->setError($error);
        $expected = Html::label('password', 'Password');
        $expected .= Html::input('password', 'password');
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValueAndError()
    {
        $password = 'super_secret';
        $r = $this->getRow('password');
        $r->setValue($password);
        $error = 'Password is incorrect.';
        $r->setError($error);
        $expected = Html::label('password', 'Password');
        $expected .= Html::input('password', 'password');
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

}
