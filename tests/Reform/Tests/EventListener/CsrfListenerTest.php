<?php

namespace Reform\Tests\EventListener;

use Reform\EventListener\CsrfListener;
use Reform\Event\FormEvent;
use Reform\Form\Row\Hidden;
use Reform\Event\CsrfEvent;
use Reform\Tests\Fixtures\FooForm;
use Reform\Exception\CsrfTokenException;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * CsrfListenerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CsrfListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $checker;
    protected $listener;
    protected $form;

    public function setUp()
    {
        $this->checker = $this->getMockBuilder('Reform\Csrf\CsrfChecker')
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->listener = new CsrfListener($this->checker);
        $this->form = new FooForm('/url');
    }

    protected function newEvent()
    {
        return new FormEvent($this->form);
    }

    public function testFieldIsAppliedToForm()
    {
        $this->assertNull($this->form->getTag(CsrfListener::ROW));
        $this->checker->expects($this->once())
                      ->method('get')
                      ->with('foo')
                      ->will($this->returnValue('csrf_token'));

        $this->listener->onFormCreate($this->newEvent());
        $this->assertSame('_token', $this->form->getTag(CsrfListener::ROW));
        $row = $this->form->getRow('_token');
        $this->assertInstanceOf('Reform\Form\Row\Hidden', $row);
        $this->assertSame('csrf_token', $row->getValue());
        $this->assertSame('_token', $row->getName());
    }

    public function testSpecifiedFieldIsAppliedToForm()
    {
        $listener = new CsrfListener($this->checker, '__csrf_token');
        $this->assertNull($this->form->getTag(CsrfListener::ROW));
        $this->checker->expects($this->once())
                      ->method('get')
                      ->with('foo')
                      ->will($this->returnValue('csrf_token'));

        $listener->onFormCreate($this->newEvent());
        $this->assertSame('__csrf_token', $this->form->getTag(CsrfListener::ROW));
        $row = $this->form->getRow('__csrf_token');
        $this->assertInstanceOf('Reform\Form\Row\Hidden', $row);
        $this->assertSame('csrf_token', $row->getValue());
        $this->assertSame('__csrf_token', $row->getName());
    }

    public function testCheckWithValidToken()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->never())
                   ->method('dispatch');

        //init the csrf field
        $this->listener->onFormCreate($this->newEvent());
        $this->checker->expects($this->once())
                      ->method('check')
                      ->with('foo', 'submitted_token')
                      ->will($this->returnValue(true));

        //submit the form
        $this->form->submitForm(array('_token' => 'submitted_token'));

        $this->assertTrue($this->form->isValid());
        $this->listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        $this->assertFalse($this->form->hasTag(CsrfListener::INVALID));
    }

    public function testCheckWithInvalidToken()
    {
        //init the csrf field
        $this->listener->onFormCreate($this->newEvent());
        $this->checker->expects($this->once())
                      ->method('check')
                      ->with('foo', 'invalid_token')
                      ->will($this->returnValue(false));

        //submit the form with an invalid token
        $this->form->submitForm(array('_token' => 'invalid_token'));

        //to pass into function scope for PHP 5.3
        $form = $this->form;

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->once())
                   ->method('dispatch')
                   ->with(
                       CsrfListener::INVALID,
                       $this->callback(function ($event) use ($form) {
                               return $event instanceof CsrfEvent &&
                                   $event->getForm() === $form &&
                                   $event->getRowName() === '_token';
                           }));

        $this->assertTrue($this->form->isValid());
        $this->listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        $this->assertTrue($this->form->hasTag(CsrfListener::INVALID));
    }

    public function testCheckWithInvalidTokenThrowException()
    {
        $listener = new CsrfListener($this->checker, '_token', true);
        //init the csrf field
        $listener->onFormCreate($this->newEvent());
        $this->checker->expects($this->once())
                      ->method('check')
                      ->with('foo', 'invalid_token')
                      ->will($this->returnValue(false));

        //submit the form with an invalid token
        $this->form->submitForm(array('_token' => 'invalid_token'));

        $this->assertTrue($this->form->isValid());

        //to pass into function scope for PHP 5.3
        $form = $this->form;

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->once())
                   ->method('dispatch')
                   ->with(
                       CsrfListener::INVALID,
                       $this->callback(function ($event) use ($form) {
                               return $event instanceof CsrfEvent &&
                                   $event->getForm() === $form &&
                                   $event->getRowName() === '_token';
                           }));

        //catching the exception here to check an event is sent and tag is applied
        try {
            $listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        } catch (CsrfTokenException $e) {
            $msg = 'Csrf field "_token" on form "foo" is invalid.';
            $this->assertSame($msg, $e->getMessage());
            $this->assertTrue($this->form->hasTag(CsrfListener::INVALID));

            return;
        }
        $this->fail('CsrfTokenException was not thrown.');
    }

    public function testNoCheckForInvalidForm()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->never())
                   ->method('dispatch');

        //form isn't valid so no check will be made
        $this->assertFalse($this->form->isValid());
        $this->listener->afterFormValidate($this->newEvent(), FormEvent::POST_VALIDATE, $dispatcher);
        $this->assertFalse($this->form->hasTag(CsrfListener::INVALID));
    }

    public function testSubscribedEvents()
    {
        $expected = array(
            FormEvent::CREATE => array('onFormCreate'),
            FormEvent::POST_VALIDATE => array('afterFormValidate'),
        );
        $this->assertSame($expected, CsrfListener::getSubscribedEvents());
    }

    public function testDispatch()
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($this->listener);
        $dispatcher->dispatch(FormEvent::CREATE, $this->newEvent());
        $this->form->submitForm(array());
        $this->assertTrue($this->form->isValid());
        $dispatcher->dispatch(FormEvent::POST_VALIDATE, $this->newEvent());
        $this->assertTrue($this->form->hasTag(CsrfListener::INVALID));
    }
}
