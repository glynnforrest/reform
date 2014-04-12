<?php

namespace Reform\Tests\Form;

use Reform\Form\Form;
use Reform\Helper\Html;
use Reform\Validation\Rule;

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../../../bootstrap.php';

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

    public function testInput()
    {
        $f = $this->createForm('/url');
        $f->text('name');
        $this->assertSame(Html::input('text', 'name'), $f->input('name'));
    }

    public function testLabel()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $this->assertSame(Html::label('username', 'Username'), $f->label('username'));
    }

    public function testError()
    {
        $f = $this->createForm('/url');
        $f->text('email');
        $this->assertNull($f->error('email'));
        $error_msg = 'Email is invalid.';
        $f->getRow('email')->setError($error_msg);
        $expected = '<small class="error">Email is invalid.</small>';
        $this->assertSame($expected, $f->error('email'));
    }

    public function testCreateSimpleForm()
    {
        $f = $this->createForm('/post/url', 'get');
        $this->assertInstanceOf('\Reform\Form\Form', $f->text('name'));
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

    public function testGetAndSetValue()
    {
        $f = $this->createForm('/url');
        $f->text('message');
        $this->assertSame(null, $f->getValue('message'));
        $this->assertInstanceOf('\Reform\Form\Form', $f->setValue('message', 'hello'));
        $this->assertSame('hello', $f->getValue('message'));
    }

    public function testSetValueIgnoresUndefinedRow()
    {
        $f = $this->createForm('/url');
        $f->setValue('username', 'user42');
        $this->assertSame(array(), $f->getValues());
    }

    public function testSetCreateNewRow()
    {
        $f = $this->createForm('/url');
        $this->assertInstanceOf('\Reform\Form\Form', $f->setValue('username', 'user42', true));
        $this->assertSame('user42', $f->getValue('username'));
        $this->assertSame('text', $f->getRow('username')->getType());
    }

    public function testGetAndSetValues()
    {
        $f = $this->createForm('/url');
        $f->text('username', 'glynn');
        $f->password('password', 'secret');
        $expected = array('username' => 'glynn', 'password' => 'secret');
        $this->assertSame($expected, $f->getValues());
        $changed = array('username' => 'glynnforrest', 'password' => 'token');
        $this->assertInstanceOf('\Reform\Form\Form', $f->setValues($changed));
        $this->assertSame($changed, $f->getValues());
    }

    public function testSetValuesIgnoresUndefinedRow()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->password('password');
        $values = array('username' => 'glynn', 'password' => 'secret', 'foo' => 'bar');
        $expected = array('username' => 'glynn', 'password' => 'secret');
        $f->setValues($values);
        $this->assertSame($expected, $f->getValues());
    }

    public function testGetAndSetError()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->setErrors(array('username' => 'Username error'));
        $this->assertSame('Username error', $f->getError('username'));
        $f->setError('username', 'A different error');
        $this->assertSame('A different error', $f->getError('username'));
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
        $f->text('username');
        $f->password('password');
        $f->setError('password', 'Password error');
        $f->setError('username', 'Username error');
        $errors = array(
            'username' => 'Username error',
            'password' => 'Password error'
        );
        $this->assertSame($errors, $f->getErrors());
    }

    public function testSetValuesCreateRows()
    {
        $f = $this->createForm('/url');
        $new = array('foo' => 'bar', 'baz' => 'qux', 'fu bar' => 'foo bar');
        $this->assertInstanceOf('\Reform\Form\Form', $f->setValues($new, true));
        foreach ($new as $name => $value) {
            $this->assertSame($value, $f->getValue($name));
            $this->assertSame('text', $f->getRow($name)->getType());
        }
    }

    public function testGetRow()
    {
        $f = $this->createForm('/url');
        $this->assertInstanceOf('\Reform\Form\Form', $f->text('username'));
        $this->assertInstanceOf('\Reform\Form\FormRow', $f->getRow('username'));
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

    public function testCreateFromArray()
    {
        $f = $this->createForm('/url');
        $values = array('username' => 'glynn', 'age' => 100);
        $f->setValues($values, true);
        $expected = Html::openTag('form', array('action' => '/url', 'method' => 'POST'));
        $expected .= $this->stubRow('text', 'username', 'glynn');
        $expected .= $this->stubRow('text', 'age', 100);
        $expected .= '</form>';
        $this->assertSame($expected, $f->render());
    }

    public function testCreateAndModify()
    {
        $f = $this->createForm('/url');
        $f->text('username', 'glynn');
        $comment =  'Hello world';
        $f->textarea('comment', $comment);

        $first_form = Html::openTag('form', array('action' => '/url', 'method' => 'POST'));
        $first_form .= $this->stubRow('text', 'username', 'glynn');
        $first_form .= $this->stubRow('textarea', 'comment', $comment);
        $first_form .= '</form>';
        $this->assertSame($first_form, $f->render());

        //now modify the rows
        $username_row = $f->getRow('username');
        $username_row->setValue('glynnforrest');

        $comment_row = $f->getRow('comment');
        $comment_row->setType('text');

        $second_form = Html::openTag('form', array('action' => '/url', 'method' => 'POST'));
        $second_form .= $this->stubRow('text', 'username', 'glynnforrest');
        $second_form .= $this->stubRow('text', 'comment', $comment);
        $second_form .= '</form>';
        $this->assertSame($second_form, $f->render());
    }

    public function testToStringCallsRender()
    {
        $f = $this->createForm('/url');
        $this->assertSame($f->render(), $f->__toString());
    }

    public function testAddErrors()
    {
        $f = $this->createForm('/url');
        $f->text('username');
        $f->text('email', 'foo');

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
        $this->assertSame($username_error_html, $f->error('username'));
        $email_error_html = '<small class="error">' . $email_error . '</small>';
        $this->assertSame($email_error_html, $f->error('email'));

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

    public function testCheck()
    {
        $f = $this->createForm('/url');
        $f->text('foo');
        $this->assertSame($f, $f->check('foo', new Rule\Required()));
        $validator = $f->getValidator();
    }

    public function testIsValidDefaultsToFalse()
    {
        $f = $this->createForm('/url');
        $this->assertFalse($f->isValid());
    }

    public function validateProvider()
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
     * @dataProvider validateProvider()
     */
    public function testValidation($values, $pass)
    {
        $f = $this->createForm('/url');
        $f->text('username')
          ->check('username', new Rule\Required())
          ->check('username', new Rule\AlphaNumeric())
          ->password('password')
          ->check('password', new Rule\Required());

        $f->validate($values);
        if ($pass) {
            $this->assertTrue($f->isValid());
        } else {
            $this->assertFalse($f->isValid());
        }
    }

    public function testIsValidIsResetOnValidation()
    {
        $f = $this->createForm('/url');
        $f->text('username')
          ->check('username', new Rule\Required());
        $f->validate(array('username' => 'foo'));
        $this->assertTrue($f->isValid());
        $f->validate(array('username' => ''));
        $this->assertFalse($f->isValid());
    }

    /**
     * @dataProvider validateProvider()
     */
    public function testHandle($values, $pass)
    {
        $f = $this->createForm('/url');
        $f->text('username')
          ->check('username', new Rule\Required())
          ->check('username', new Rule\AlphaNumeric())
          ->password('password')
          ->check('password', new Rule\Required());

        $request = Request::create('/url');
        $request->request->add($values);
        $f->handle($request);
        if ($pass) {
            $this->assertTrue($f->isValid());
        } else {
            $this->assertFalse($f->isValid());
        }
    }

    public function testGetValuesWithArray()
    {
        $f = $this->createForm('/url');
        $f->text('data[0]', 'foo')
          ->text('data[1]', 'bar');
        $expected = array(
            'data' => array(
                'foo', 'bar'
            )
        );
        $this->assertSame('foo', $f->getValue('data[0]'));
        $this->assertSame('bar', $f->getValue('data[1]'));
        $this->assertSame($expected, $f->getValues());
    }

    public function testGetValuesWithComplexArray()
    {
        $f = $this->createForm('/url');
        $f->text('data[rows][first]', 'foo')
          ->text('data[rows][second]', 'bar')
          ->text('data[foo]', 'baz')
          ->text('foo', 'bar');
        $expected = array(
            'data' => array(
                'rows' => array(
                    'first' => 'foo',
                    'second' => 'bar',
                ),
                'foo' => 'baz'
            ),
            'foo' => 'bar'
        );
        $this->assertSame('foo', $f->getValue('data[rows][first]'));
        $this->assertSame('bar', $f->getValue('data[rows][second]'));
        $this->assertSame('baz', $f->getValue('data[foo]'));
        $this->assertSame('bar', $f->getValue('foo'));
        $this->assertSame($expected, $f->getValues());
    }

    public function testGetValuesOverwritesArrays()
    {
        $f = $this->createForm('/url');
        $f->text('data[foo]', 'foo')
          ->text('data', 'bar');
        $expected = array(
            'data' => 'bar'
        );
        $this->assertSame($expected, $f->getValues());
    }

    public function testGetValuesOverwritesRows()
    {
        $f = $this->createForm('/url');
        $f->text('data', 'bar')
          ->text('data[bar]', 'bar');
        $expected = array(
            'data' => array(
                'bar' => 'bar'
            )
        );
        $this->assertSame($expected, $f->getValues());
    }

    public function testGetValuesOverwritesArraysNested()
    {
        $f = $this->createForm('/url');
        $f->text('data[foo][bar][baz]', 'foo')
          ->text('data[foo][bar]', 'bar');
        $expected = array(
            'data' => array(
                'foo' => array(
                    'bar' => 'bar'
                )
            )
        );
        $this->assertSame($expected, $f->getValues());
    }

    public function testGetValuesOverwritesRowsNested()
    {
        $f = $this->createForm('/url');
        $f->text('data[foo][bar]', 'bar')
          ->text('data[foo][bar][baz]', 'baz');
        $expected = array(
            'data' => array(
                'foo' => array(
                    'bar' => array(
                        'baz' => 'baz'
                    )
                )
            )
        );
        $this->assertSame($expected, $f->getValues());
    }

    public function testSetValuesWithArrays()
    {
        $values = array(
            'foo' => 'foo',
            'bar' => array(
                'one' => 'one',
                'two' => 'two'
            )
        );
        $f = $this->createForm('/url');
        $f->setValues($values, true);
        $this->assertSame('foo', $f->getValue('foo'));
        $this->assertSame('one', $f->getValue('bar[one]'));
        $this->assertSame('two', $f->getValue('bar[two]'));
        $this->assertSame($values, $f->getValues());
    }

    public function testMatchesRowsWithArrays()
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
        $f->text('foo')
          ->text('bar[one]')
          ->text('bar[two]')
          ->text('baz[one][two]');
        $request = Request::create('/url');
        $request->request->add($values);
        $f->handle($request);
        $this->assertTrue($f->isValid());
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
        $f->validate(array());
    }

    public function testSetAndGetValidator()
    {
        $f = $this->createForm('/url');
        $this->assertInstanceOf('\Reform\Validation\Validator', $f->getValidator());
        $validator = $this->getMock('\Reform\Validation\Validator');
        $this->assertSame($f, $f->setValidator($validator));
        $this->assertSame($validator, $f->getValidator());
    }

}
