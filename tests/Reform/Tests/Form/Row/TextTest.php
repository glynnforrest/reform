<?php

namespace Reform\Tests\Form\FormRow;

use Reform\Form\Row\Text;
use Reform\Helper\Html;

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * TextTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextTest extends \PHPUnit_Framework_TestCase
{

    public function testInput()
    {
        $r = new Text('name');
        $expected = Html::input('text', 'name');
        $this->assertSame($expected, $r->input());
    }

    public function testRow()
    {
        $r = new Text('name');
        $expected = Html::label('name', 'Name');
        $expected .= Html::input('text', 'name');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $email = 'test@example.com';
        $r = new Text('email');
        $r->setValue($email);
        $expected = Html::label('email', 'Email');
        $expected .= Html::input('text', 'email', $email);
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = new Text('email');
        $error = 'Email is incorrect.';
        $r->setError($error);
        $expected = Html::label('email', 'Email');
        $expected .= Html::input('text', 'email');
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValueAndError()
    {
        $email = 'foo_bar';
        $r = new Text('email');
        $r->setValue($email);
        $error = 'Email is invalid.';
        $r->setError($error);
        $expected = Html::label('email', 'Email');
        $expected .= Html::input('text', 'email', $email);
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

}
