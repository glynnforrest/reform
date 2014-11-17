<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;
use Reform\Validation\Rule\AbstractRule;
use Reform\Exception\BuildValidationException;
use Reform\Form\Renderer\RendererInterface;

/**
 * AbstractRow
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractRow
{

    protected $name;
    protected $value;

    protected $attributes;
    protected $label;
    protected $rules = array();
    protected $rules_enabled = true;
    protected $error;

    public function __construct($name, $label = null, $attributes = array())
    {
        $this->name = $name;
        $this->label = $label ? $label : $this->sensible($name);
        $this->attributes = $attributes;
    }

    /**
     * Get the name of this row.
     *
     * @return string The name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Create a sensible, human readable default for $string,
     * e.g. creating a label for the name of form inputs.
     *
     * @param string $string the string to transform
     */
    protected function sensible($string)
    {
        $string = preg_replace('`([A-Z])`', '-\1', $string);
        $string = str_replace(array('-', '_'), ' ', $string);

        return ucfirst(trim(strtolower($string)));
    }

    /**
     * Set the error message attached to this FormRow.
     *
     * @param string $error The error message.
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get the error message attached to this FormRow.
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the label text attached to this FormRow.
     *
     * @param string $label The label.
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the label text attached to this FormRow.
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of the input attached to this FormRow.
     *
     * @param string $value The value.
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of the input attached to this FormRow.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the html attributes of the input attached to this FormRow. All
     * previous attributes will be reset.
     *
     * @param array $attributes An array of keys and values
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Add to the html attributes of the input attached to this FormRow.
     *
     * @param array $attributes An array of keys and values
     */
    public function addAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * Get the html attributes of the input attached to this FormRow.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Prevent any additional validation rules from being added to
     * this row. This is used internally by the Form class when
     * creating the Validator.
     */
    public function disableRules()
    {
        $this->rules_enabled = false;
    }

    /**
     * Ensure that validation rules are allowed to be added.
     */
    protected function ensureRulesEnabled()
    {
        if (!$this->rules_enabled) {
            throw new BuildValidationException("Adding rules is forbidden, validation has already been prepared");
        }
    }

    /**
     * Set the assigned validation rules.
     *
     * @param array $rules The validation rules
     */
    public function setRules(array $rules)
    {
        $this->ensureRulesEnabled();
        $this->rules = $rules;

        return $this;
    }

    /**
     * Assign a validation rule.
     *
     * @param AbstractRule $rule The validation rule
     */
    public function addRule(AbstractRule $rule)
    {
        $this->ensureRulesEnabled();
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * Return the assigned validation rules.
     *
     * @return array An array of rules
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Pass in submitted values to allow the row to assign any values
     * that are required.
     *
     * @param array $values The values
     */
    public function submitForm(array $values)
    {
        $this->value = isset($values[$this->name]) ? $values[$this->name] : null;
    }

    abstract public function input(RendererInterface $renderer);

    abstract public function render(RendererInterface $renderer);

}
