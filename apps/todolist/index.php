<?php

// include wicked
require '../../wicked/bootstrap.php';

// create simple app
$app = wicked\App::simple();

// setup syn
$syn = new syn\MySQL('todolist');
$syn->task->model('app\models\Task');
//$syn->synchronize();

// add to app
$app->set('syn', $syn);

// run app
$app->run();