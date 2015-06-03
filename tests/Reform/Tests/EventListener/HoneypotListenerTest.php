<?php

namespace Reform\Tests\EventListener;

use Reform\EventListener\HoneypotListener;
use Reform\Event\FormEvent;
use Reform\Form\Row\HoneypotRow;
use Reform\Form\Form;
use Reform\Event\HoneypotEvent;
use Reform\Exception\HoneypotException;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
        $this->assertNull($this->form->getTag(HoneypotListener::ROW));
        $this->listener->onFormCreate($this->newEvent());
        $this->assertSame('rating', $this->form->getTag(HoneypotListener::ROW));
        $this->assertInstanceOf('Reform\Form\Row\HoneypotRow', $this->form->getRow('rating'));
    }

    public function testSpecifiedFieldIsAppliedToForm()
    {
        $this->listener = new HoneypotListener('foo');
        $this->assertNull($this->form->getTag(HoneypotListener::ROW));
        $this->listener->onFormCreate($this->newEvent());
        $this->assertSame('foo', $this->form->getTag(HoneypotListener::ROW));
        $this->assertInstanceOf('Reform\Form\Row\HoneypotRow', $this->form->getRow('foo'));
    }

    public function testHoneypotCaught()
    {
        //to pass into function scope for PHP 5.3
        $form = $this->form;

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->once())
                   ->method('dispatch')
                   ->with(
                       HoneypotListener::CAUGHT,
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
        $this->assertFalse($this->form->hasTag(HoneypotListener::CAUGHT));
        $this->listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        $this->assertTrue($this->form->hasTag(HoneypotListener::CAUGHT));
    }

    public function testHoneypotCaughtThrowException()
    {
        $listener = new HoneypotListener('rating', 'Do not complete', true);

        //to pass into function scope for PHP 5.3
        $form = $this->form;

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->once())
                   ->method('dispatch')
                   ->with(
                       HoneypotListener::CAUGHT,
                       $this->callback(function ($event) use ($form) {
                               return $event instanceof HoneypotEvent &&
                                   $event->getForm() === $form &&
                                   $event->getRowName() === 'rating';
                           }));

        //init the honeypot field
        $listener->onFormCreate($this->newEvent());

        //submit the form with something in the field
        $this->form->submitForm(array('rating' => 'spam'));

        //form is valid and the honeypot field has input
        $this->assertTrue($this->form->isValid());
        $this->assertFalse($this->form->hasTag(HoneypotListener::CAUGHT));

        //catching the exception here to check an event is sent and tag is applied
        try {
            $listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        } catch (HoneypotException $e) {
            $msg = 'Honeypot field "rating" tripped on form "Reform\Form\Form"';
            $this->assertSame($msg, $e->getMessage());
            $this->assertTrue($this->form->hasTag(HoneypotListener::CAUGHT));

            return;
        }
        $this->fail('HoneypotException was not thrown.');
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
        $this->assertFalse($this->form->hasTag(HoneypotListener::CAUGHT));
    }

    public function testNoCheckForInvalidForm()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->never())
                   ->method('dispatch');

        //init the honeypot field
        $this->listener->onFormCreate($this->newEvent());

        //form isn't valid so no check will be made
        $this->assertFalse($this->form->isValid());
        $this->listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        $this->assertFalse($this->form->hasTag(HoneypotListener::CAUGHT));
    }

    public function testSubscribedEvents()
    {
        $expected = array(
            FormEvent::CREATE => array('onFormCreate'),
            FormEvent::POST_VALIDATE => array('afterFormValidate'),
        );
        $this->assertSame($expected, HoneypotListener::getSubscribedEvents());
    }

    public function testDispatch()
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($this->listener);
        $dispatcher->dispatch(FormEvent::CREATE, $this->newEvent());
        $this->form->submitForm(array('rating' => 'spam'));
        $this->assertTrue($this->form->isValid());
        $dispatcher->dispatch(FormEvent::POST_VALIDATE, $this->newEvent());
        $this->assertTrue($this->form->hasTag(HoneypotListener::CAUGHT));
    }
}
