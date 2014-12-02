# Reform

[![Build Status](https://travis-ci.org/glynnforrest/reform.png)](https://travis-ci.org/glynnforrest/reform)

The Reform library makes it easy to create forms in PHP. Create a
form, add rows and validation, then simply echo it to the
browser. Everything else is done automatically - checking for
submissions, validating data, setting values, creating labels and
error messages, handling CSRF...

For greater control, the form can be rendered row-by-row, or even in
individual pieces. You can use only a few features without the rest
getting in the way.

## Features

* Many row types and validation rules. It is trivial to add custom
  types to match your requirements.
* Different renderers to apply styles to the form
  (e.g. Bootstrap). Changing the renderer can be done with a single
  line of code.
* Integration with
  [Symfony HttpFoundation](https://github.com/symfony/HttpFoundation)
  to automatically submit forms.
* Security measures like honeypot fields, timers, and CSRF
  protection. Add the
  [Blockade](https://github.com/glynnforrest/blockade) security
  library to have these exceptions handled automatically.
* Events to customize how forms behave.

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
    ->addRule(new Rule\Required());
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
    login_user();
    redirect_to_home_page();
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

## Viewing the examples

```bash
composer install
cd examples/
bower install
php -S localhost:8000
```
Then visit `localhost:8000` in your web browser.

These instructions assume you have composer, bower and PHP 5.4 installed.

## License

MIT, see LICENSE for details.

Copyright 2014 Glynn Forrest
