<?php

/*
   Reform examples

   This is all in one file for the sake of example only. Please do not
   write spaghetti code like this!
 */

include '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Reform\Form\Renderer\BasicRenderer;
use Reform\Form\Form;
use Reform\Validation\Rule;

$form = new Form(null);
$form->text('text')->addRule(new Rule\Required());
$form->number('number');
$form->textarea('textarea');
$form->checkbox('checkbox');
$form->hidden('hidden');
$form->password('password');
$form->select('select')->setChoices(array('Apple' => 'apple', 'Orange' => 'orange', 'Grapes' => 'grapes'));
$form->select('multiple_select')->setChoices(array('Apple' => 'apple', 'Orange' => 'orange', 'Grapes' => 'grapes'))->setMultiple();
$form->submit('submit');

$request = Request::createFromGlobals();
$form->handle($request);

$renderer = isset($_GET['r']) ? $_GET['r'] : 'twbs';

switch ($renderer) {
case 'twbs':
    $css = 'bootstrap/dist/css/bootstrap.min.css';
    break;
default:
    $form->setDefaultRenderer(new BasicRenderer());
}

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Reform Examples</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <?php if (isset($css)): ?>
      <link rel="stylesheet" type="text/css" href="bower_components/<?=$css;?>" />
    <?php endif; ?>
    <style>
     #_spacer {
       width:900px;
       margin:0 auto;
     }
    </style>
  </head>
  <body>
    <div id="_spacer">
      <h1>Reform examples</h1>
      <ul>
        <li>
          <a href="?r=basic">Basic</a>
        </li>
        <li>
          <a href="?r=twbs">Bootstrap</a>
        </li>
        <li>
          <a href="?r=zurb">Zurb Foundation</a>
        </li>
        <li>
          <a href="?r=tables">Tables</a>
        </li>
      </ul>
      <?=$form->render();?>
    </div>
  </body>
</html>
