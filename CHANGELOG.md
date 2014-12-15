Changelog
=========

### 0.4.0 2014-12-15

Version 0.4 is a big refactoring of how rows are created and rendered.

* Form rows are now separated into separate classes. Currently
  implemented types are text, textarea, password, checkbox, hidden,
  number, select and submit.
* Select input supports multiple values.
* Introduction of RendererInterface for rendering form rows. Three
  renderers are supported so far - BootstrapRenderer,
  FoundationRenderer and BasicRenderer.
* Forms can have arbitrary tags applied to them, with or without values.
* Adding Honeypot row and HoneypotListener for catching spam form
  submissions. Forms with a captured honeypot field have the
  HoneypotListener::CAUGHT tag applied to them. Optionally,
  glynnforrest/blockade can be used to handle spam submissions
  automatically.
* Importing CSRF functionality away from
  glynnforrest/blockade. Blockade is now only required to have CSRF
  violations handled automatically. Forms with a CSRF violation have
  the CrsfListener::INVALID tag applied to them.
* Adding some examples.

### 0.3.1 2014-08-18

By using the Blockade security library, this version uses form events
to add automatic CSRF protection for forms. The CsrfListener listens
for when a form is submitted and checks the token using Blockade's
CrsfManager. An exception is thrown if the submitted token is invalid.

* Adding CSRF protection using glynnforrest/blockade.
* Adding Alpha validation rule.
* Making Form#addRow() public.

### 0.3.0 2014-04-21

Version 0.3 introduces a backwards incompatible change when creating
form rows. A FormRow now accepts name, label and attributes on
instantiation, instead of the name, value and attributes. This creates
a distinct separation between presenting the form and the actual value
of the form. To set the value of the row, call setValue().

* Backwards incompatible break - FormRow now takes the label instead
  of the value on creation.
* Adding Length validation rule.
* Adding more tests, particularly for validation rules.

### 0.2.0 2014-04-12

This release cuts down on required libraries.

* Symfony EventDispatcher is now optional.
* Removing dependency on Stringy.

### 0.1.2 2014-04-12

* Html tag attributes can now be added to the main form tag and form rows.
* Adding Url validation rule.
* Adding Form#useFiles().

### 0.1.1 2014-04-02

Adding Form#getId().

### 0.1.0 2014-04-02

Initial release. A summary of what's included so far:

* A Validator that uses instances of Rules to check incoming form
  data. Current rules are AlphaNumeric, Email, Integer, Matches,
  Range, Regex and Required.
* A FormRow represents a single row in a form, including the label,
  input and error message. This row can be customized before being
  rendered.
* A Form class that has a number of these FormRows, and may have a
  Validator. A Symfony Request can be passed to the handle() method to
  check if the form has been submitted and passes any of the
  validation rules.
* A FormEvent class to customize a form at various points in its
  lifetime.
* A Html helper, used internally by the Form class as well as offering
  standalone HTML helper methods.
