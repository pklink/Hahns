<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform some PUT requests');

$I->sendPUT('/put', ['id' => 400]);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'home', 'id' => 400]);

$I->sendPUT('/hello/peter', ['say' => 'nice to meet you!']);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'nice to meet you!']);
