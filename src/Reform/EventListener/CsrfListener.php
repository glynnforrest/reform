<?php

namespace Reform\EventListener;

use Reform\Event\FormEvent;
use Reform\Form\Row\Hidden;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Reform\Csrf\CsrfChecker;
use Reform\Exception\CsrfTokenException;
use Reform\Event\CsrfEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * CsrfListener
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CsrfListener implements EventSubscriberInterface
{
    //the tag on the form that contains the name of the csrf row
    const ROW = 'reform.csrf.row';
    //the tag on the form when the field is invalid
    const INVALID = 'reform.csrf.invalid';

    protected $checker;
    protected $form_field;
    protected $throw_exception;

    public function __construct(CsrfChecker $checker, $form_field = '_token', $throw_exception = false)
    {
        $this->checker = $checker;
        $this->form_field = $form_field;
        $this->throw_exception = $throw_exception;
    }

    public function onFormCreate(FormEvent $event)
    {
        $form = $event->getForm();
        $id = $form->getId();
        $this->checker->maybeInit($id);
        $input = new Hidden($this->form_field);
        $input->setValue($this->checker->get($id));
        $form->addRow($input);
        $form->addTag(self::ROW, $this->form_field);
    }

    public function afterFormValidate(FormEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
        $form = $event->getForm();
        if (!$form->isValid()) {
            return;
        }

        $id = $form->getId();
        $input = $form->getRow($this->form_field);
        $token = $input->getValue();

        if ($this->checker->check($id, $token)) {
            //the token is valid and is now removed. Generate a new token
            //in case the form needs to be submitted again.
            $input->setValue($this->checker->init($id));

            return;
        }

        $form->addTag(CsrfListener::INVALID);
        $csrf_event = new CsrfEvent($form, $this->form_field);
        $dispatcher->dispatch(CsrfListener::INVALID, $csrf_event);

        if ($this->throw_exception) {
            throw new CsrfTokenException(sprintf('Csrf field "%s" on form "%s" is invalid.', $this->form_field, $form->getId()));
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
