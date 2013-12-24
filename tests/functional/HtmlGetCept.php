<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform some GET requests');

$I->sendGET('/html');
$I->seeHttpHeader('Content-Type', 'text/html');
$I->seeResponseEquals('<h1>yep</h1>');
