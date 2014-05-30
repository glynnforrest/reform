<?php

namespace Reform\Validation\Rule;

use Reform\Validation\Rule\Regex;

/**
 * Alpha
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Alpha extends Regex
{

    protected $message = ':name can only contain letters.';
    protected $pattern = '/^[\pL]+$/u';

    public function __construct($message = null)
    {
        if ($message) {
            $this->message = $message;
        }
    }

}
