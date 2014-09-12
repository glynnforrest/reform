<?php

namespace Reform\Tests\Helper;

use Reform\Helper\Html;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * HtmlTest
 * @author Glynn Forrest me@glynnforrest.com
 **/
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testJs()
    {
        $this->assertSame('<script type="text/javascript" src="/js/test.js"></script>' . PHP_EOL, Html::js('/js/test.js'));
    }

    public function testJsAttributes()
    {
        $this->assertSame('<script type="text/javascript" src="/js/test.js" id="my_script" class="script"></script>' . PHP_EOL, Html::js('/js/test.js', array(
        'id' => 'my_script', 'class' => 'script')));
    }

    public function testCss()
    {
        $this->assertSame('<link rel="stylesheet" type="text/css" href="/css/style.css" />' . PHP_EOL, Html::css('/css/style.css'));
    }

    public function testCssAttributes()
    {
        $this->assertSame('<link rel="stylesheet" type="text/css" href="/css/style.css" id="my_style" class="style" />' . PHP_EOL, Html::css('/css/style.css', array(
        'id' => 'my_style', 'class' => 'style')));
    }

    public function testEscape()
    {
        $this->assertSame('&lt;p&gt;Paragraph&lt;/p&gt;', Html::escape('<p>Paragraph</p>'));
    }

    public function testOpenTag()
    {
        $this->assertSame('<p>', Html::openTag('p'));
        $this->assertSame('<p class="text">',
        Html::openTag('p', array('class' => 'text')));
        $this->assertSame('<p class="text" id="paragraph5">',
        Html::openTag('p', array('class' => 'text', 'id' => 'paragraph5')));
    }

    public function testCloseTag()
    {
        $this->assertSame('</p>', Html::closeTag('p'));
    }

    public function testTag()
    {
        $this->assertSame('<p></p>', Html::tag('p'));
        $this->assertSame('<p>Hello world</p>',
        Html::tag('p', 'Hello world'));
        $this->assertSame('<p class="text" id="something">Hello world</p>',
        Html::tag('p', 'Hello world', array('class' => 'text', 'id' => 'something')));
    }

    public function testSelfTag()
    {
        $this->assertSame('<input />', Html::selfTag('input'));
        $this->assertSame('<input type="checkbox" checked="checked" />',
        Html::selfTag('input', array('type' => 'checkbox', 'checked')));
    }

    public function testInputText()
    {
        $expected = '<input type="text" id="test" name="test" value="" />';
        $this->assertSame($expected, Html::input('text', 'test'));
        $expected = '<input type="text" id="other-id" name="test" value="foo" class="text-input" />';
        $this->assertSame($expected, Html::input('text', 'test', 'foo', array('id' => 'other-id', 'class' => 'text-input')));
    }

    /**
     * Passwords are not hidden by the Html. Use FormRow and Form to
     * avoid self-foot-shooting.
     */
    public function testInputPassword()
    {
        $expected = '<input type="password" id="pword" name="pword" value="secret" />';
        $this->assertSame($expected, Html::input('password', 'pword', 'secret'));
        $expected = '<input type="password" id="password" name="pword" value="secret" />';
        $this->assertSame($expected, Html::input('password', 'pword', 'secret', array('id' => 'password')));
    }

    public function testInputTextarea()
    {
        $expected = '<textarea id="comment" name="comment"></textarea>';
        $this->assertSame($expected, Html::input('textarea', 'comment'));
        $expected = '<textarea id="other-id" name="comment">Something</textarea>';
        $this->assertSame($expected, Html::input('textarea', 'comment', 'Something', array('id' => 'other-id')));
    }

    public function testAttributesThrowsExceptionForBadAttributes()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Html::attributes(null);
    }

    public function testLabel()
    {
        $expected = '<label for="username">Username</label>';
        $this->assertSame($expected, Html::label('username', 'Username'));
    }

    public function testLabelOverrideAttributes()
    {
        $expected = '<label for="username1">Username</label>';
        $this->assertSame($expected, Html::label('username', 'Username', array(
            'for' => 'username1'
        )));
    }

    public function testDuplicateAttributesRemoved()
    {
        $expected = ' id="tick" name="tick" checked="checked"';
        $attributes = Html::attributes(array(
            'id' => 'foo',
            'name' => 'bar',
            'id' => 'tick',
            'name' => 'tick',
            'checked' => 'checked',
            'checked'
        ));
        $this->assertSame($expected, $attributes);
    }

    public function testSelect()
    {
        $expected = '<select name="choice">';
        $expected .= '<option value="foo">Foo</option>';
        $expected .= '</select>';
        $this->assertSame($expected, Html::select('choice', array('Foo' => 'foo')));
    }

    public function testSelectWithSelected()
    {
        $expected = '<select name="choice">';
        $expected .= '<option value="foo">Foo</option>';
        $expected .= '<option value="bar" selected="selected">Bar</option>';
        $expected .= '</select>';
        $this->assertSame($expected, Html::select('choice', array('Foo' => 'foo', 'Bar' => 'bar'), 'bar'));
    }

    public function testSelectUsesLooseTypeChecking()
    {

        $expected = '<select name="choice">';
        $expected .= '<option value="0" selected="selected">Zero</option>';
        $expected .= '<option value="1">One</option>';
        $expected .= '</select>';
        $this->assertSame($expected, Html::select('choice', array('Zero' => 0, 'One' => 1), '0'));
    }

    public function testSelectMultiple()
    {
        $expected = '<select name="choice" multiple="multiple" class="foo">';
        $expected .= '<option value="foo">Foo</option>';
        $expected .= '<option value="bar" selected="selected">Bar</option>';
        $expected .= '<option value="baz" selected="selected">Baz</option>';
        $expected .= '</select>';
        $choices = array('Foo' => 'foo', 'Bar' => 'bar', 'Baz' => 'baz');
        $this->assertSame($expected, Html::select('choice', $choices, array('bar', 'baz'), true, array('class' => 'foo')));
    }

    public function testSelectThrowsExceptionForArrayWithoutMultiple()
    {
        $msg = 'Html::select() must be passed the "multiple" argument to use multiple selections';
        $this->setExpectedException('\InvalidArgumentException', $msg);
        Html::select('choice', array('foo'), array());
    }

}
