<?php

/* @var \Codeception\Scenario $scenario */

$I = new TestGuy($scenario);
$I->wantTo('check callback parameters');

$I->sendGET('/parameters');
$I->seeResponseEquals('yup');

$I->sendGET('/parameters/invalid');
$I->seeResponseContains('Uncaught exception \'Hahns\Exception\ParameterIsNotRegisterException\'');