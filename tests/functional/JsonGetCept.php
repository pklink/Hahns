<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform some GET requests');

$I->sendGET('/');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'home']);

$I->sendGET('/hallo');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'hallo welt!']);

$I->sendGET('/hello');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'hello world!']);

$I->sendGET('/hallo/peter');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'hallo peter']);

$I->sendGET('/hello/peter');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'hello peter']);

$I->sendGET('/hello/peter/1337');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'hello peter-1337']);

$I->sendGET('/hallo/peter/1337');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'hallo peter-1337']);

$I->sendGET('/hello/peter/pan');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'hello peter pan']);

$I->sendGET('/hallo/peter/pan');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => 'hallo peter pan']);

$I->sendGET('/blah/4434f');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => '4434']);

$I->sendGET('/say/4434.json');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['message' => '4434']);