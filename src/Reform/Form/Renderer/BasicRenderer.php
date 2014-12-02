<?php

namespace Reform\Form\Renderer;

use Reform\Form\Row\AbstractRow;
use Reform\Helper\Html;

/**
 * BasicRenderer
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BasicRenderer implements RendererInterface
{
    public function row(AbstractRow $row, $use_label = true)
    {
        $html = $row->input($this);

        if ($use_label) {
            $html = Html::label($row->getName(), $row->getLabel()).$html;
        }

        if ($error = $row->getError($this)) {
            $html .= sprintf('<p>%s</p>', $error);
        }

        return Html::tag('div', $html);
    }

    public function input($type, $name, $value = null, array $attributes = array())
    {
        return Html::input($type, $name, $value, $attributes);
    }

    public function select($name, array $values, $selected = null, $multiple = false, array $attributes = array())
    {
        return Html::select($name, $values, $selected, $multiple, $attributes);
    }
}
