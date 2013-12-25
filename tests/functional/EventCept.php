<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check events');

$I->sendGET('/event');
$I->seeResponseEquals('/event');