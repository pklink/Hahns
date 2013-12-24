<?php

/* @var \Hahns\Hahns $app */

$app->get('/html', function (\Hahns\Response\Html $response) {
    return $response->send('<h1>yep</h1>');
});