<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform actions and see result');

$I->sendPOST('/', ['id' => 400]);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'home', 'id' => 400]);

$I->sendPOST('/hello/peter', ['say' => 'nice to meet you!']);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'nice to meet you!']);
