<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;

/**
 * TextRow
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextRow extends AbstractRow
{
    public function input(RendererInterface $renderer)
    {
        return $renderer->input('text', $this->name, $this->value, $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this);
    }
}
