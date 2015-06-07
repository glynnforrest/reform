<?php

namespace Reform\Validation\Rule;

use Reform\Validation\Result;

/**
 * Before
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Before extends AbstractRule
{
    protected $datetime;
    protected $message = ':name must be before :date';

    public function __construct(\DateTime $datetime = null)
    {
        $this->datetime = $datetime ? $datetime : new \DateTime();
    }

    public function validate(Result $result, $name, $value, array $input = array())
    {
        if ($value instanceof \DateTime && $value < $this->datetime) {
            return true;
        }

        return $this->fail($result, $name, $value, array(
            ':date' => $this->datetime->format('F j, Y')
        ));
    }
}
