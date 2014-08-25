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

    protected $choices_enabled = true;

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
