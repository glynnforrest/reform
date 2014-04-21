<?php

namespace Reform\Tests\Form\FormRow;

use Reform\Form\FormRow;
use Reform\Helper\Html;

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * FormRowPasswordTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FormRowPasswordTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $r = new FormRow('password', 'password');
        $this->assertSame('password', $r->getType());
    }

    public function testInput()
    {
        $r = new FormRow('password', 'password');
        $expected = Html::input('password', 'password');
        $this->assertSame($expected, $r->input());
    }

    public function testRow()
    {
        $r = new FormRow('password', 'password');
        $expected = Html::label('password', 'Password');
        $expected .= Html::input('password', 'password');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $password = 'hunter2';
        $r = new FormRow('password', 'password');
        $r->setValue($password);
        $expected = Html::label('password', 'Password');
        $expected .= Html::input('password', 'password');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = new FormRow('password', 'password');
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
        $r = new FormRow('password', 'password');
        $r->setValue($password);
        $error = 'Password is incorrect.';
        $r->setError($error);
        $expected = Html::label('password', 'Password');
        $expected .= Html::input('password', 'password');
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

}
