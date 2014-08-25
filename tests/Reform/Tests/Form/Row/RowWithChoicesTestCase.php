<?php

namespace Reform\Tests\Form\Row;

use Reform\Helper\Html;

/**
 * RowWithChoicesTestCase
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class RowWithChoicesTestCase extends RowTestCase
{

    public function testGetAndSetChoices()
    {
        $r = $this->getRow('gender');
        $this->assertSame($r, $r->setChoices(array('male', 'female')));
        $this->assertSame(array('Male' => 'male', 'Female' => 'female'),
        $r->getChoices());

        $this->assertSame($r, $r->setChoices(array()));
        $this->assertSame(array(), $r->getChoices());
    }

    public function testSetChoicesAddsSensibleLabels()
    {
        $r = $this->getRow('variables');
        $this->assertSame($r, $r->setChoices(array('first_name', 'last_name')));

        $nice_array = array('First name' => 'first_name', 'Last name' => 'last_name');
        $this->assertSame($nice_array, $r->getChoices());

        $html = Html::select('variables', $nice_array);
        $this->assertSame($html, $r->input());
    }

    public function testSetChoicesDoesNotChangeFloatKeys()
    {
        $r = $this->getRow('var');
        $this->assertSame($r, $r->setChoices(array('0.1' => 'foo', '0.2' => 'bar')));
        $this->assertSame(array('0.1' => 'foo', '0.2' => 'bar'),
        $r->getChoices());
    }

    public function testAddChoices()
    {
        $r = $this->getRow('decision');
        $r->setChoices(array('yes', 'no'));
        $this->assertSame($r, $r->addChoices(array('maybe')));
        $this->assertSame(array('Yes' => 'yes', 'No' => 'no', 'Maybe' => 'maybe'),
        $r->getChoices());
    }

}
