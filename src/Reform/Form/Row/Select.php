<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;

/**
 * Select
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Select extends AbstractRow
{

    protected $choices = array();
    protected $multiple;

    /**
     * Allow this row to accept multiple choices.
     *
     * @param bool $multiple Allow or disallow multiple choices
     */
    public function setMultiple($multiple = true)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Set the choices for this row. Keys will be created
     * automatically if no keys are given or, due to PHP's array
     * implementation, keys are strings containing valid integers.
     *
     * @param array $choices An array of keys and values to use for choices
     */
    public function setChoices(array $choices)
    {
        $this->choices = array();
        $this->addChoices($choices);

        return $this;
    }

    /**
     * Add to the choices for this row. Keys will be created
     * automatically if no keys are given or, due to PHP's array
     * implementation, keys are strings containing valid integers.
     *
     * @param array $choices An array of keys and values to use for choices
     */
    public function addChoices(array $choices)
    {
        foreach ($choices as $k => $v) {
            if (is_int($k)) {
                $k = $this->sensible($v);
            }
            $this->choices[$k] = $v;
        }

        return $this;
    }

    /**
     * Get the choices for this row.
     */
    public function getChoices()
    {
        return $this->choices;
    }

    public function submitForm(array $values)
    {
        if (!$this->multiple) {
            $this->value = isset($values[$this->name]) ? $values[$this->name] : null;

            return;
        }

        //expecting multiple values
        //the array has been flattened, so take the keys that match
        //$this->name[\d+]
        $name = str_replace('[', '\[', $this->name);
        $name = str_replace(']', '\]', $name);
        $pattern = sprintf('`%s\[\d+\]`', $name);

        $keys = array_filter(array_keys($values), function ($key) use ($pattern) {
            return preg_match($pattern, $key);
        });
        $this->value = array_values(array_intersect_key($values, array_flip($keys)));
    }

    public function input()
    {
        $name = $this->multiple ? $this->name . '[]' : $this->name;

        return Html::select($name, $this->choices, $this->value, $this->multiple, $this->attributes);
    }

    public function render()
    {
        $str = str_replace(':label', $this->label(), $this->row_string);
        $str = str_replace(':error', $this->error(), $str);
        $str = str_replace(':input', $this->input(), $str);

        return $str;
    }

}
