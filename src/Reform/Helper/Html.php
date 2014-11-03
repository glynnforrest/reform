<?php

namespace Reform\Helper;

/**
 * Html
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class Html
{

    /**
     * Create a string of attributes to use in an HTML tag.
     *
     * @param  array  $attributes An array of keys and values
     * @return string The attributes with a leading space
     */
    public static function attributes(array $attributes = array())
    {
        $text = array();
        foreach ($attributes as $key => $value) {
            //if we have numeric keys (e.g. checked), set the value as
            //the $key (e.g. checked="checked"), but only if it
            //doesn't exist already
            if (is_numeric($key)) {
                $value = htmlspecialchars($value);
                if (!isset($attributes[$value])) {
                    $text[] = $value . '="' . $value . '"';
                }
                continue;
            }
            $text[] = $key . '="' . htmlspecialchars($value) . '"';
        }

        return empty($text) ? '' : ' ' . implode(' ', $text);
    }

    /**
     * Add to the value of an attribute, checking for duplicates. This
     * could be used to add css classes when css classes already
     * exist.
     *
     * @param string $attribute The value of the attribute
     * @param string $addition  The text to add
     */
    public static function addToAttribute($attribute, $addition)
    {
        $new = array_unique(array_merge(explode(' ', trim($attribute)), explode(' ', trim($addition))));

        return trim(implode(' ', $new));
    }

    /**
     * Add to the value of an attribute in an array of attributes. The
     * attribute will be created if it doesn't exist.
     *
     * @param array  $attributes The attributes
     * @param string $name       The name of the attribute to add to
     * @param string $addition   The text to add
     */
    public static function addToAttributeArray(array $attributes, $name, $addition)
    {
        $attribute = isset($attributes[$name]) ? $attributes[$name] : '';
        $attributes[$name] = self::addToAttribute($attribute, $addition);

        return $attributes;
    }

    /**
     * Open an HTML tag.
     *
     * @param  string $tag        The name of the tag
     * @param  array  $attributes An array of html attributes
     * @return string The tag
     */
    public static function openTag($tag, array $attributes = array())
    {
        return '<' . $tag . self::attributes($attributes) . '>';
    }

    /**
     * Close an HTML tag.
     *
     * @param string The name of the tag
     * @return string The tag
     */
    public static function closeTag($tag)
    {
        return '</' . $tag . '>';
    }

    /**
     * Create an HTML tag.
     *
     * @param  string $tag        The name of the tag
     * @param  string $content    The HTML content of the tag
     * @param  array  $attributes An array of html attributes
     * @return string The tag
     */
    public static function tag($tag, $content = null, array $attributes = array())
    {
        return self::openTag($tag, $attributes) . $content . self::closeTag($tag);
    }

    /**
     * Create a self closing HTML tag.
     *
     * @param  string $tag        The name of the tag
     * @param  array  $attributes An array of html attributes
     * @return string The tag
     */
    public static function selfTag($tag, array $attributes = array())
    {
        return '<' . $tag . self::attributes($attributes) . ' />';
    }

    /**
     * Create an input tag. If $type is 'textarea', a textarea tag
     * will be rendered instead.
     *
     * @param  string $type       The type of the input
     * @param  array  $name       The name of the input
     * @param  string $value      The value of the input
     * @param  array  $attributes An array of html attributes
     * @return string The input tag
     */
    public static function input($type, $name, $value = null, array $attributes = array())
    {
        if ($type === 'textarea') {
            $attributes = array_merge(array(
                'id' => $name,
                'name' => $name
            ), $attributes);

            return self::tag('textarea', htmlspecialchars($value), $attributes);
        }
        $attributes = array_merge(array(
            'type' => $type,
            'id' => $name,
            'name' => $name,
            'value' => $value
        ), $attributes);

        return self::selfTag('input', $attributes);
    }

    /**
     * Create a select tag with child option tags. Option tags are
     * created from the $values array, where the keys become the
     * content of the tag (the name visible in the browser) and values
     * become the value of the tag (the value attribute). Pass
     * $selected, where $selected is a value (not a key) in $values,
     * to pre-select one of the options. Select more than one with an
     * array and by setting the $multiple attribute.
     *
     * @param  string $name       The name attribute of the select tag
     * @param  array  $values     An array of keys and value to use as option tags
     * @param  string $selected   The value of the option to pre-select
     * @param  bool   $multiple   Allow for multiple selections
     * @param  array  $attributes An array of html attributes
     * @return string The select tag
     */
    public static function select($name, array $values, $selected = null, $multiple = false, array $attributes = array())
    {
        $attr = $multiple ? array('name' => $name, 'multiple') : array('name' => $name);
        $attributes = array_merge($attr, $attributes);

        if (is_array($selected) && !$multiple) {
            throw new \InvalidArgumentException('Html::select() must be passed the "multiple" argument to use multiple selections');
        }

        $text = self::openTag('select', $attributes);

        foreach ($values as $k => $v) {
            if (is_numeric($k)) {
                $k = $v;
            }

            //this comparison and in_array do not check types intentionally
            if ($v == $selected) {
                $attributes = array('value' => $v, 'selected');
            } elseif (is_array($selected)) {
                $attributes = in_array($v, $selected) ? array('value' => $v, 'selected') : array('value' => $v);
            } else {
                $attributes = array('value' => $v);
            }

            $text .= self::tag('option', $k, $attributes);
        }
        $text .= self::closeTag('select');

        return $text;
    }

    /**
     * Create a label tag.
     *
     * @param  string $for        The name of the input the label is for
     * @param  string $content    The content of the label, if any
     * @param  array  $attributes An array of html attributes
     * @return string The label tag
     */
    public static function label($for, $content = null, array $attributes = array())
    {
        $attributes = array_merge(array('for' => $for), $attributes);

        return self::tag('label', $content, $attributes);
    }

}
