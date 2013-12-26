<?php

/* @var \Hahns\Hahns $app */

$app->get('/header/using/header/bli/blub', function (\Hahns\Response\Json $response) {
    $response->header('bli', 'blub');
    return $response->send([]);
});

$app->get('/header/code/created', function (\Hahns\Response\Json $response) {
    $response->status(\Hahns\Response::CODE_CREATED);
    return $response->send([]);
});

$app->get('/header/code/created/by/send', function (\Hahns\Response\Json $response) {
    return $response->send([], \Hahns\Response::CODE_CREATED);
});

$app->get('/header/return/bla', function (\Hahns\Request $request) {
    return $request->header('bla');
});

$app->get('/header/return/xbla', function (\Hahns\Request $request) {
    return $request->header('X-Bla');
});