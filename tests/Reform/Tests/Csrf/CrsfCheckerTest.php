<?php

namespace Reform\Tests\Csrf;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Reform\Csrf\CsrfChecker;
use Reform\Exception\CsrfTokenException;

/**
 * CsrfCheckerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrsfCheckerTest extends \PHPUnit_Framework_TestCase
{
    protected $checker;
    protected $session;

    public function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->checker = new CsrfChecker($this->session);
    }

    protected function setSessionToken($name, $token)
    {
        $this->session->set('reform.csrf.'.$name, $token);
    }

    public function checkProvider()
    {
        return array(
            array('secret_token', 'secret_token', true),
            array('secret_token', 'secret_oken'),
            array(null, 'secret_token'),
            array('secret_token', null),
            array(null, null),
        );
    }

    /**
     * @dataProvider checkProvider()
     */
    public function testCheck($token, $session_token, $pass = false)
    {
        $this->setSessionToken('testing', $session_token);
        if ($pass) {
            $this->assertTrue($this->checker->check('testing', $token));
        } else {
            $this->setExpectedException('\Reform\Exception\CsrfTokenException');
            $this->checker->check('testing', $token);
        }
    }

    public function testTokenIsExpiredAfterSuccess()
    {
        $this->setSessionToken('testing', 'secret');
        $this->assertTrue($this->checker->check('testing', 'secret'));
        //token should have now expired
        $this->setExpectedException('\Reform\Exception\CsrfTokenException');
        $this->checker->check('testing', 'secret');
    }

    public function testTokenIsNotExpiredOnFailure()
    {
        $this->setSessionToken('testing', 'valid_token');

        //catch the exception so we can use assertions in this method
        try {
            $this->checker->check('testing', 'invalid_token');
        } catch (CsrfTokenException $e) {
        }

        $this->assertTrue($this->checker->check('testing', 'valid_token'));
    }

    public function testDifferentTokenIdIsInvalid()
    {
        $msg = 'Invalid CSRF token supplied';
        $this->setExpectedException('\Reform\Exception\CsrfTokenException', $msg);
        $this->checker->check('not-testing', 'token');
    }
}
