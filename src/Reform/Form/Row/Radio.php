<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;

/**
 * Radio
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Radio extends AbstractRow
{

    protected $choices_enabled = true;

    public function input()
    {
        $html = '';
        $i = 0;
        foreach ($this->choices as $label => $choice) {

            //if no label is supplied, guess a sensible version
            if (is_numeric($label)) {
                $label = $this->sensible($choice);
            }

            if ($this->value === $choice) {
                $attributes = array_merge($this->attributes, array('checked'));
            } else {
                $attributes = $this->attributes;
            }

            $attributes = array_merge($attributes, array('id' => $this->name . $i));

            $input = Html::input('radio', $this->name, $choice, $attributes);
            $html .= Html::label($this->name . $i, $label . $input);
            $i++;
        }

        return $html;
    }

    public function render()
    {
        $str = str_replace(':label', $this->label(), $this->row_string);
        $str = str_replace(':error', $this->error(), $str);
        $str = str_replace(':input', $this->input(), $str);

        return $str;
    }

}
