<?php

/* @var \Hahns\Hahns $app */

$app->post('/post', function (\Hahns\Response\Json $response, \Hahns\Request $request) {
    return $response->send([
        'message' => 'home',
        'id'      => (int) $request->post('id')
    ]);
});

$app->post('/hello/[.+:name]', function (\Hahns\Response\Json $response, \Hahns\Request $request) {
    return $response->send([
        'message' => $request->post('say')
    ]);
});
