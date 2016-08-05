<?php

/* help ourselves to the Composer autoloader... */
/*
 * FIXME I have to imagine that assuming the install directory is 'vendor' is
 * 		 unsafe...
 */
if (strpos(__DIR__, '/vendor/')) {
    $composer = require preg_replace('%(.*/vendor)/.*%', '$1/autoload.php', __DIR__);
} else {
    $composer = require __DIR__ . '/vendor/autoload.php';
}

use smtech\StMarksSmarty\StMarksSmarty;

session_start();

$smarty = StMarksSmarty::getSmarty();
$smarty->addTemplateDir(__DIR__ . '/templates', 'smtech/stmarks-reflexive-canvas-lti');
