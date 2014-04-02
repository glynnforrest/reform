<?php

namespace Reform\Tests\Validation\Rule;

require_once __DIR__ . '/../../../../bootstrap.php';

use Reform\Validation\Rule\Email;

/**
 * EmailTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailTest extends \PHPUnit_Framework_TestCase
{

    public function emailProvider()
    {
        return array(
            array('me@glynnforrest.com', true),
            array('me@glynnforrest@com', false),
        );
    }

    /**
     * @dataProvider emailProvider()
     */
    public function testEmail($email, $pass)
    {
        $rule = new Email();
        $result = $this->getMock('Reform\Validation\Result');
        if ($pass) {
            $this->assertTrue($rule->validate($result, 'email_address', $email));
        } else {
            $this->assertFalse($rule->validate($result, 'email_address', $email));
        }
    }

}