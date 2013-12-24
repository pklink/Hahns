<?php

/* @var \Hahns\Hahns $app */

$app->patch('/patch', function (\Hahns\Response\Json $response, \Hahns\Request $request) {
    return $response->send([
        'message' => 'home',
        'id'      => (int) $request->payload('id')
    ]);
});

$app->patch('/hello/[.+:name]', function (\Hahns\Response\Json $response, \Hahns\Request $request) {
    return $response->send([
        'message' => $request->payload('say')
    ]);
});
