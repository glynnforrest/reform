<?php

namespace Reform\Tests\EventListener;

use Reform\EventListener\CsrfListener;
use Reform\Form\FormEvent;
use Reform\Form\Row\Hidden;

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
        $this->form = $this->getMockBuilder('Reform\Form\Form')
                           ->disableOriginalConstructor()
                           ->getMock();
    }

    protected function newEvent()
    {
        return new FormEvent($this->form);
    }

    public function testFieldIsAppliedToForm()
    {
        $this->form->expects($this->once())
                   ->method('getId')
                   ->will($this->returnValue('foo'));
        $this->checker->expects($this->once())
                      ->method('get')
                      ->with('foo')
                      ->will($this->returnValue('csrf_id'));
        $this->form->expects($this->once())
                   ->method('addRow')
                   ->with($this->callback(function ($row) {
                       return $row instanceof Hidden &&
                           $row->getValue() === 'csrf_id' &&
                           $row->getName() === '_token';
                   }));
        $this->listener->onFormCreate($this->newEvent());
    }

    public function testSpecifiedFieldIsAppliedToForm()
    {
        $listener = new CsrfListener($this->checker, '__csrf_token');
        $this->form->expects($this->once())
                   ->method('getId')
                   ->will($this->returnValue('foo'));
        $this->checker->expects($this->once())
                      ->method('get')
                      ->with('foo')
                      ->will($this->returnValue('csrf_id'));
        $this->form->expects($this->once())
                   ->method('addRow')
                   ->with($this->callback(function ($row) {
                       return $row instanceof Hidden &&
                           $row->getValue() === 'csrf_id' &&
                           $row->getName() === '__csrf_token';
                   }));
        $listener->onFormCreate($this->newEvent());
    }

    public function testTokenIsCheckedAfterValidation()
    {
        $this->form->expects($this->once())
                   ->method('isValid')
                   ->will($this->returnValue(true));
        $this->form->expects($this->once())
                   ->method('getId')
                   ->will($this->returnValue('foo'));

        $input = new Hidden('_token');
        $input->setValue('csrf_token');
        $this->form->expects($this->once())
                   ->method('getRow')
                   ->with('_token')
                   ->will($this->returnValue($input));

        $this->checker->expects($this->once())
                      ->method('check')
                      ->with('foo', 'csrf_token')
                      ->will($this->returnValue(true));

        //after the token has been verified, assert that a new token is generated
        $this->checker->expects($this->once())
                      ->method('init')
                      ->with('foo')
                      ->will($this->returnValue('new_token'));
        $this->listener->afterFormValidate($this->newEvent());
        $this->assertSame('new_token', $input->getValue());
    }

    public function testTokenIsNotCheckedIfFormIsNotValid()
    {
        $this->form->expects($this->once())
                   ->method('isValid')
                   ->will($this->returnValue(false));
        $this->form->expects($this->never())
                   ->method('getId');
        $this->form->expects($this->never())
                   ->method('getRow');
        $this->checker->expects($this->never())
                      ->method('check');
        $this->checker->expects($this->never())
                      ->method('init');
        $this->listener->afterFormValidate($this->newEvent());
    }

    public function testSubscribedEvents()
    {
        $expected = array(
            FormEvent::CREATE => array('onFormCreate'),
            FormEvent::POST_VALIDATE => array('afterFormValidate'),
        );
        $this->assertSame($expected, CsrfListener::getSubscribedEvents());
    }
}
