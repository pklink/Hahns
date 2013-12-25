<?php

/* @var \Hahns\Hahns $app */

$app->on(\Hahns\Hahns::EVENT_ERROR, function(\Hahns\Hahns $app, \Exception $e) {
    echo get_class($e);
});


$app->get('/an-error', function () {
    throw new \Hahns\Exception\ErrorException();
});

