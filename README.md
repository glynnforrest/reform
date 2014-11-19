# Reform

[![Build Status](https://travis-ci.org/glynnforrest/reform.png)](https://travis-ci.org/glynnforrest/reform)

Create PHP forms that render and validate with ease. Create a form,
add rows and validation, then simply echo it to the browser.

* Many row types and validation rules. Easily add custom types to
  match your requirements.
* Use different renderers to apply styles to the form, e.g. Bootstrap,
  without changing code.
* Integration with
  [Symfony HttpFoundation](https://github.com/symfony/HttpFoundation)
  to automatically submit forms.
* Security measures like honeypot fields and timers, and integration
  with [Blockade](https://github.com/glynnforrest/blockade) for
  automatic CSRF protection.
* Use events to customize how forms behave.

## Quickstart

A simple form with a username and password field.

```php
$form = new Reform\Form\Form('/login');
$form->text('username');
$form->password('password');
$form->submit('login');

echo $form;
```

Now with some validation.

```php
$form = new Reform\Form\Form('/login');
$form->text('username')
    ->addRule(new Rule\Required('Did you forget your name?'))
    ->addRule(new Rule\Regex('`[A-z.]+`'))
$form->password('password')
    ->addRule(new Rule\Required('Did you forget your name?'));
$form->submit('login');
```

Submit the form automatically by using a Symfony Request object. If
the correct HTTP method was used and all fields have passed the
required validation, the form is considered valid.

Valid or not, after a submission the fields are populated with the
submitted data.

```php
$request = Request::createFromGlobals();
$form->handle($request);

if ($form->isValid()) {
    //the form was submitted correctly - the correct http method was
    //used and all validation rules passed

    //perform the login and redirect
}
//the form was either not submitted or failed the validation. $form
//now has any submitted parameters bound to it, so all we need to do
//is echo the form again, and any values and errors will be added
//automatically.

echo $form;
```

See `docs/` for further documentation.

## Installation

Install using composer:

```json
{
    "require": {
        "glynnforrest/reform": "0.3.*"
    }
}
```

## License

MIT, see LICENSE for details.

Copyright 2014 Glynn Forrest
