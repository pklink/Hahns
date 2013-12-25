<?php

/* @var \Hahns\Hahns $app */

$app->service('test-service', function () {
    $obj = new stdClass();
    $obj->test = 'yep';
    return $obj;
});

$app->get('/services/yep', function (Hahns\Services $services) {
    return $services->get('test-service')->test;
});