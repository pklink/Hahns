<?php

/* @var \Hahns\Hahns $app */

$app->post('/post', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send([
        'message' => 'home',
        'id'      => (int) $request->post('id')
    ]);
});

$app->post('/hello/[.+:name]', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send([
        'message' => $request->post('say')
    ]);
});
