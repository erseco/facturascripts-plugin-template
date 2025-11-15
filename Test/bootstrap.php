<?php
/**
 * This file is part of PluginTemplate plugin for FacturaScripts.
 * PHPUnit bootstrap file for testing
 */

// Define the base path
define('FS_FOLDER', dirname(__DIR__));

// Load FacturaScripts bootstrap if it exists
if (file_exists(FS_FOLDER . '/vendor/autoload.php')) {
    require_once FS_FOLDER . '/vendor/autoload.php';
}

// This file is used when running tests inside FacturaScripts
