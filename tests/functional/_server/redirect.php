<?php

/* @var \Hahns\Hahns $app */

$app->get('/redirect', function (\Hahns\Response\Json $response) {
    $response->redirect('/redirected');
});