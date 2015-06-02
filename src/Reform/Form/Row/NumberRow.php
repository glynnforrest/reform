<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;

/**
 * NumberRow
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NumberRow extends AbstractRow
{
    public function input(RendererInterface $renderer)
    {
        return $renderer->input('number', $this->name, $this->value, $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this);
    }
}
