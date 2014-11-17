<?php

namespace Reform\Form\Row;

use Reform\Form\Renderer\RendererInterface;

/**
 * Password
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Password extends AbstractRow
{

    public function input(RendererInterface $renderer)
    {
        return $renderer->input('password', $this->name, null, $this->attributes);
    }

    public function render(RendererInterface $renderer)
    {
        return $renderer->row($this);
    }

}
