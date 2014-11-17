<?php

namespace Reform\EventListener;

use Reform\Form\FormEvent;
use Reform\Form\Row\Honeypot;
use Reform\Exception\HoneypotException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * HoneypotListener adds a dummy text field hidden with inline css. A form
 * submission containing this field will be considered to be spam.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotListener implements EventSubscriberInterface
{
    protected $manager;
    protected $form_field;
    protected $form_label;

    public function __construct($form_field = 'rating', $form_label = 'Do not complete this field')
    {
        $this->form_field = $form_field;
        $this->form_label = $form_label;
    }

    public function onFormCreate(FormEvent $event)
    {
        $form = $event->getForm();
        $input = new Honeypot($this->form_field);
        $input->setLabel($this->form_label);
        $form->addRow($input);
    }

    public function afterFormValidate(FormEvent $event)
    {
        $form = $event->getForm();
        if (!$form->isValid()) {
            return;
        }
        if ((string) $form->getRow($this->form_field)->getValue() !== '') {
            throw new HoneypotException(sprintf('Honeypot field "%s" tripped on form "%s"', $this->form_field, $form->getId()));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvent::CREATE => array('onFormCreate'),
            FormEvent::POST_VALIDATE => array('afterFormValidate'),
        );
    }
}
