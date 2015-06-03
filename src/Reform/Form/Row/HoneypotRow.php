<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;
use Reform\Form\Renderer\RendererInterface;

/**
 * HoneypotRow
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotRow extends AbstractRow
{
    protected $visible;

    /**
     * Do not hide the honeypot field and label with css.
     */
    public function setVisible()
    {
        $this->visible = true;
    }

    public function input(RendererInterface $renderer)
    {
        $attributes = $this->visible ? $this->attributes : array_merge(array('style' => 'display: none;'), $this->attributes);

        return Html::input('text', $this->name, $this->value, $attributes);
    }

    public function render(RendererInterface $renderer)
    {
        if ($this->visible) {
            $label = sprintf('<label for="%s" id="%s">%s</label>', $this->name, $this->name, $this->label);
        } else {
            $label = sprintf('<label for="%s" id="%s" style="display: none;">%s</label>', $this->name, $this->name, $this->label);
        }

        return $label.$this->input($renderer);
    }
}
