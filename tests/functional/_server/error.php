<?php

/* @var \Hahns\Hahns $app */

$app->on(\Hahns\Hahns::EVENT_ERROR, function(\Exception $e) {
    echo get_class($e);
});


$app->get('/an-error', function () {
    throw new \Hahns\Exception\ErrorException();
});

$app->get('/fatal-error', function () {
    trigger_error('sada', E_ERROR);
});

