<?php

/* @var \Hahns\Hahns $app */

$app->delete('/delete', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send([
        'message' => 'home',
        'id'      => (int) $request->payload('id')
    ]);
});

$app->delete('/hello/[.+:name]', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send([
        'message' => $request->payload('say')
    ]);
});
