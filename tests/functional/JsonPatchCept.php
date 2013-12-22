<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform some PATCH requests');

$I->sendPATCH('/patch', ['id' => 400]);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'home', 'id' => 400]);

$I->sendPATCH('/hello/peter', ['say' => 'nice to meet you!']);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'nice to meet you!']);
