<?php

namespace Reform\Helper;

/**
 * Html
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class Html
{

    public static function attributes($attributes = array())
    {
        if (!is_array($attributes)) {
            $type = gettype($attributes);
            throw new \InvalidArgumentException(
                "Html::attributes() must be passed an array, $type given."
            );
        }
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

    public static function openTag($tag, $attributes = array())
    {
        return '<' . $tag . self::attributes($attributes) . '>';
    }

    public static function closeTag($tag)
    {
        return '</' . $tag . '>';
    }

    public static function tag($tag, $content = null, $attributes = array())
    {
        return self::openTag($tag, $attributes) . $content . self::closeTag($tag);
    }

    public static function selfTag($tag, $attributes = array())
    {
        return '<' . $tag . self::attributes($attributes) . ' />';
    }

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
     * @param string $name       The name attribute of the select tag
     * @param array  $name       An array of keys and value to use as option tags.
     * @param string $selected   The value of the input to pre-select.
     * @param bool   $multiple   Allow for multiple selections
     * @param array  $attributes An array of html attributes.
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

    public static function label($for, $content = null, $attributes = array())
    {
        $attributes = array_merge(array('for' => $for), $attributes);

        return self::tag('label', $content, $attributes);
    }

}
