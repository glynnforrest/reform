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

    public function input()
    {
        return Html::select($this->name, $this->choices, $this->value, $this->attributes);
    }

    public function render()
    {
        $str = str_replace(':label', $this->label(), $this->row_string);
        $str = str_replace(':error', $this->error(), $str);
        $str = str_replace(':input', $this->input(), $str);

        return $str;
    }

}
