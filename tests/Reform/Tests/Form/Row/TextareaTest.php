<?php

namespace Reform\Tests\Form\Row;

use Reform\Form\Row\Textarea;
use Reform\Helper\Html;

/**
 * TextareaTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextareaTest extends \PHPUnit_Framework_TestCase
{

    protected function getRow($name, $label = null, $attributes = array())
    {
        return new Textarea($name, $label, $attributes);
    }

    public function testInput()
    {
        $r = $this->getRow('name');
        $expected = Html::input('textarea', 'name');
        $this->assertSame($expected, $r->input());
    }

    public function testRow()
    {
        $r = $this->getRow('name');
        $expected = Html::label('name', 'Name');
        $expected .= Html::input('textarea', 'name');
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValue()
    {
        $comment = 'Hello world';
        $r = $this->getRow('comment');
        $r->setValue($comment);
        $expected = Html::label('comment', 'Comment');
        $expected .= Html::input('textarea', 'comment', $comment);
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithError()
    {
        $r = $this->getRow('comment');
        $error = 'Comment is required.';
        $r->setError($error);
        $expected = Html::label('comment', 'Comment');
        $expected .= Html::input('textarea', 'comment');
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

    public function testRowWithValueAndError()
    {
        $comment = 'Hello world';
        $r = $this->getRow('comment');
        $r->setValue($comment);
        $error = 'Comment isn\'t good enough.';
        $r->setError($error);
        $expected = Html::label('comment', 'Comment');
        $expected .= Html::input('textarea', 'comment', $comment);
        $expected .= '<small class="error">' . $error . '</small>';
        $this->assertSame($expected, $r->render());
    }

}
