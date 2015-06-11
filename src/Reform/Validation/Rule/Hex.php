<?php

namespace Reform\Validation\Rule;

/**
 * Hex
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Hex extends Regex
{
    protected $message = ':name must be hexadecimal';
    protected $pattern = '`^[0-9a-fA-F]+$`';

    public function __construct($message = null)
    {
        if ($message) {
            $this->message = $message;
        }
    }
}
