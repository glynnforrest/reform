<?php

/**
 * Validator
 * @author Glynn Forrest <me@glynnforrest.com>
 */

namespace Reform\Validation;

use Reform\Validation\Rule\AbstractRule;
use Reform\Validation\Rule\Required;

class Validator
{
    protected $rules = array();
    protected $required = array();

    /**
     * Get the validation rules.
     *
     * @return array An array of validation rules.
     */
    public function getRules()
    {
        return $this->rules;
    }

    public function addRule($name, AbstractRule $rule)
    {
        if (!isset($this->rules[$name])) {
            $this->rules[$name] = array();
        }

        $this->rules[$name][] = $rule;

        if ($rule instanceof Required) {
            $this->required[$name] = true;
        }

        return $this;
    }

    protected function doValidation(Result $result, array $input, $name, $rules, $early_exit = false)
    {
        //first check if the value actually exists.
        if (!isset($input[$name])) {
            $result->addError($name, $name . ' is not in input.');

            return $result;
        }
        //now run the rules for this name
        foreach ($rules as $rule) {
            if (!$rule->validate($result, $name, $input[$name], $input) && $early_exit) {
                return $result;
            }
        }

        return $result;
    }

    /**
     * Validate an array of values.
     */
    public function validate(array $input, $early_exit = false)
    {
        $result = new Result($input);
        //add custom messages that have been supplied
        foreach ($this->rules as $name => $rules) {
            $this->doValidation($result, $input, $name, $rules, $early_exit);
        }

        return $result;
    }

    /**
     * Validate an array of values from a form. Unlike validate(),
     * if a value is not submitted, the rules for that value will not
     * be check unless the value has the 'Required' rule. This allows
     * validation on optional fields, but only if they are present.
     */
    public function validateForm(array $input, $early_exit = false)
    {
        $result = new Result($input);
        foreach ($this->rules as $name => $rules) {
            //skip a value if it is not required...
            if (!isset($this->required[$name])) {
                //and not submitted.
                //here 'submitted' means either not existing or failing submitted()
                if (!isset($input[$name]) || !$this->submitted($input[$name])) {
                    continue;
                }
            }
            $this->doValidation($result, $input, $name, $rules, $early_exit);
        }

        return $result;
    }

    /**
     * Check if a value has been submitted. This means not empty or
     * containing only white space.
     */
    protected function submitted($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        if (empty($value) && !is_numeric($value)) {
            return false;
        }

        return true;
    }

}
