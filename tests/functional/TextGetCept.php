<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform some GET requests');

$I->sendGET('/text');
$I->seeHttpHeader('Content-Type', 'text/plain');
$I->seeResponseEquals('yep');
