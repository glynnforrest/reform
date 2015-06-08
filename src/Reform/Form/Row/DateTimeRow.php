<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;

/**
 * DateTimeRow
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeRow extends AbstractRow
{
    public function input(RendererInterface $renderer)
    {
        $date = $this->value instanceof \DateTime ? $this->value->format('Y-m-d') : null;
        $time = $this->value instanceof \DateTime ? $this->value->format('H:i') : null;

        return $renderer->input('date', $this->name.'[date]', $date, $this->attributes) .
        $renderer->input('time', $this->name.'[time]', $time, $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this);
    }

    public function submitForm(array $values)
    {
        if (!isset($values[$this->name.'[date]']) || !isset($values[$this->name.'[time]'])) {
            $this->value = null;
            return;
        }

        $date = $values[$this->name.'[date]'];
        $time = $values[$this->name.'[time]'];
        $this->value = new \DateTime("$date $time");
    }
}
