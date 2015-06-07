<?php

namespace Reform\Tests\Validation\Rule;

use Reform\Validation\Rule\After;

/**
 * AfterTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AfterTest extends RuleTest
{
    protected $rule;

    public function setup()
    {
        $this->rule = new After(new \DateTime('2000-01-01'));
    }

    public function dataProvider()
    {
        return array(
            array(0, false),
            array('1999-12-31', false),
            array(new \DateTime('2001-01-01'), true),
            array(new \DateTime('2000-02-01'), true),
            array(new \DateTime('2000-01-02'), true),
            array(new \DateTime('2000-01-01'), false),
            array(new \DateTime('1999-12-31'), false),
            array(new \DateTime('1999-01-31'), false),
        );
    }
}
