<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform some DELETE requests');

$I->sendDELETE('/delete', ['id' => 400]);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'home', 'id' => 400]);

$I->sendDELETE('/hello/peter', ['say' => 'nice to meet you!']);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'nice to meet you!']);

$I->sendDELETE('/cars/id-412/now');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'removed card with id `412`']);
