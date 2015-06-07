<?php

namespace Reform\Validation\Rule;

use Reform\Validation\Result;

/**
 * After
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class After extends AbstractRule
{
    protected $datetime;

    public function __construct(\DateTime $datetime = null)
    {
        $this->datetime = $datetime ? $datetime : new \DateTime();
    }

    public function validate(Result $result, $name, $value, array $input = array())
    {
        if ($value instanceof \DateTime && $value > $this->datetime) {
            return true;
        }

        return $this->fail($result, $name, $value);
    }
}
