<?php

namespace Reform\EventListener;

use Blockade\CsrfManager;
use Reform\Form\FormEvent;
use Reform\Form\Row\Hidden;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * CsrfListener
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CsrfListener implements EventSubscriberInterface
{

    protected $manager;
    protected $form_field;

    public function __construct(CsrfManager $manager, $form_field = '_token')
    {
        $this->manager = $manager;
        $this->form_field = $form_field;
    }

    public function onFormCreate(FormEvent $event)
    {
        $form = $event->getForm();
        $id = $form->getId();
        $this->manager->maybeInit($id);
        $input = new Hidden($this->form_field);
        $input->setValue($this->manager->get($id));
        $form->addRow($input);
    }

    public function afterFormValidate(FormEvent $event)
    {
        $form = $event->getForm();
        if (!$form->isValid()) {
            return;
        }
        $id = $form->getId();
        $input = $form->getRow($this->form_field);
        $token = $input->getValue();
        $this->manager->check($id, $token);
        //the token is valid and is now removed. Generate a new token
        //in case the form needs to be submitted again.
        $input->setValue($this->manager->init($id));
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvent::CREATE => array('onFormCreate'),
            FormEvent::POST_VALIDATE => array('afterFormValidate')
        );
    }

}
