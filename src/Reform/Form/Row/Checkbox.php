<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;

/**
 * Checkbox
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Checkbox extends AbstractRow
{
    public function input(RendererInterface $renderer)
    {
        if ((bool) $this->value !== false) {
            $this->addAttributes(array('checked'));
        }
        //no matter what, the value of the input is 'checked'
        return $renderer->input('checkbox', $this->name, 'checked', $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this);
    }
}
