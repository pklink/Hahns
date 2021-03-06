<?php

/* @var \Hahns\Hahns $app */

$app->service('bla', function () {
    $obj = new stdClass();
    $obj->hello = 'world';
    return $obj;
});

$app->get('/service', function (\Hahns\Response\Json $response, \Hahns\ServiceHolder $container) {
    return $response->send([
        'message' => $container->get('bla')->hello
    ]);
});
