<?php

require __DIR__ . '/../vendor/autoload.php';

// create app
$app = new \Hahns\Hahns();

require '_server/json-get.php';
require '_server/json-post.php';

// run app
$app->run();
