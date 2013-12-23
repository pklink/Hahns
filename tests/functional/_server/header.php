<?php

/* @var \Hahns\Hahns $app */

$app->get('/header/using/header/bli/blub', function (\Hahns\Response\JsonImpl $response) {
    $response->header('bli', 'blub');
    return $response->send([]);
});

$app->get('/header/using/send/bli/blub', function (\Hahns\Response\JsonImpl $response) {
    return $response->send([], ['bli' => 'blub']);
});

$app->get('/header/using/send/bli/blub/bla/bloh', function (\Hahns\Response\JsonImpl $response) {
    return $response->send([], [
        'bli' => 'blub',
        'bla' => 'bloh'
    ]);
});