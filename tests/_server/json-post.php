<?php

/* @var \Hahns\Hahns $app */

$app->post('/', function (\Hahns\Response\JsonImpl $response) {
    return $response->send([
        'message' => 'home',
        'id'      => (int) $_POST['id']
    ]);
});

$app->post('/hello/[.+:name]', function (\Hahns\Response\JsonImpl $response) {
    return $response->send([
        'message' => $_POST['say']
    ]);
});
