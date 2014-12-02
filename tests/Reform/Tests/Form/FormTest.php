<?php

namespace Reform\Tests\Form;

use Reform\Form\Form;
use Reform\Helper\Html;
use Reform\Validation\Rule;
use Reform\Form\Renderer\BootstrapRenderer;
use Reform\Form\Row\Text;
use Symfony\Component\HttpFoundation\Request;
use Reform\Event\FormEvent;

/**
 * FormTest
 * @author Glynn Forrest me@glynnforrest.com
 **/
class FormTest extends \PHPUnit_Framework_TestCase
{
    protected $dispatcher;

    public function setup()
    {
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->renderer = $this->getMock('Reform\Form\Renderer\RendererInterface');
    }

    protected function createForm($url, $method = 'POST', $attributes = array())
    {
        $form = new Form($url, $method, $attributes);
        $form->setDefaultRenderer($this->renderer);

        return $form;
    }

    public function testGetAndSetDefaultRenderer()
    {
        $form = $this->createForm('/url');
        $this->assertSame($this->renderer, $form->getDefaultRenderer());

        $new_renderer = new BootstrapRenderer();
        $this->assertSame($form, $form->setDefaultRenderer($new_renderer));
        $this->assertSame($new_renderer, $form->getDefaultRenderer());
    }

    public function testGetDefaultRendererNoneSet()
    {
        $form = new Form('/url');
        $this->assertInstanceOf('Reform\Form\Renderer\BootstrapRenderer', $form->getDefaultRenderer());
    }

    public function testCreateEmptyForm()
    {
        $f = $this->createForm('/post/url');
        $expected = Html::tag('form', null, array('action' => '/post/url', 'method' => 'POST'));
        $this->assertSame($expected, $f->render());
    }

    public function testCreateSimpleForm()
    {
        $f = $this->createForm('/post/url', 'get');
        $row = new Text('foo');
        $f->addRow($row);

        $expected = Html::openTag('form', array('action' => '/post/url', 'method' => 'GET'));
        $this->renderer->expects($this->once())
              ->method('row')
              ->with($row)
              ->will($this->returnValue('row'));
        $expected .= 'row';

        $expected .= '</form>';
        $this->assertSame($expected, $f->render());
    }

    public function testSimpleFormPassInRenderer()
    {
        $f = $this->createForm('/post/url', 'get');
        $row = new Text('foo');
        $f->addRow($row);

        $expected = Html::openTag('form', array('action' => '/post/url', 'method' => 'GET'));
        $this->renderer->expects($this->never())
                       ->method('row');

        $renderer = $this->getMock('Reform\Form\Renderer\RendererInterface');
        $renderer->expects($this->once())
              ->method('row')
              ->with($row)
              ->will($this->returnValue('row'));
        $expected .= 'row';

        $expected .= '</form>';
        $this->assertSame($expected, $f->render($renderer));
    }

    public function testGetAndSetAction()
    {
        $f = $this->createForm('/login');
        $this->assertSame('/login', $f->getAction());
        $this->assertInstanceOf('\Reform\Form\Form', $f->setAction('/login/somewhere/else'));
        $this->assertSame('/login/somewhere/else', $f->getAction());
    }

    public function testGetAndSetMethod()
    {
        $f = $this->createForm('/url');
        $this->assertSame('POST', $f->getMethod());
        $this->assertInstanceOf('\Reform\Form\Form', $f->setMethod('get'));
        $this->assertSame('GET', $f->getMethod());
    }

    public function testSetMethodThrowsException()
    {
        $f = $this->createForm('/url');
        $this->setExpectedException('\InvalidArgumentException');
        $f->setMethod('something-stupid');
    }

    public function testGetAndSetAttributes()
    {
        $f = $this->createForm('/url');
        $this->assertSame(array(), $f->getAttributes());
        $attributes = array('id' => 'my-form', 'class' => 'form');
        $this->assertInstanceOf('\Reform\Form\Form', $f->setAttributes($attributes));
        $this->assertSame($attributes, $f->getAttributes());
    }

    public function testAddAttributes()
    {
        $f = $this->createForm('/url');
        $this->assertInstanceOf('\Reform\Form\Form', $f->addAttributes(array('id' => 'my-form')));
        $this->assertSame(array('id' => 'my-form'), $f->getAttributes());
        $this->assertInstanceOf('\Reform\Form\Form', $f->addAttributes(array('class' => 'form')));
        $attributes = array('id' => 'my-form', 'class' => 'form');
        $this->assertSame($attributes, $f->getAttributes());
    }

    public function testGetAndSetValues()
    {
        $f = $this->createForm('/url');
        $f->text('username')->setValue('glynn');
        $f->password('password')->setValue('secret');
        $expected = array('username' => 'glynn', 'password' => 'secret');
        $this->assertSame($expected, $f->getValues());
        $changed = array('username' => 'glynnforrest', 'password' => 'token');
        $this->assertSame($f, $f->setValues($changed));
        $this->assertSame($changed, $f->getValues());
    }

    public function testSetValuesThrowsExceptionOnUnknown()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->password('password');
        $values = array('username' => 'glynn', 'foo' => 'bar', 'password' => 'secret');
        $this->setExpectedException('\InvalidArgumentException');
        $f->setValues($values);
    }

    public function testSetValuesIgnoreUnknown()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->password('password');
        $values = array('username' => 'glynn', 'foo' => 'bar', 'password' => 'secret');
        $expected = array('username' => 'glynn', 'password' => 'secret');
        $f->setValues($values, true);
        $this->assertSame($expected, $f->getValues());
    }

    public function testGetAndSetErrors()
    {
        $f = $this->createForm('/url');
        $f->text('username')->setError('Username error');
        $f->password('password')->setError('Password error');
        $errors = array(
            'username' => 'Username error',
            'password' => 'Password error',
        );
        $this->assertSame($errors, $f->getErrors());
        $changed = array('username' => 'bad', 'password' => 'incorrect');
        $this->assertSame($f, $f->setErrors($changed));
        $this->assertSame($changed, $f->getErrors());
    }

    public function testSetErrorsThrowsExceptionOnUnknown()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->password('password');
        $errors = array('username' => 'bad', 'foo' => 'poor', 'password' => 'unacceptable');
        $this->setExpectedException('\InvalidArgumentException');
        $f->setErrors($errors);
    }

    public function testSetErrorsIgnoreUnknown()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->password('password');
        $errors = array('username' => 'bad', 'foo' => 'poor', 'password' => 'unacceptable');
        $expected = array('username' => 'bad', 'password' => 'unacceptable');
        $f->setErrors($errors, true);
        $this->assertSame($expected, $f->getErrors());
    }

    public function testGetRow()
    {
        $f = $this->createForm('/url');
        $this->assertInstanceOf('\Reform\Form\Row\Text', $f->text('username'));
        $this->assertInstanceOf('\Reform\Form\Row\Text', $f->getRow('username'));
    }

    public function testRowIsReturnedByReference()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        //check that the same FormRow instance is returned every time.
        $first = $f->getRow('username');
        $this->assertNull($first->getValue());
        $second = $f->getRow('username');
        $second->setValue('user');
        $this->assertSame('user', $first->getValue());
        $this->assertSame($first, $second);
    }

    public function testSetErrors()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->text('email');
        $f->getRow('email')->setValue('foo');

        $username_error = 'Username is required.';
        $email_error = 'Email is invalid';
        $f->setErrors(array(
            'username' => $username_error,
            'email' => $email_error,
        ));

        //test the error messages are stored in each FormRow instance
        $this->assertSame($username_error, $f->getRow('username')->getError());
        $this->assertSame($email_error, $f->getRow('email')->getError());
    }

    public function testGetFields()
    {
        $f = $this->createForm('/url');
        $f->hidden('id');
        $f->text('username');
        $f->text('email');
        $f->password('password');
        $expected = array('id', 'username', 'email', 'password');
        $this->assertSame($expected, $f->getFields());
    }

    public function testBuildValidation()
    {
        $f = $this->createForm('/url');
        $v = $this->getMock('Reform\Validation\Validator');
        $f->setValidator($v);

        $rule = new Rule\Required();
        $f->text('foo')->addRule($rule);
        $v->expects($this->once())
          ->method('addRule')
          ->with('foo', $rule);

        $this->assertSame($v, $f->buildValidator());
        //no matter how many times build is called, nothing changes
        $this->assertSame($v, $f->buildValidator());
        $this->assertSame($v, $f->buildValidator());

        //allowing new rules is forbidden after building the validation
        $this->setExpectedException('Reform\Exception\BuildValidationException');
        $f->getRow('foo')->addRule($rule);
    }

    public function testIsValidDefaultsToFalse()
    {
        $f = $this->createForm('/url');
        $this->assertFalse($f->isValid());
    }

    public function submitProvider()
    {
        return array(
            array(array(), false),
            array(array('username' => 'foo'), false),
            array(array('password' => 'foo'), false),
            array(array('username' => '', 'password' => ''), false),
            array(array('username' => 'foo', 'password' => ''), false),
            array(array('username' => '', 'password' => 'foo'), false),
            array(array('username' => 'f-oo', 'password' => 'foo'), false),
            array(array('username' => 'foo', 'password' => 'foo'), true),
        );
    }

    /**
     * @dataProvider submitProvider()
     */
    public function testSubmitForm($values, $pass)
    {
        $f = $this->createForm('/url');
        $f->text('username')
          ->addRule(new Rule\Required())
          ->addRule(new Rule\AlphaNumeric());
        $f->password('password')
          ->addRule(new Rule\Required());

        $f->submitForm($values);
        if ($pass) {
            $this->assertTrue($f->isValid());
        } else {
            $this->assertFalse($f->isValid());
        }
    }

    public function testIsValidIsResetOnValidation()
    {
        $f = $this->createForm('/url');
        $f->text('username')->addRule(new Rule\Required());
        $f->submitForm(array('username' => 'foo'));
        $this->assertTrue($f->isValid());
        $f->submitForm(array('username' => ''));
        $this->assertFalse($f->isValid());
    }

    /**
     * @dataProvider submitProvider()
     */
    public function testHandle($values, $pass)
    {
        $f = $this->createForm('/url');
        $f->text('username')
          ->addRule(new Rule\Required())
          ->addRule(new Rule\AlphaNumeric());
        $f->password('password')
          ->addRule(new Rule\Required());

        $request = Request::create('/url');
        $request->request->add($values);
        $f->handle($request);
        if ($pass) {
            $this->assertTrue($f->isValid());
        } else {
            $this->assertFalse($f->isValid());
        }
    }

    public function testHandleWithArrays()
    {
        $values = array(
            'foo' => 'foo',
            'bar' => array(
                'one' => 'one',
                'two' => 'two',
            ),
            'baz' => array(
                'one' => array(
                    'two' => 'foo',
                ),
            ),
        );
        $f = $this->createForm('/url');
        $f->text('foo');
        $f->text('bar[one]');
        $f->text('bar[two]');
        $f->text('baz[one][two]');
        $request = Request::create('/url');
        $request->request->add($values);
        $f->handle($request);
        $this->assertSame('foo', $f->getRow('foo')->getValue());
        $this->assertSame('one', $f->getRow('bar[one]')->getValue());
        $this->assertSame('two', $f->getRow('bar[two]')->getValue());
        $this->assertSame('foo', $f->getRow('baz[one][two]')->getValue());
    }

    public function testGetId()
    {
        $this->assertSame('Reform\Form\Form', $this->createForm('/url')->getId());
    }

    public function testUseFiles()
    {
        $f = $this->createForm('/url');
        $this->assertSame($f, $f->useFiles());
        $expected = '<form action="/url" method="POST" enctype="multipart/form-data">';
        $this->assertSame($expected, $f->header());

        $f->addAttributes(array('class' => 'form'));
        $expected = '<form action="/url" method="POST" enctype="multipart/form-data" class="form">';
        $this->assertSame($expected, $f->header());
    }

    public function testSetEventDispatcher()
    {
        $f = $this->createForm('/url');
        $this->dispatcher->expects($this->once())
                         ->method('hasListeners')
                         ->with(FormEvent::CREATE)
                         ->will($this->returnValue(true));
        $this->dispatcher->expects($this->once())
                         ->method('dispatch')
                         ->with(FormEvent::CREATE);
        $this->assertSame($f, $f->setEventDispatcher($this->dispatcher));
    }

    public function testValidateEvents()
    {
        $f = $this->createForm('/url');
        $f->setEventDispatcher($this->dispatcher);
        $this->dispatcher->expects($this->exactly(2))
                         ->method('hasListeners')
                         ->with($this->logicalOr(
                             FormEvent::PRE_VALIDATE,
                             FormEvent::POST_VALIDATE
                         ))
                         ->will($this->returnValue(true));
        $this->dispatcher->expects($this->exactly(2))
                         ->method('dispatch')
                         ->with($this->logicalOr(
                             FormEvent::PRE_VALIDATE,
                             FormEvent::POST_VALIDATE
                         ));
        $f->submitForm(array());
    }

    public function testSetAndGetValidator()
    {
        $f = $this->createForm('/url');
        $this->assertInstanceOf('\Reform\Validation\Validator', $f->getValidator());
        $validator = $this->getMock('\Reform\Validation\Validator');
        $this->assertSame($f, $f->setValidator($validator));
        $this->assertSame($validator, $f->getValidator());
    }

    public function testAddRow()
    {
        $f = $this->createForm('/url');
        $row = $this->getMockForAbstractClass('Reform\Form\Row\AbstractRow', array('foo'));
        $this->assertSame($f, $f->addRow($row));
        $this->assertSame($row, $f->getRow('foo'));
    }

    public function testRow()
    {
        $f = $this->createForm('/url');
        $row = $this->getMockForAbstractClass('Reform\Form\Row\AbstractRow', array('foo'));

        $f->addRow($row);

        $row->expects($this->once())
            ->method('render')
            ->with($this->renderer)
            ->will($this->returnValue('row'));
        $this->assertSame('row', $f->row('foo'));
    }

    public function testRowWithRenderer()
    {
        $f = $this->createForm('/url');
        $row = $this->getMockForAbstractClass('Reform\Form\Row\AbstractRow', array('foo'));
        $renderer = $this->getMock('Reform\Form\Renderer\RendererInterface');

        $f->addRow($row);

        $row->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($renderer))
            ->will($this->returnValue('row'));
        $this->assertSame('row', $f->row('foo', $renderer));
    }

    public function testInput()
    {
        $f = $this->createForm('/url');
        $row = $this->getMockForAbstractClass('Reform\Form\Row\AbstractRow', array('foo'));

        $f->addRow($row);

        $row->expects($this->once())
            ->method('input')
            ->with($this->renderer)
            ->will($this->returnValue('input'));
        $this->assertSame('input', $f->input('foo'));
    }

    public function testInputWithRenderer()
    {
        $f = $this->createForm('/url');
        $row = $this->getMockForAbstractClass('Reform\Form\Row\AbstractRow', array('foo'));
        $renderer = $this->getMock('Reform\Form\Renderer\RendererInterface');

        $f->addRow($row);

        $row->expects($this->once())
            ->method('input')
            ->with($this->identicalTo($renderer))
            ->will($this->returnValue('input'));
        $this->assertSame('input', $f->input('foo', $renderer));
    }

    public function testTags()
    {
        $f = $this->createForm('/url');
        $this->assertSame(array(), $f->getTags());
        $this->assertFalse($f->hasTag('foo'));

        $this->assertSame($f, $f->addTag('foo'));
        $this->assertSame(array('foo'), $f->getTags());
        $this->assertTrue($f->hasTag('foo'));

        $this->assertSame($f, $f->addTag('bar'));
        $this->assertSame(array('foo', 'bar'), $f->getTags());
        $this->assertTrue($f->hasTag('bar'));

        //check for duplicates
        $this->assertSame($f, $f->addTag('foo'));
        $this->assertSame(array('foo', 'bar'), $f->getTags());
        $this->assertTrue($f->hasTag('foo'));

        $this->assertSame($f, $f->removeTag('foo'));
        $this->assertSame(array('bar'), $f->getTags());
        $this->assertFalse($f->hasTag('foo'));

        //remove non-existing tag
        $this->assertSame($f, $f->removeTag('baz'));
        $this->assertSame(array('bar'), $f->getTags());
        $this->assertFalse($f->hasTag('baz'));
    }

    public function testTagsWithValues()
    {
        $f = $this->createForm('/url');
        $this->assertSame(array(), $f->getTags());
        $this->assertFalse($f->hasTag('foo'));
        $this->assertNull($f->getTag('foo'));

        $this->assertSame($f, $f->addTag('foo', 'foo-value'));
        $this->assertSame(array('foo'), $f->getTags());
        $this->assertTrue($f->hasTag('foo'));
        $this->assertSame('foo-value', $f->getTag('foo'));

        $this->assertSame($f, $f->addTag('bar', 'bar-value'));
        $this->assertSame(array('foo', 'bar'), $f->getTags());
        $this->assertTrue($f->hasTag('bar'));
        $this->assertSame('bar-value', $f->getTag('bar'));

        $this->assertSame($f, $f->addTag('foo', 'foo-value-again'));
        $this->assertSame(array('foo', 'bar'), $f->getTags());
        $this->assertTrue($f->hasTag('foo'));
        $this->assertSame('foo-value-again', $f->getTag('foo'));
    }
}
