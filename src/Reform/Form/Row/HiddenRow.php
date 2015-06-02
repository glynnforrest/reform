<?php

namespace Reform\Form\Row;

use Reform\Helper\Html;
use Reform\Form\Renderer\RendererInterface;

/**
 * HiddenRow
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HiddenRow extends AbstractRow
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
