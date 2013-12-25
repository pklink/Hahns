<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check ErrorException');

$I->sendGET('/an-error');
$I->seeResponseCodeIs(500);
$I->seeResponseEquals('Hahns\\Exception\\ErrorException');
