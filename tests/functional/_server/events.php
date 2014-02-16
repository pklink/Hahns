<?php

/* @var \Hahns\Hahns $app */

$tmp = '';

$app->on(\Hahns\Hahns::EVENT_BEFORE_ROUTING, function($usedRoute) use ($tmp) {
    $tmp = $usedRoute;
});


$app->get('/event', function (\Hahns\Hahns $app) use ($tmp) {
    echo $tmp;
});

