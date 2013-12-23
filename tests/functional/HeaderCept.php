<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform some GET requests');

$I->sendGET('/header/using/header/bli/blub');
$I->seeHttpHeader('bli', 'blub');

$I->sendGET('/header/using/send/bli/blub');
$I->seeHttpHeader('bli', 'blub');

$I->sendGET('/header/using/send/bli/blub/bla/bloh');
$I->seeHttpHeader('bli', 'blub');
$I->seeHttpHeader('bla', 'bloh');