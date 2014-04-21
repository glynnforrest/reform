<?php

namespace Reform\Form;

use Reform\Helper\Html;

/**
 * FormRow
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FormRow extends AbstractFormRow
{

    /**
     * Render the input attached to this FormRow as Html.
     */
    public function input()
    {
        if ($this->type === 'select') {
            $selected = $this->value;

            return Html::select($this->name, $this->choices, $selected, $this->attributes);
        }

        switch ($this->type) {
        //if input is a checkbox and it has a truthy value, add
        //checked to attributes before render
        case 'checkbox':
            if ($this->value !== null) {
                $this->addAttributes(array('checked'));
            }
            //no matter what, the value of the input is 'checked'
            $value = 'checked';
            break;
        case 'password':
            //remove the value from all password fields
            $value = null;
            break;
        case 'submit':
            //add a value to the submit button if there is none
            if ($this->value === null) {
                $value = $this->sensible($this->name);
                break;
            }
            $value = $this->value;
            break;
        case 'date':
            //parse the value as a datetime
            //add day
            //add month
            //add year
        default:
            $value = $this->value;
        }

        return Html::input($this->type, $this->name, $value, $this->attributes);
    }
    /**
     * Render this FormRow instance as Html, with label, input and
     * error message, if available.
     */
    public function render()
    {
        //a hidden field should be just an input
        if ($this->type == 'hidden') {
            return $this->input();
        }
        //a submit field should be just an input, but with extra html
        //set in $this->row_string
        if ($this->type == 'submit') {
            $str = str_replace(':error', '', $this->row_string);
            $str = str_replace(':label', '', $str);
            $str = str_replace(':input', $this->input(), $str);

            return $str;
        }
        //otherwise, substitute :label, :input and :error into
        //$this->row_string
        $str = str_replace(':label', $this->label(), $this->row_string);
        $str = str_replace(':error', $this->error(), $str);
        $str = str_replace(':input', $this->input(), $str);

        return $str;
    }

    public static function getSupportedTypes()
    {
        return array(
            'checkbox',
            'hidden',
            'password',
            'radio',
            'select',
            'submit',
            'text',
            'textarea',
        );
    }

}
