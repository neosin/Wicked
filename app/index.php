<?php

// get wicked
require '../wicked/bootstrap.php';

// create app
$app = new wicked\App();

// add db
// $app['syn'] = new syn\MySQL('yourdb');

// 404
$app->on('404', function($app, $message){
    // $app->mog->go('/not/found');
    die($message);
});

$app->run();