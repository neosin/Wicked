<?php

// include wicked
require '../../wicked/bootstrap.php';

// create app
$app = new wicked\App();

$app->on('render', function($app, $view){
    $view->set('var', 'plop');
});


$app->run();