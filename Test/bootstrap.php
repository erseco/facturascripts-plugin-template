<?php

/**
 * This file is part of PluginTemplate plugin for FacturaScripts.
 * Copyright (C) 2025 Your Name <your@email.com>
 *
 * PHPUnit bootstrap file for testing
 */

// Define FacturaScripts folder
define('FS_FOLDER', dirname(__DIR__));

// Load composer autoloader
require_once FS_FOLDER . '/vendor/autoload.php';

// Load FacturaScripts configuration
if (file_exists(FS_FOLDER . '/config.php')) {
    require_once FS_FOLDER . '/config.php';
}

// Initialize minimal FacturaScripts environment for testing
if (!defined('FS_LANG')) {
    define('FS_LANG', 'es_ES');
}

if (!defined('FS_TIMEZONE')) {
    define('FS_TIMEZONE', 'Europe/Madrid');
}

// Register plugin namespaces with the autoloader
$loader = require FS_FOLDER . '/vendor/autoload.php';

// Register FacturaScripts Core
$loader->addPsr4('FacturaScripts\\Core\\', FS_FOLDER . '/Core');

// Register PluginTemplate
$loader->addPsr4('FacturaScripts\\Plugins\\PluginTemplate\\', FS_FOLDER . '/Plugins/PluginTemplate');

\FacturaScripts\Core\Kernel::init();
if (!in_array('PluginTemplate', \FacturaScripts\Core\Plugins::enabled(), true)) {
    if (!\FacturaScripts\Core\Plugins::enable('PluginTemplate')) {
        throw new RuntimeException('Could not enable PluginTemplate in the test environment.');
    }
}
\FacturaScripts\Core\Plugins::deploy();

// If your plugin depends on other plugins, register them here as well
// Example: $loader->addPsr4('FacturaScripts\\Plugins\\OtherPlugin\\', FS_FOLDER . '/Plugins/OtherPlugin');
