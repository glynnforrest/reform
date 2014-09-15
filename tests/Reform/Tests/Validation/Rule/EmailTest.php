<?php

namespace Reform\Tests\Validation\Rule;

use Reform\Validation\Rule\Email;

/**
 * EmailTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailTest extends RuleTest
{

    protected $rule;

    public function setup()
    {
        $this->rule = new Email();
    }

    public function dataProvider()
    {
        return array(
            array('me@glynnforrest.com', true),
            array('me@glynnforrest@com', false),
        );
    }

}