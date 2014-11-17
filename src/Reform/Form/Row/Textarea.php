<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;
use Reform\Form\Renderer\RendererInterface;

/**
 * Textarea
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Textarea extends AbstractRow
{

    public function input(RendererInterface $renderer)
    {
        return $renderer->input('textarea', $this->name, $this->value, $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this);
    }

}
