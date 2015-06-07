<?php

namespace Reform\Validation\Rule;

use Reform\Validation\Result;

/**
 * AbstractRule
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractRule
{

    protected $message = ':name is invalid.';

    abstract public function validate(Result $result, $name, $value, array $input = array());

    /**
     * Add $this->message to $result, automatically substituting
     * $name, $value and any additional parameters supplied in
     * $context.
     */
    protected function fail(Result $result, $name, $value = null, array $context = array())
    {
        if (!isset($context[':name'])) {
            $context[':name'] = $this->sensible($name);
        }

        if (is_scalar($value) && !isset($context[':value'])) {
            $context[':value'] = (string) $value;
        }

        $message = str_replace(array_keys($context), array_values($context), $this->message);
        $result->addError($name, $message);

        return false;
    }

    /**
     * Create a sensible, human readable representation for $name.
     *
     * @param string $name the name to transform
     */
    protected function sensible($name)
    {
        $name = preg_replace('`([A-Z])`', '-\1', $name);
        $name = str_replace(array('-', '_'), ' ', $name);

        return ucfirst(trim(strtolower($name)));
    }

}
