<?php

namespace Reform\Event;

use Symfony\Component\EventDispatcher\Event;
use Reform\Form\Form;

/**
 * CsrfEvent
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CsrfEvent extends Event
{
    protected $form;
    protected $form_field;

    public function __construct(Form $form, $form_field)
    {
        $this->form = $form;
        $this->form_field = $form_field;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getRow()
    {
        return $this->form->getRow($this->form_field);
    }

    public function getRowName()
    {
        return $this->form_field;
    }
}
