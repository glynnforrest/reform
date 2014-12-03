<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;
use Reform\Helper\Html;

/**
 * Choice
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Choice extends AbstractRow
{
    protected $choices = array();
    protected $multiple;
    protected $divided;

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
     * Set this row to render checkboxes and radios.
     *
     * @param bool $divided Allow or disallow divided inputs
     */
    public function setDivided($divided = true)
    {
        $this->divided = $divided;

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

    public function input(RendererInterface $renderer)
    {
        $name = $this->multiple ? $this->name.'[]' : $this->name;

        if (!$this->divided) {
            return $renderer->select($name, $this->choices, $this->value, $this->multiple, $this->attributes);
        }

        $input_type = $this->multiple ? 'checkbox' : 'radio';

        $html = '';
        $i = 0;
        foreach ($this->choices as $label => $choice) {

            //if no label is supplied, guess a sensible version
            if (is_numeric($label)) {
                $label = $this->sensible($choice);
            }

            //this comparison and in_array do not check types intentionally
            if ($this->value == $choice) {
                $attributes = array_merge($this->attributes, array('checked'));
            } elseif (is_array($this->value) && in_array($choice, $this->value)){
                $attributes = array_merge($this->attributes, array('checked'));
            } else {
                $attributes = $this->attributes;
            }

            $attributes = array_merge($attributes, array('id' => $this->name . $i));

            $input = $renderer->input($input_type, $name, $choice, $attributes);
            // $html .= Html::label($this->name . $i, $label . $input);
            $html .= $input . $label;
            $i++;
        }

        return $html;
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this);
    }
}
