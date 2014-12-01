<?php

namespace Reform\Exception;

use Blockade\Exception\BlockadeException;

/**
 * CsrfTokenException is thrown when the client supplies an incorrect
 * CSRF token, or fails to supply one at all.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CsrfTokenException extends BlockadeException
{
    protected $message = 'Invalid CSRF token supplied';
    protected $code = 403;
}
