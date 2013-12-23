<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check services');

$I->sendGET('/services/yep');
$I->seeResponseEquals('yep');