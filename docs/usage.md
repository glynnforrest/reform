# Usage

Create a form, specifying the action in the constructor.

```php
$form = new Form('/login');
```

Use the second parameter to specify the method (default is POST).

```php
$form = new Form('/search', 'GET');
```

Use `newRow()` and `addRow` to add rows.

```php
$form->newRow('text', 'username');
$form->newRow('password', 'password');

// OR

$form->addRow(new Text('username'));
$form->addRow(new Password('password'));
```

Both `newRow()` and `addRow()` add a row to the form, however
`newRow()` takes the type of row to create, whereas `addRow()` expects
an instance of `Reform\Form\Row\AbstractRow`.

### Registering rows

`newRow()` only knows about rows that have been registered. Use
`registerType()` to tell the form about a type of row, specifying the
name of the type and the class name. The row must extend
`Reform\Form\Row\AbstractRow`.

```php
$form->registerType('custom', 'MyApp\Form\Row\Custom');
$form->newRow('custom', 'custom1');
$form->newRow('custom', 'custom2');

// is the same as
$form->addRow(new Custom('custom1'));
$form->addRow(new Custom('custom2'));
```

The following types are registered automatically:

* text -> `Reform\Form\Row\Text`
* checkbox -> `Reform\Form\Row\Checkbox`
* hidden -> `Reform\Form\Row\Hidden`
* number -> `Reform\Form\Row\Number`
* password -> `Reform\Form\Row\Password`
* select -> `Reform\Form\Row\Select`
* submit -> `Reform\Form\Row\Submit`
* textarea -> `Reform\Form\Row\Textarea`
