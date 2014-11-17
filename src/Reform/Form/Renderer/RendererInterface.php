<?php

namespace Reform\Form\Renderer;

use Reform\Form\Row\AbstractRow;

/**
 * RendererInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface RendererInterface
{

    public function row(AbstractRow $row, $use_label = true);

    public function input($type, $name, $value = null, array $attributes = array());

    public function select($name, array $values, $selected = null, $multiple = false, array $attributes = array());

}
