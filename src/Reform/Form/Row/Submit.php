<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;

/**
 * Submit
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Submit extends AbstractRow
{
    public function input(RendererInterface $renderer)
    {
        //add a value to the submit button if there is none
        if ($this->value === null) {
            $this->value = $this->sensible($this->name);
        }

        return $renderer->input('submit', $this->name, $this->value, $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this, false);
    }
}
