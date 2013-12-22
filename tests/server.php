<?php

require '../vendor/autoload.php';

// create app
$app = new \Hahns\Hahns();

// include json-get routings
require '_server/json-get.php';

// run app
$app->run();
