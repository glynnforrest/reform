<?php

namespace Reform\Event;

use Symfony\Component\EventDispatcher\Event;
use Reform\Form\Form;

/**
 * FormEvent
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FormEvent extends Event
{
    const CREATE = 'reform.create';
    const PRE_VALIDATE = 'reform.pre-validate';
    const POST_VALIDATE = 'reform.post-validate';

    protected $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }
}
