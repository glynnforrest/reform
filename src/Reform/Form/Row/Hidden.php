<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;

/**
 * Hidden
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Hidden extends AbstractRow
{

    public function input()
    {
        return Html::input('hidden', $this->name, $this->value, $this->attributes);
    }

    public function render()
    {
        return $this->input();
    }

}
