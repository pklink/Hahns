<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check redirections');

$I->sendGET('/redirect');
$I->seeResponseCodeIs(404);
$I->amOnPage('/redirected');