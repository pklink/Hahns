<?php

$app->get('/not-found', function (\Hahns\Response\Html $response) {
    $response->header('test0r', 'yep');
    throw new \Hahns\Exception\NotFoundException();
});

