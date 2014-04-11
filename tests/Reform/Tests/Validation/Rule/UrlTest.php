<?php

namespace Reform\Tests\Validation\Rule;

require_once __DIR__ . '/../../../../bootstrap.php';

use Reform\Validation\Rule\Url;

/**
 * UrlTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UrlTest extends RuleTest
{

    protected $rule;

    public function setup()
    {
        $this->rule = new Url();
    }

    public function dataProvider()
    {
        return array(
            array('http://example.com', true),
            array('http://www.example.com', true),
            array('ftp://example.com', true),
            array('example.com', false),
            array('www.example.com', false),
        );
    }

}