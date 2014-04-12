<?php

namespace Reform\Helper;

/**
 * Html
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class Html
{
    public static function escape($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8', false);
    }

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
                if (!array_key_exists($value, $attributes)) {
                    $key = $value;
                } else {
                    continue;
                }
            }
            $text[] = $key . '="' . $value . '"';
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

    public static function input($type, $name, $value = null, $attributes = array())
    {
        if ($type === 'textarea') {
            $attributes = array_merge(array(
                'id' => $name,
                'name' => $name
            ), $attributes);

            return self::tag('textarea', $value, $attributes);
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
     * to pre-select one of the options.
     *
     * @param string $name     The name attribute of the select tag
     * @param array  $name     An array of keys and value to use as option tags.
     * @param string $selected The value of the input to pre-select.
     * @param attributes array An array of html attributes.
     */
    public static function select($name, array $values, $selected = null, $attributes = array())
    {
        $attributes['name'] = $name;
        $text = self::openTag('select', $attributes);
        foreach ($values as $k => $v) {
            if (is_numeric($k)) {
                $k = $v;
            }
            if ($v === $selected) {
                $attributes = array('value' => $v, 'selected');
            } else {
                $attributes = array('value' => $v);
            }
            $text .= self::tag('option', $k, $attributes);
        }
        $text .= self::closeTag('select');

        return $text;
    }

    public static function js($src, $attributes = array())
    {
        $attributes = array_merge(array(
            'type' => 'text/javascript',
            'src' => $src), $attributes);

        return self::tag('script', null, $attributes) . PHP_EOL;
    }

    public static function css($src, $attributes = array())
    {
        $attributes = array_merge(array(
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'href' => $src), $attributes);

        return self::selfTag('link', $attributes) . PHP_EOL;
    }

    public static function label($for, $content = null, $attributes = array())
    {
        $attributes = array_merge(array('for' => $for), $attributes);

        return self::tag('label', $content, $attributes);
    }

}
