<?php

/* @var \Hahns\Hahns $app */

$app->put('/put', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send([
        'message' => 'home',
        'id'      => (int) $request->payload('id')
    ]);
});

$app->put('/hello/[.+:name]', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send([
        'message' => $request->payload('say')
    ]);
});
