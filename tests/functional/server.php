<?php

require __DIR__ . '/../../vendor/autoload.php';

// create app
$app = new \Hahns\Hahns();

require '_server/json-get.php';
require '_server/json-get-serviceholder.php';
require '_server/json-post.php';
require '_server/json-put.php';
require '_server/json-patch.php';
require '_server/json-delete.php';
require '_server/header.php';
require '_server/services.php';

$app->notFound(function () {
    echo 1;
});

// run app
$app->run();
