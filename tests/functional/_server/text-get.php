<?php

/* @var \Hahns\Hahns $app */

$app->get('/text', function (\Hahns\Response\Text $response) {
    return $response->send('yep');
});