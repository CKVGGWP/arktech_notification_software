<?php

/*set your website title*/

define('WEBSITE_TITLE', "Arktech Manufacturing");

/*protocol type http or https*/

define('PROTOCOL', 'http');

/*root and asset paths*/

$path = str_replace("\\", "/", PROTOCOL . "://" . $_SERVER['SERVER_NAME'] . __DIR__  . "/");
$path = str_replace($_SERVER['DOCUMENT_ROOT'], "", $path);
$newPath = $_SERVER['DOCUMENT_ROOT'] . "/Arktech Main/";

define('ROOT', str_replace("config", "Arktech Main", $path));
define('ASSETS', str_replace("config", "assets", $path));
define('MODELS', str_replace("config", "models", $path));

/*set to true to allow error reporting
set to false when you upload online to stop error reporting*/

define('DEBUG', true);

if (DEBUG) {
    ini_set("display_errors", 1);
} else {
    ini_set("display_errors", 0);
}
