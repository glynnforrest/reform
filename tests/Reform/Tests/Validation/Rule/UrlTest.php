<?php

namespace Reform\Tests\Validation\Rule;

require_once __DIR__ . '/../../../../bootstrap.php';

use Reform\Validation\Rule\Url;

/**
 * UrlTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UrlTest extends \PHPUnit_Framework_TestCase
{

    public function urlProvider()
    {
        return array(
            array('http://example.com', true),
            array('http://www.example.com', true),
            array('ftp://example.com', true),
            array('example.com', false),
            array('www.example.com', false),
        );
    }

    /**
     * @dataProvider urlProvider()
     */
    public function testUrl($email, $pass)
    {
        $rule = new Url();
        $result = $this->getMock('Reform\Validation\Result');
        if ($pass) {
            $this->assertTrue($rule->validate($result, 'email_address', $email));
        } else {
            $this->assertFalse($rule->validate($result, 'email_address', $email));
        }
    }

}