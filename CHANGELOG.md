Changelog
=========

### 0.1.2 2014-04-12

* Html tag attributes can now be added to the main form tag and form rows.
* Adding Url validation rule.
* Adding Form#useFiles().

### 0.1.1 2014-04-02

Adding Form#getId().

### 0.1.0 2014-04-02

Initial release. A summary of what's included so far:

* A Validator that uses instances of Rules to check incoming form data.
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
