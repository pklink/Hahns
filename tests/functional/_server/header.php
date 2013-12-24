<?php

/* @var \Hahns\Hahns $app */

$app->get('/header/using/header/bli/blub', function (\Hahns\Response\Json $response) {
    $response->header('bli', 'blub');
    return $response->send([]);
});

$app->get('/header/using/send/bli/blub', function (\Hahns\Response\Json $response) {
    return $response->send([], ['bli' => 'blub']);
});

$app->get('/header/using/send/bli/blub/bla/bloh', function (\Hahns\Response\Json $response) {
    return $response->send([], [
        'bli' => 'blub',
        'bla' => 'bloh'
    ]);
});

$app->get('/header/code/created', function (\Hahns\Response\Json $response) {
    $response->status(\Hahns\Response::CODE_CREATED);
    return $response->send([]);
});