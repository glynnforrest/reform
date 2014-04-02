# Reform
### Forms using HttpFoundation that render and validate with ease.
### By Glynn Forrest

[![Build Status](https://travis-ci.org/glynnforrest/reform.png)](https://travis-ci.org/glynnforrest/reform)

Intro
-----
As web developers, forms are one of the most common things we work
with. Unfortunately, for the most part, dealing with them
sucks. Wouldn't it be cool if rendering a form could be as easy as
this?

    $form = new Form('/login');
    $form->text('username')
    ->password('password')
    ->submit('login');

    echo $form;

But what about handling the form submission, and what about validating
those fields?

Easy - just add some validation rules, and tell the form to handle a
Symfony HttpFoundation Request object:

    $form->check('username', new Rule\Required('Did you forget your name?'))
         ->check('username', new Rule\Regex('`[A-z.]+`'))
         ->check('password', new Rule\Required());

    //the form can validate itself by looking at a Symfony Request object
    $request = Request::createFromGlobals();
    $form->handle($request);

    //check if the form is valid
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

How easy was that?!?

Installation
------------
Reform is installed via Composer. To add it to your project, simply add it to your
composer.json file:

	{
		"require": {
			"glynnforrest/reform": "0.1.*"
		}
	}

And run composer to update your dependencies:

	$ curl -s http://getcomposer.org/installer | php
	$ php composer.phar update

License
-------

MIT
