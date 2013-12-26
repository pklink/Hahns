<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check events');

$I->sendDELETE('/any');
$I->seeResponseEquals('any');

$I->sendGET('/any');
$I->seeResponseEquals('any');

$I->sendPATCH('/any');
$I->seeResponseEquals('any');

$I->sendPOST('/any');
$I->seeResponseEquals('any');

$I->sendPUT('/any');
$I->seeResponseEquals('any');