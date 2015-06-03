<?php

namespace Reform\Form;

use Reform\Helper\Html;
use Reform\Validation\Validator;
use Reform\Form\Row\AbstractRow;
use Reform\Form\Renderer\BootstrapRenderer;
use Reform\Form\Renderer\RendererInterface;
use Reform\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Form
 * @author Glynn Forrest me@glynnforrest.com
 **/
class Form
{
    protected $dispatcher;
    protected $default_renderer;
    protected $types = array();
    protected $action;
    protected $method;
    protected $attributes;
    protected $rows = array();
    protected $validator;
    protected $valid = false;
    protected $validator_built = false;
    protected $tags = array();

    public function __construct($action, $method = 'POST', $attributes = array())
    {
        $this->setHeader($action, $method, $attributes);
        $this->validator = new Validator();
        $this->init();
    }

    /**
     * Attach an EventDispatcher to this Form. Instances of the
     * FormEvent will be dispatched at various points:
     *
     * CREATE - sent when this method is called. For this reason it is
     * recommended to call this method immediately after
     * instantiation if using events.
     *
     * PRE_VALIDATE - sent before the form is validated.
     *
     * POST_VALIDATE - sent after the form has been validated.
     *
     * @param  EventDispatcherInterface $dispatcher
     * @return Form                     This Form instance
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->sendEvent(FormEvent::CREATE);

        return $this;
    }

    /**
     * Return an identifying token for this form.
     */
    public function getId()
    {
        return get_class($this);
    }

    /**
     * Set the default renderer used when rendering this form.
     *
     * @param  RendererInterface $renderer The renderer
     * @return Form              This Form instance
     */
    public function setDefaultRenderer(RendererInterface $renderer)
    {
        $this->default_renderer = $renderer;

        return $this;
    }

    /**
     * Get the default renderer used when rendering this form.
     *
     * @return RendererInterface $renderer The renderer
     */
    public function getDefaultRenderer()
    {
        if (!$this->default_renderer) {
            $this->default_renderer = new BootstrapRenderer();
        }

        return $this->default_renderer;
    }

    protected function init()
    {
        $this->registerType('text', 'Reform\Form\Row\TextRow');
        $this->registerType('checkbox', 'Reform\Form\Row\CheckboxRow');
        $this->registerType('hidden', 'Reform\Form\Row\HiddenRow');
        $this->registerType('number', 'Reform\Form\Row\NumberRow');
        $this->registerType('password', 'Reform\Form\Row\PasswordRow');
        $this->registerType('select', 'Reform\Form\Row\SelectRow');
        $this->registerType('submit', 'Reform\Form\Row\SubmitRow');
        $this->registerType('textarea', 'Reform\Form\Row\TextareaRow');
    }

    /**
     * Set the Validator used to validate this form.
     *
     * @param \Reform\Validation\Validator The validator
     * @return \Reform\Form\Form This Form instance
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get the Validator used to validate this form.
     *
     * @return \Reform\Validation\Validator The validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Set the action attribute of this Form.
     *
     * @param string $action The action.
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get the action attribute of this Form.
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the method attribute of this Form. An exception will be
     * throw if $method is not an allowed http method.
     *
     * @param string $method The method.
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);
        if ($method !== 'POST' && $method !== 'GET') {
            throw new \InvalidArgumentException("Invalid method passed to Form::setMethod: $method");
        }
        $this->method = $method;

        return $this;
    }

    /**
     * Get the method attribute of this Form.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the html attributes of this Form. All previous attributes will be
     * reset.
     *
     * @param array The attributes
     * @return Form This Form instance
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Add to the html attributes of this Form.
     *
     * @param array The attributes
     * @return Form This Form instance
     */
    public function addAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * Get the html attributes of this Form.
     *
     * @return array The attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Give a tag to this form.
     *
     * @param  string $tag   The name of the tag
     * @param  mixed  $value The value of the tag (optional)
     * @return Form   This form
     */
    public function addTag($tag, $value = false)
    {
        $this->tags[$tag] = $value;

        return $this;
    }

    /**
     * Remove a tag from this form.
     *
     * @param  string $tag The name of the tag
     * @return Form   This form
     */
    public function removeTag($tag)
    {
        unset($this->tags[$tag]);

        return $this;
    }

    /**
     * Check if this form has a tag.
     *
     * @param  string $tag The name of the tag
     * @return bool
     */
    public function hasTag($tag)
    {
        return isset($this->tags[$tag]);
    }

    /**
     * Get the value of a tag, or null if it doesn't exist.
     *
     * @param  string $tag The name of the tag
     * @return mixed  The value of the tag or null
     */
    public function getTag($tag)
    {
        return isset($this->tags[$tag]) ? $this->tags[$tag] : null;
    }

    /**
     * Get all tags given to this form.
     *
     * @return array The tags
     */
    public function getTags()
    {
        return array_keys($this->tags);
    }

    /**
     * Set the action, method and any additional attributes of the Form.
     *
     * @param string $action     The action.
     * @param string $method     The method.
     * @param array  $attributes The attributes.
     */
    public function setHeader($action, $method = 'POST', array $attributes = array())
    {
        $this->setAction($action);
        $this->setMethod($method);
        $this->setAttributes($attributes);

        return $this;
    }

    /**
     * Render the header of this Form as Html.
     */
    public function header()
    {
        $attributes = array('action' => $this->action, 'method' => $this->method);
        $attributes = array_merge($attributes, $this->attributes);

        return Html::openTag('form', $attributes);
    }

    /**
     * Render the entire Form.
     */
    public function render(RendererInterface $renderer = null)
    {
        $renderer = $renderer ?: $this->getDefaultRenderer();
        $form = $this->header();
        foreach ($this->rows as $row) {
            $form .= $row->render($renderer);
        }
        $form .= '</form>';

        return $form;
    }

    /**
     * Render a row. If no renderer is supplied, the default renderer on this
     * form will be used.
     *
     * @param string            $name     The name of the row
     * @param RendererInterface $renderer The renderer to use
     */
    public function row($name, RendererInterface $renderer = null)
    {
        $renderer = $renderer ?: $this->getDefaultRenderer();

        return $this->getRow($name)->render($renderer);
    }

    /**
     * Render the input of a row. If no renderer is supplied, the default
     * renderer on this form will be used.
     *
     * @param string            $name     The name of the row
     * @param RendererInterface $renderer The renderer to use
     */
    public function input($name, RendererInterface $renderer = null)
    {
        $renderer = $renderer ?: $this->getDefaultRenderer();

        return $this->getRow($name)->input($renderer);
    }

    /**
     * Create a new row and add it to the Form. If no label is
     * supplied a label will be guessed using the $name attribute.
     *
     * @param string      $type       A registered form row type
     * @param string      $name       The name of the row
     * @param string|null $label      The label to give the row
     * @param array       $attributes Any attributes to give the row
     */
    public function newRow($type, $name, $label = null, array $attributes = array())
    {
        if (!isset($this->types[$type])) {
            throw new \InvalidArgumentException(sprintf('Form type "%s" not registered', $type));
        }
        $class = $this->types[$type];
        $this->rows[$name] = new $class($name, $label, $attributes);

        return $this->rows[$name];
    }

    /**
     * Add a row to the Form.
     *
     * @param  AbstractRow $row The row
     * @return Form        This Form instance.
     */
    public function addRow(AbstractRow $row)
    {
        $this->rows[$row->getName()] = $row;

        return $this;
    }

    /**
     * Get the FormRow instance with name $name.
     *
     * @param string $name The name of the row
     */
    public function getRow($name)
    {
        if (!isset($this->rows[$name])) {
            throw new \InvalidArgumentException(
                "Attempting to access unknown form row '$name'"
            );
        }

        return $this->rows[$name];
    }

    /**
     * Get a list of field names in this form.
     *
     * @return array An array of field names.
     */
    public function getFields()
    {
        return array_keys($this->rows);
    }

    /**
     * Set the value of a row.
     *
     * @param string $name  The name of the row
     * @param mixed  $value The value
     *
     * @return Form This Form instance.
     */
    public function setValue($name, $value)
    {
        $this->getRow($name)->setValue($value);

        return $this;
    }

    /**
     * Set the value of multiple rows.
     *
     * @param array $values         The array of values
     * @param bool  $ignore_unknown Whether to ignore any unknown form rows
     */
    public function setValues(array $values = array(), $ignore_unknown = false)
    {
        foreach ($values as $name => $value) {
            try {
                $this->getRow($name)->setValue($value);
            } catch (\InvalidArgumentException $e) {
                if (!$ignore_unknown) {
                    throw $e;
                }
            }
        }

        return $this;
    }

    /**
     * Get the value of a row.
     *
     * @param string $name The name of the row
     *
     * @return mixed The value
     */
    public function getValue($name)
    {
        return $this->getRow($name)->getValue();
    }

    /**
     * Get the values of all rows.
     */
    public function getValues()
    {
        $values = array();
        foreach ($this->rows as $name => $row) {
            $values[$name] = $row->getValue();
        }

        return $values;
    }

    /**
     * Add multiple errors to this Form. $errors should be an array of
     * keys and values, where a key is a name of a FormRow and the
     * value is the error message.
     *
     * @param array $errors         The array of errors
     * @param bool  $ignore_unknown Whether to ignore any unknown form rows
     */
    public function setErrors(array $errors = array(), $ignore_unknown = false)
    {
        foreach ($errors as $name => $error) {
            try {
                $this->getRow($name)->setError($error);
            } catch (\InvalidArgumentException $e) {
                if (!$ignore_unknown) {
                    throw $e;
                }
            }
        }

        return $this;
    }

    /**
     * Get all of the errors attached to this Form.
     *
     * @return array An array of errors
     */
    public function getErrors()
    {
        return array_map(function ($row) {
            return $row->getError();
            },
        $this->rows);
    }

    public function __call($method, array $args)
    {
        //call newRow with the method name as the first argument
        array_unshift($args, $method);

        return call_user_func_array(array($this, 'newRow'), $args);
    }

    /**
     * Add the rules from all rows to form a complete Validator for
     * this form. The Validator can only be built once - no additional
     * rules may be added after building.
     */
    public function buildValidator()
    {
        if ($this->validator_built) {
            return $this->validator;
        }

        foreach ($this->rows as $name => $row) {
            foreach ($row->getRules() as $rule) {
                $this->validator->addRule($name, $rule);
            }
            $row->disableRules();
        }
        $this->validator_built = true;

        return $this->validator;
    }

    /**
     * Flatten a multidimensional array into a one-dimensional array, using
     * square brackets to show the structure of the original array.
     */
    protected function flattenArray(array $values, $previous = '')
    {
        $result = array();
        foreach ($values as $key => $value) {
            if ($previous) {
                $key = $previous.'['.$key.']';
            }
            if (is_array($value)) {
                $result = $result + $this->flattenArray($value, $key);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Submit the form with an array of values. Execution happens in
     * the following order:
     *
     * All rows are given the values to assign values to themselves.
     *
     * A pre-validate event is sent.
     *
     * The validator is 'built', disabling the addition of any more
     * validation rules.
     *
     * The values are checked for validity, setting the form's
     * isValid() state.
     *
     * The post-validate event is sent.
     *
     * @param  array  $values The submitted values.
     * @return Result A validation result.
     */
    public function submitForm(array $values)
    {
        //send a flattened version of the values to each of the
        //rows so they can assign values to themselves.
        $flattened = $this->flattenArray($values);
        foreach ($this->rows as $row) {
            $row->submitForm($flattened);
        }

        //assigning values before sending the pre-validate event
        //allows for modification
        $this->sendEvent(FormEvent::PRE_VALIDATE);

        $this->buildValidator();

        //validate
        $result = $this->validator->validateForm($this->getValues());
        $this->valid = $result->isValid();
        if (!$this->valid) {
            $this->setErrors($result->getFirstErrors());
        }

        $this->sendEvent(FormEvent::POST_VALIDATE);

        return $result;
    }

    /**
     * Handle an incoming Request, calling submit() if the form has
     * been submitted.
     *
     * @param Request $request The incoming request
     */
    public function handle(Request $request)
    {
        //get the correct method
        if ($this->method === 'GET') {
            $values = $request->query->all();
        } else {
            $values = $request->request->all();
        }

        // The form is submitted if at least one field is present
        if (count(array_intersect_key($values, $this->rows)) > 0) {
            return $this->submitForm($values);
        }
    }

    protected function sendEvent($event_name)
    {
        if (!$this->dispatcher) {
            return;
        }

        if ($this->dispatcher->hasListeners($event_name)) {
            $event = new FormEvent($this);
            $this->dispatcher->dispatch($event_name, $event);
        }
    }

    /**
     * Check if this form is submitted and has passed validation.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Register a new row type to be available when calling
     * newRow(). $class must extend Reform\Form\Row\AbstractRow.
     *
     * @string $type The name of the type, e.g. 'text'
     * @string $class The class name, e.g. 'Reform\Form\Row\Text'
     */
    public function registerType($type, $class)
    {
        $this->types[$type] = $class;
    }

    /**
     * Enable this Form to use file uploads by adding
     * enctype="multipart/form-data".
     *
     * @return Form This form instance
     */
    public function useFiles()
    {
        return $this->addAttributes(array('enctype' => 'multipart/form-data'));
    }
}
