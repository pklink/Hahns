<?php

class IsSingleton {
    public $test;

    function __construct()
    {
        $this->test = microtime();
    }


}

class IsNotSingleton extends IsSingleton { }