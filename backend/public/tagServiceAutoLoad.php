<?php

spl_autoload_register(function ($className){
    $fileFullName = __DIR__ . trim(preg_replace(['/\\\+/', '/PageParser/'], ['/', '/lib'], $className)) . '.php';

    if(is_readable($fileFullName)){
        include_once ($fileFullName);
    }
});