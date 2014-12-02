<?php

namespace Reform\Form\Renderer;

use Reform\Form\Row\AbstractRow;
use Reform\Helper\Html;

/**
 * FoundationRenderer
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FoundationRenderer implements RendererInterface
{
    public function row(AbstractRow $row, $use_label = true)
    {
        $html = $row->input($this);

        $error = $row->getError($this);

        if ($use_label) {
            $attr = $error ? array('class' => 'error') : array();
            $html = Html::label($row->getName(), $row->getLabel().$html, $attr);
        }

        if ($error) {
            $html .= sprintf('<small class="error">%s</small>', $error);
        }

        return Html::tag('div', $html);
    }

    public function input($type, $name, $value = null, array $attributes = array())
    {
        if ($type === 'submit') {
            $attributes['class'] = 'button';
        }

        return Html::input($type, $name, $value, $attributes);
    }

    public function select($name, array $values, $selected = null, $multiple = false, array $attributes = array())
    {
        return Html::select($name, $values, $selected, $multiple, $attributes);
    }
}
