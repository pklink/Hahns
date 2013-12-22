<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('perform not founding request');

$I->sendGET('/wdwadwadwayccy');
$I->seeResponseCodeIs(404);
$I->seeResponseEquals('1');
