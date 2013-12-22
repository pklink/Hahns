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

$app->delete('/cars/id-[\d+:id]/now', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send([
        'message' => sprintf('removed card with id `%d`', $request->get('id'))
    ]);
});
