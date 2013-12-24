<?php

/* @var \Hahns\Hahns $app */

$app->get('/redirect', function (\Hahns\Response\JsonImpl $response) {
    $response->redirect('/redirected');
});