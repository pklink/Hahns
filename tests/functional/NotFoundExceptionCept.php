<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check NotFoundException');

$I->sendGET('/not-found');
$I->seeResponseCodeIs(404);
$I->canSeeHttpHeader('test0r', 'yep');