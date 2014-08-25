<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;

/**
 * Checkbox
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Checkbox extends AbstractRow
{

    public function input()
    {
        if ($this->value !== null) {
            $this->addAttributes(array('checked'));
        }
        //no matter what, the value of the input is 'checked'
        return Html::input('checkbox', $this->name, 'checked', $this->attributes);
    }

    public function render()
    {
        $str = str_replace(':label', $this->label(), $this->row_string);
        $str = str_replace(':error', $this->error(), $str);
        $str = str_replace(':input', $this->input(), $str);

        return $str;
    }

}
