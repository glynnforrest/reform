<?php

namespace Reform\Validation\Rule;

use Reform\Validation\Rule\AbstractRule;
use Reform\Validation\Result;

/**
 * Url
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Url extends AbstractRule
{

    protected $message = ':name must be a valid url.';

    public function __construct($message = null)
    {
        if ($message) {
            $this->message = $message;
        }
    }

    public function validate(Result $result, $name, $value, array $input = array())
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return $this->fail($result, $name, $value);
        }

        return true;
    }

}
