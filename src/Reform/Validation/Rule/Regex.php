<?php

namespace Reform\Validation\Rule;

use Reform\Validation\Rule\AbstractRule;
use Reform\Validation\Result;

/**
 * Regex
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Regex extends AbstractRule
{

    protected $message = ':name contains invalid characters.';
    protected $pattern;

    public function __construct($pattern, $message = null)
    {
        $this->pattern = $pattern;
        if ($message) {
            $this->message = $message;
        }
    }

    public function validate(Result $result, $name, $value, array $input = array())
    {
        if (is_scalar($value) && preg_match($this->pattern, (string) $value)) {
            return true;
        }

        return $this->fail($result, $name, $value);
    }

}
