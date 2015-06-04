<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;

/**
 * DateRow
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateRow extends AbstractRow
{
    public function input(RendererInterface $renderer)
    {
        $value = $this->value instanceof \DateTime ? $this->value->format('Y-m-d') : null;

        return $renderer->input('date', $this->name, $value, $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this);
    }

    public function submitForm(array $values)
    {
        if (!isset($values[$this->name]) || $values[$this->name] === '') {
            $this->value = null;
            return;
        }

        $this->value = new \DateTime($values[$this->name]);
    }
}
