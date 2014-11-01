<?php

namespace Reform\Tests\Form;

use Reform\Form\Form;
use Reform\Helper\Html;
use Reform\Validation\Rule;

use Symfony\Component\HttpFoundation\Request;

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
    }

    protected function createForm($url, $method = 'POST', $attributes = array())
    {
        $form = new Form($url, $method, $attributes);

        return $form;
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
        $this->assertInstanceOf('\Reform\Form\Row\Text', $f->text('name'));
        $expected = Html::openTag('form', array('action' => '/post/url', 'method' => 'GET'));
        $expected .= Html::label('name', 'Name');
        $expected .= Html::input('text', 'name');
        $expected .= '</form>';
        $this->assertSame($expected, $f->render());
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

    public function testSetValuesIgnoreUndefined()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->password('password');
        $values = array('username' => 'glynn', 'password' => 'secret', 'foo' => 'bar');
        $expected = array('username' => 'glynn', 'password' => 'secret');
        /* $f->setValues($values, true); */
        /* $this->assertSame($expected, $f->getValues()); */
    }

    public function testGetAndSetError()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->setErrors(array('username' => 'Username error'));
        $this->assertSame('Username error', $f->getRow('username')->getError());
        $f->setErrors(array('username' => 'A different error'));
        $this->assertSame('A different error', $f->getRow('username')->getError());
    }

    public function testSetErrorThrowsExceptionUndefinedRow()
    {
        $f = $this->createForm('/url');
        $this->setExpectedException('InvalidArgumentException');
        $f->setError('username', 'user42');
    }

    public function testGetErrors()
    {
        $f = $this->createForm('/url');
        $f->text('username')->setError('Username error');
        $f->password('password')->setError('Password error');
        $errors = array(
            'username' => 'Username error',
            'password' => 'Password error'
        );
        $this->assertSame($errors, $f->getErrors());
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

    protected function stubRow($type, $name, $value = null, $error = null, $attributes = array())
    {
        $html = Html::label($name, ucfirst($name));
        $html .= Html::input($type, $name, $value, $attributes);
        if ($error) {
            $html .= '<small class="error">' . $error . '</small>';
        }

        return $html;
    }

    public function testCreateAndModify()
    {
        $f = $this->createForm('/url');
        $f->text('username')->setValue('glynn');
        $comment =  'Hello world';
        $f->textarea('comment')->setValue($comment);

        $first_form = Html::openTag('form', array('action' => '/url', 'method' => 'POST'));
        $first_form .= $this->stubRow('text', 'username', 'glynn');
        $first_form .= $this->stubRow('textarea', 'comment', $comment);
        $first_form .= '</form>';
        $this->assertSame($first_form, $f->render());

        //now modify the rows
        $username_row = $f->getRow('username');
        $username_row->setValue('glynnforrest');

        $comment_row = $f->getRow('comment');
        $comment_row->setValue('foo');

        $second_form = Html::openTag('form', array('action' => '/url', 'method' => 'POST'));
        $second_form .= $this->stubRow('text', 'username', 'glynnforrest');
        $second_form .= $this->stubRow('textarea', 'comment', 'foo');
        $second_form .= '</form>';
        $this->assertSame($second_form, $f->render());
    }

    public function testToStringCallsRender()
    {
        $f = $this->createForm('/url');
        $this->assertSame($f->render(), $f->__toString());
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
            'email' => $email_error
        ));

        //test the error messages are stored in each FormRow instance
        $this->assertSame($username_error, $f->getRow('username')->getError());
        $this->assertSame($email_error, $f->getRow('email')->getError());

        //test the error html is rendered
        $username_error_html = '<small class="error">' . $username_error . '</small>';
        $this->assertSame($username_error_html, $f->getRow('username')->error());
        $email_error_html = '<small class="error">' . $email_error . '</small>';
        $this->assertSame($email_error_html, $f->getRow('email')->error());

        //test the completed form contains the errors
        $form = Html::openTag('form', array('action' => '/url', 'method' => 'POST'));
        $form .= $this->stubRow('text', 'username', null, $username_error);
        $form .= $this->stubRow('text', 'email', 'foo', $email_error);
        $form .= '</form>';
        $this->assertSame($form, $f->render());
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

    public function testAddRule()
    {
        $f = $this->createForm('/url');
        $f->text('foo');
        $rule = new Rule\Required();
        $this->assertSame($f, $f->addRule('foo', $rule));
        $f->buildValidator();
        $validator = $f->getValidator();
        $this->assertSame(array('foo' => array($rule)), $validator->getRules());
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
                'two' => 'two'
            ),
            'baz' => array(
                'one' => array(
                    'two' => 'foo'
                )
            )
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
                         ->with('form.create')
                         ->will($this->returnValue(true));
        $this->dispatcher->expects($this->once())
                         ->method('dispatch')
                         ->with('form.create');
        $this->assertSame($f, $f->setEventDispatcher($this->dispatcher));
    }

    public function testValidateEvents()
    {
        $f = $this->createForm('/url');
        $f->setEventDispatcher($this->dispatcher);
        $this->dispatcher->expects($this->exactly(2))
                         ->method('hasListeners')
                         ->with($this->logicalOr(
                             'form.pre-validate',
                             'form.post-validate'
                         ))
                         ->will($this->returnValue(true));
        $this->dispatcher->expects($this->exactly(2))
                         ->method('dispatch')
                         ->with($this->logicalOr(
                             'form.pre-validate',
                             'form.post-validate'
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
        $row = $this->getMockBuilder('Reform\Form\Row\AbstractRow')
                    ->disableOriginalConstructor()
                    ->getMock();
        $row->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $f->addRow($row);
        $this->assertSame($row, $f->getRow('foo'));
    }

}
