<?php

namespace Reform\Tests\Helper;

use Reform\Helper\Html;

/**
 * HtmlTest
 * @author Glynn Forrest me@glynnforrest.com
 **/
class HtmlTest extends \PHPUnit_Framework_TestCase
{

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
        $expected = '<select id="choice" name="choice">';
        $expected .= '<option value="foo">Foo</option>';
        $expected .= '</select>';
        $this->assertSame($expected, Html::select('choice', array('Foo' => 'foo')));
    }

    public function testSelectWithSelected()
    {
        $expected = '<select id="choice" name="choice">';
        $expected .= '<option value="foo">Foo</option>';
        $expected .= '<option value="bar" selected="selected">Bar</option>';
        $expected .= '</select>';
        $this->assertSame($expected, Html::select('choice', array('Foo' => 'foo', 'Bar' => 'bar'), 'bar'));
    }

    public function testSelectUsesLooseTypeChecking()
    {

        $expected = '<select id="choice" name="choice">';
        $expected .= '<option value="0" selected="selected">Zero</option>';
        $expected .= '<option value="1">One</option>';
        $expected .= '</select>';
        $this->assertSame($expected, Html::select('choice', array('Zero' => 0, 'One' => 1), '0'));
        $this->assertSame($expected, Html::select('choice', array('Zero' => 0, 'One' => 1), 0));
    }

    public function testSelectWithStrangeTypes()
    {
        $choices = array(1.1 => 1, 2 => 2, '3' => 3, 4);
        $expected = '<select id="choice" name="choice">';
        $expected .= '<option value="1" selected="selected">1</option>';
        $expected .= '<option value="2">2</option>';
        $expected .= '<option value="3">3</option>';
        $expected .= '<option value="4">4</option>';
        $expected .= '</select>';
        $this->assertSame($expected, Html::select('choice', $choices, '1'));
        $this->assertSame($expected, Html::select('choice', $choices, 1));
    }

    public function testSelectMultiple()
    {
        $expected = '<select id="choice" name="choice" multiple="multiple" class="foo">';
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

    public function testInputAttributesAreEscaped()
    {
        $expected = '<input type="text" id="foo" name="foo" value="&quot; /&gt; &lt;p&gt;Gotcha!&lt;/p&gt;" />';
        $this->assertSame($expected, Html::input('text', 'foo', '" /> <p>Gotcha!</p>'));
    }

    public function testInputKeylessAttributesAreEscaped()
    {
        $expected = '<input type="text" id="foo" name="foo" value="" /&gt;&lt;p&gt;Götcha!&lt;/p&gt;="/&gt;&lt;p&gt;Götcha!&lt;/p&gt;" />';
        $this->assertSame($expected, Html::input('text', 'foo', null, array('/><p>Götcha!</p>')));
    }

    public function testTextareaContentIsEscaped()
    {
        $expected = '<textarea id="foo" name="foo">&lt;/textarea&gt;&lt;p&gt;Gotcha!&lt;/p&gt;</textarea>';
        $this->assertSame($expected, Html::input('textarea', 'foo', '</textarea><p>Gotcha!</p>'));
    }

    public function attributeAddProvider()
    {
        return array(
            array('foo', 'bar', 'foo bar'),
            array('foo bar', 'baz', 'foo bar baz'),
            array(' foo bar', 'baz', 'foo bar baz'),
            array('foo bar ', 'baz', 'foo bar baz'),
            array('foo bar', ' baz', 'foo bar baz'),
            array('foo bar ', 'baz ', 'foo bar baz'),
            array('foo    bar ', 'baz ', 'foo bar baz'),
            array('foo bar', 'foo', 'foo bar'),
            array('', 'foo bar', 'foo bar'),
            array('foo bar', '', 'foo bar'),
            array('', '', ''),
            array('a lot of class names', 'some more class names', 'a lot of class names some more'),
            array('a     lot of     class names    ', '    some   more    class names', 'a lot of class names some more')
        );
    }

    /**
     * @dataProvider attributeAddProvider()
     */
    public function testAddToAttribute($attribute, $addition, $expected)
    {
        $this->assertSame($expected, Html::addToAttribute($attribute, $addition));
    }

    /**
     * @dataProvider attributeAddProvider()
     */
    public function testAddToAttributeArray($attribute, $addition, $expected)
    {
        $attributes = array(
            'id' => 'my-element',
            'class' => $attribute
        );
        $expected = array(
            'id' => 'my-element',
            'class' => $expected
        );
        $this->assertSame($expected, Html::addToAttributeArray($attributes, 'class', $addition));
    }

    public function notSetAttributeAddProvider()
    {
        return array(
            array('foo bar', 'foo bar'),
            array(' foo bar baz', 'foo bar baz'),
            array(' foo ', 'foo'),
            array('', ''),
            array(' ', ''),
            array('  ', ''),
        );
    }

    /**
     * @dataProvider notSetAttributeAddProvider()
     */
    public function testAddToAttributeArrayNotSet($addition, $expected)
    {
        $attributes = array(
            'id' => 'my-element'
        );
        $expected = array(
            'id' => 'my-element',
            'class' => $expected
        );
        $this->assertSame($expected, Html::addToAttributeArray($attributes, 'class', $addition));
    }

}
