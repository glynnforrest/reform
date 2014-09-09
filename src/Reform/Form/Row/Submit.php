<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;

/**
 * Submit
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Submit extends AbstractRow
{

    public function input()
    {
        //add a value to the submit button if there is none
        if ($this->value === null) {
            $this->value = $this->sensible($this->name);
        }
        return Html::input('submit', $this->name, $this->value, $this->attributes);
    }

    public function render()
    {
        $str = str_replace(':error', '', $this->row_string);
        $str = str_replace(':label', '', $str);
        $str = str_replace(':input', $this->input(), $str);

        return $str;
    }

}
