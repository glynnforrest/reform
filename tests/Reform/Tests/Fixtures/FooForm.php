<?php

namespace Reform\Tests\Fixtures;

use Reform\Form\Form;

/**
 * FooForm
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FooForm extends Form
{
    public function getId()
    {
        return 'foo';
    }
}
