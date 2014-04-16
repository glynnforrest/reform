<?php

namespace Reform\Validation\Rule;

use Reform\Validation\Result;

/**
 * Length
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Length extends AbstractRule
{

    protected $message = ':name must be between :min and :max characters long.';
    protected $minimum_message = ':name must be at least :min characters long.';
    protected $min;
    protected $max;

    public function __construct($min, $max = null, $message = null)
    {
        $this->min = $min;
        $this->max = $max;
        if ($message) {
            $this->message = $message;
        } elseif (!$this->max) {
            $this->message = $this->minimum_message;
        }
    }

    public function validate(Result $result, $name, $value, array $input = array())
    {
        if (!is_scalar($value)) {
            return $this->fail($result, $name, $value, array(
                ':min' => $this->min,
                ':max' => $this->max));
        }

        $length = mb_strlen((string) $value);

        if ($this->max && $length > $this->max) {
            return $this->fail($result, $name, $value, array(
                ':min' => $this->min,
                ':max' => $this->max));
        }

        if ($length < $this->min) {
            return $this->fail($result, $name, $value, array(
                ':min' => $this->min,
                ':max' => $this->max));
        }

        return true;
    }

}
