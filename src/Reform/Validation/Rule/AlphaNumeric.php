<?php

namespace Reform\Validation\Rule;

use Reform\Validation\Rule\Regex;
use Reform\Validation\Result;

/**
 * AlphaNumeric
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AlphaNumeric extends Regex
{

    protected $message = ':name must be alphanumeric.';
    protected $pattern = '/^[\pL\pN]+$/u';

    public function __construct($message = null)
    {
        if ($message) {
            $this->message = $message;
        }
    }

}
