<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;

/**
 * Text
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Text extends AbstractRow
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
