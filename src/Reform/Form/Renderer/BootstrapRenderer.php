<?php

namespace Reform\Form\Renderer;

use Reform\Form\Row\AbstractRow;
use Reform\Helper\Html;

/**
 * BootstrapRenderer
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BootstrapRenderer implements RendererInterface
{
    public function row(AbstractRow $row, $use_label = true)
    {
        $html = $row->input($this);

        if ($use_label) {
            $html = Html::label($row->getName(), $row->getLabel()).$html;
        }

        $error = $row->getError($this);
        if ($error) {
            $html .= sprintf('<p class="help-block">%s</p>', $error);

            return Html::tag('div', $html, array('class' => 'form-group has-error'));
        }

        return Html::tag('div', $html, array('class' => 'form-group'));
    }

    public function input($type, $name, $value = null, array $attributes = array())
    {
        if ($type === 'submit') {
            //check for a btn (or button-type for compat with others?)
            //attribute, apply if it's set (primary, success, danger)
            //and unset from attributes
            $attributes['class'] = 'btn btn-primary';
        } else {
            $attributes['class'] = 'form-control';
        }

        return Html::input($type, $name, $value, $attributes);
    }

    public function select($name, array $values, $selected = null, $multiple = false, array $attributes = array())
    {
        $attributes['class'] = 'form-control';

        return Html::select($name, $values, $selected, $multiple, $attributes);
    }
}
