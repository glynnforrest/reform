<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;

/**
 * Text
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Text extends AbstractRow
{

    public function input()
    {
        return Html::input('text', $this->name, $this->value, $this->attributes);
    }

    public function render()
    {
        $str = str_replace(':label', $this->label(), $this->row_string);
        $str = str_replace(':error', $this->error(), $str);
        $str = str_replace(':input', $this->input(), $str);

        return $str;
    }

}