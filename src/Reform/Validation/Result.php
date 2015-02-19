<?php

namespace Reform\Validation;

/**
 * Result
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Result
{

    protected $input;
    protected $errors = array();

    public function __construct(array $input = array())
    {
        $this->input = $input;
    }

    public function get($key)
    {
        return isset($this->input[$key]) ? $this->input[$key] : null;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function getValues()
    {
        return $this->input;
    }

    public function isValid()
    {
        return true === empty($this->errors);
    }

    public function addError($name, $message)
    {
        $this->errors[$name][] = $message;
    }

    /**
     * Get the errors for value $name. If $name is not supplied, all
     * errors will be returned.
     */
    public function getErrors($name = null)
    {
        if (!$name) {
            $errors = array();
            //flatten the errors array into a single array
            array_walk_recursive($this->errors, function ($error) use (&$errors) {
                    $errors[] = $error;
                }
            );

            return $errors;
        }

        return isset($this->errors[$name]) ? $this->errors[$name] : array();
    }

    /**
     * Get the first error found for $name. If $name is not supplied,
     * the very first error is returned.
     */
    public function getFirstError($name = null)
    {
        if (!$name) {
            $name = key($this->errors);
        }

        if (!isset($this->errors[$name])) {
            return;
        }

        return current($this->errors[$name]);
    }

    /**
     * Get the first error for each value.
     */
    public function getFirstErrors()
    {
        $errors = array();
        foreach (array_keys($this->errors) as $name) {
            $errors[$name] = current($this->errors[$name]);
        }

        return $errors;
    }

    /**
     * Check if this result has any errors. If $name is supplied, only
     * check for errors on that row. Otherwise, check all rows.
     *
     * @param  null|string $name The name of the row, if supplied
     * @return bool
     */
    public function hasErrors($name = null)
    {
        if (!$name) {
            return !empty($this->errors);
        }

        return isset($this->errors[$name]);
    }
}
