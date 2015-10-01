<?php

const PROJECT_NAMESPACE = 'Robier\\Holiday\\';
const PROJECT_LOCATION = '../src/';

spl_autoload_register(function ($className) {

    if (strpos($className, PROJECT_NAMESPACE) === false) {
        return;
    }

    include str_replace([PROJECT_NAMESPACE, '\\'], [PROJECT_LOCATION, '/'], $className) . '.php';
});