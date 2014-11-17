<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;
use Reform\Form\Renderer\RendererInterface;

/**
 * Hidden
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Hidden extends AbstractRow
{
    public function input(RendererInterface $renderer)
    {
        return Html::input('hidden', $this->name, $this->value, $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $this->input($renderer);
    }
}
