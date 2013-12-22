<?php

/* @var \Hahns\Hahns $app */

$app->register('bla', function () {
    $obj = new stdClass();
    $obj->hello = 'world';
    return $obj;
});

$app->get('/service', function (\Hahns\Response\JsonImpl $response, \Hahns\ServiceHolder $container) {
    return $response->send([
        'message' => $container->get('bla')->hello
    ]);
});
