<?php

namespace Reform\EventListener;

use Reform\Form\FormEvent;
use Reform\Form\Row\Honeypot;
use Reform\Exception\HoneypotException;
use Reform\Event\HoneypotEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * HoneypotListener adds a dummy text field hidden with inline css. A form
 * submission containing this field will be considered to be spam.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotListener implements EventSubscriberInterface
{
    //the tag on the form that contains the name of the honeypot row
    const ROW = 'reform.honeypot.field';
    //the tag on the form when the field is tripped
    const CAUGHT = 'reform.honeypot.caught';

    protected $throw_exception;
    protected $form_field;
    protected $form_label;

    public function __construct($throw_exception = false, $form_field = 'rating', $form_label = 'Do not complete this field')
    {
        $this->throw_exception = $throw_exception;
        $this->form_field = $form_field;
        $this->form_label = $form_label;
    }

    public function onFormCreate(FormEvent $event)
    {
        $input = new Honeypot($this->form_field);
        $input->setLabel($this->form_label);
        $form = $event->getForm();
        $form->addRow($input);
        $form->addTag(self::ROW, $this->form_field);
    }

    public function afterFormValidate(FormEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
        $form = $event->getForm();
        if (!$form->isValid() || (string) $form->getRow($this->form_field)->getValue() === '') {
            return;
        }

        $form->addTag(self::CAUGHT);
        $honeypot_event = new HoneypotEvent($form, $this->form_field);
        $dispatcher->dispatch(self::CAUGHT, $honeypot_event);

        if ($this->throw_exception) {
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
