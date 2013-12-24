<?php

/* @var \Hahns\Hahns $app */

$app->parameter('\\stdClass', function() {
    $obj = new stdClass();
    $obj->test = 'yup';
    return $obj;
});

$app->get('/parameters', function (\stdClass $obj) {
    return $obj->test;
});

$app->get('/parameters/invalid', function (\Hahns\Hahns $app) {
    return get_class($app) . 'produce an error';
});