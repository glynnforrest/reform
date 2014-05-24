<?php

namespace Reform\Tests\EventListener;

require_once __DIR__ . '/../../../bootstrap.php';

use Reform\EventListener\CsrfListener;
use Reform\Form\FormEvent;

/**
 * CsrfListenerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CsrfListenerTest extends \PHPUnit_Framework_TestCase
{

    protected $manager;
    protected $listener;
    protected $form;

    public function setUp()
    {
        $this->manager = $this->getMockBuilder('Blockade\CsrfManager')
                              ->disableOriginalConstructor()
                              ->getMock();
        $this->listener = new CsrfListener($this->manager);
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
        $this->manager->expects($this->once())
                      ->method('get')
                      ->with('foo')
                      ->will($this->returnValue('csrf_id'));
        $this->form->expects($this->once())
                   ->method('addRow')
                   ->with('hidden', '_token');
        $this->form->expects($this->once())
                   ->method('setValue')
                   ->with('_token', 'csrf_id');
        $this->listener->onFormCreate($this->newEvent());
    }

    public function testSpecifiedFieldIsAppliedToForm()
    {
        $listener = new CsrfListener($this->manager, '__csrf_token');
        $this->form->expects($this->once())
                   ->method('getId')
                   ->will($this->returnValue('foo'));
        $this->manager->expects($this->once())
                      ->method('get')
                      ->with('foo')
                      ->will($this->returnValue('csrf_id'));
        $this->form->expects($this->once())
                   ->method('addRow')
                   ->with('hidden', '__csrf_token');
        $this->form->expects($this->once())
                   ->method('setValue')
                   ->with('__csrf_token', 'csrf_id');
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
        $this->form->expects($this->once())
                   ->method('getValue')
                   ->will($this->returnValue('csrf_token'));
        $this->manager->expects($this->once())
                      ->method('check')
                      ->with('foo', 'csrf_token')
                      ->will($this->returnValue(true));
        $this->listener->afterFormValidate($this->newEvent());
    }

    public function testTokenIsNotCheckedIfFormIsNotValid()
    {
        $this->form->expects($this->once())
                   ->method('isValid')
                   ->will($this->returnValue(false));
        $this->form->expects($this->never())
                   ->method('getId');
        $this->form->expects($this->never())
                   ->method('getValue');
        $this->manager->expects($this->never())
                      ->method('check');
        $this->listener->afterFormValidate($this->newEvent());
    }

    public function testSubscribedEvents()
    {
        $expected = array(
            FormEvent::CREATE => array('onFormCreate'),
            FormEvent::POST_VALIDATE => array('afterFormValidate')
        );
        $this->assertSame($expected, CsrfListener::getSubscribedEvents());
    }

}