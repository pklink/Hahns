<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check response headers');

$I->sendGET('/event');
$I->seeResponseEquals('/event');