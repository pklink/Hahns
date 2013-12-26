<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform some named routes');

$I->sendGET('/route1');
$I->seeResponseEquals('hello world');

$I->sendGET('/route2');
$I->seeResponseEquals('hello world');

$I->sendGET('/route3');
$I->seeResponseEquals('hello world');