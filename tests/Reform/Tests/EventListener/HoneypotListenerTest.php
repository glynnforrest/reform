<?php

namespace Reform\Tests\EventListener;

use Reform\EventListener\HoneypotListener;
use Reform\Form\FormEvent;
use Reform\Form\Row\Honeypot;
use Reform\Form\Form;
use Reform\Event\HoneypotEvent;

/**
 * HoneypotListenerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $form;

    public function setUp()
    {
        $this->listener = new HoneypotListener();
        $this->form = new Form('/url');
    }

    protected function newEvent()
    {
        return new FormEvent($this->form);
    }

    public function testFieldIsAppliedToForm()
    {
        $this->assertNull($this->form->getTag(Honeypot::ROW_TAG));
        $this->listener->onFormCreate($this->newEvent());
        $this->assertSame('rating', $this->form->getTag(Honeypot::ROW_TAG));
        $this->assertInstanceOf('Reform\Form\Row\Honeypot', $this->form->getRow('rating'));
    }

    public function testSpecifiedFieldIsAppliedToForm()
    {
        $this->listener = new HoneypotListener(false, 'foo');
        $this->assertNull($this->form->getTag(Honeypot::ROW_TAG));
        $this->listener->onFormCreate($this->newEvent());
        $this->assertSame('foo', $this->form->getTag(Honeypot::ROW_TAG));
        $this->assertInstanceOf('Reform\Form\Row\Honeypot', $this->form->getRow('foo'));
    }

    public function testHoneypotCaught()
    {
        //to pass into function scope for PHP 5.3
        $form = $this->form;

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->once())
                   ->method('dispatch')
                   ->with(
                       Honeypot::CAUGHT,
                       $this->callback(function ($event) use ($form) {
                               return $event instanceof HoneypotEvent &&
                                   $event->getForm() === $form &&
                                   $event->getRowName() === 'rating';
                           }));

        //init the honeypot field
        $this->listener->onFormCreate($this->newEvent());

        //submit the form with something in the field
        $this->form->submitForm(array('rating' => 'spam'));

        //form is valid and the honeypot field has input
        $this->assertTrue($this->form->isValid());
        $this->assertFalse($this->form->hasTag(Honeypot::CAUGHT));
        $this->listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        $this->assertTrue($this->form->hasTag(Honeypot::CAUGHT));
    }

    public function testHoneypotFieldEmpty()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->never())
                   ->method('dispatch');

        //init the honeypot field
        $this->listener->onFormCreate($this->newEvent());

        //submit the form
        $this->form->submitForm(array());

        //form is valid but the honeypot field is empty
        $this->assertTrue($this->form->isValid());
        $this->listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        $this->assertFalse($this->form->hasTag(Honeypot::CAUGHT));
    }

    public function testFormNotValid()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->never())
                   ->method('dispatch');

        //init the honeypot field
        $this->listener->onFormCreate($this->newEvent());

        //form isn't valid so no check will be made
        $this->assertFalse($this->form->isValid());
        $this->listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        $this->assertFalse($this->form->hasTag(Honeypot::CAUGHT));
    }

    // public function testTokenIsNotCheckedIfFormIsNotValid()
    // {
    //     $this->form->expects($this->once())
    //                ->method('isValid')
    //                ->will($this->returnValue(false));
    //     $this->form->expects($this->never())
    //                ->method('getId');
    //     $this->form->expects($this->never())
    //                ->method('getRow');
    //     $this->manager->expects($this->never())
    //                   ->method('check');
    //     $this->manager->expects($this->never())
    //                   ->method('init');
    //     $this->listener->afterFormValidate($this->newEvent());
    // }

    public function testSubscribedEvents()
    {
        $expected = array(
            FormEvent::CREATE => array('onFormCreate'),
            FormEvent::POST_VALIDATE => array('afterFormValidate'),
        );
        $this->assertSame($expected, HoneypotListener::getSubscribedEvents());
    }
}
