<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check response headers');

$I->sendGET('/header/using/header/bli/blub');
$I->seeHttpHeader('bli', 'blub');

$I->sendGET('/header/code/created');
$I->canSeeResponseCodeIs(201);

$I->sendGET('/header/code/created/by/send');
$I->canSeeResponseCodeIs(201);

$I->haveHttpHeader('bla', 'roflcopter');
$I->sendGET('/header/return/bla');
$I->seeResponseEquals('roflcopter');

$I->haveHttpHeader('X-Bla', 'doge');
$I->sendGET('/header/return/xbla');
$I->seeResponseEquals('doge');