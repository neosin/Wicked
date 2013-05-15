<?php

// include wicked
require '../../wicked/bootstrap.php';

// create simple app
$simple = wicked\core\Router::simple();
$app = new wicked\App($simple);

// setup syn
$app['syn'] = new syn\MySQL('todolist');
$app['syn']->task->model('app\models\Task');
//$syn->synchronize();

// run app
$app->run();