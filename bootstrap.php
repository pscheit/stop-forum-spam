<?php

/**
 * Bootstrap and Autoload whole application
 *
 * you can use this file to bootstrap for tests or bootstrap for scripts / others
 */
$ds = DIRECTORY_SEPARATOR;

// autoload project dependencies and self autoloading for the library
require_once __DIR__.$ds.'vendor'.$ds.'autoload.php';

require_once __DIR__.$ds.'vendor'.$ds.'pscheit'.$ds.'psc-cms'.$ds.'bin'.$ds.'psc-cms.phar.gz';
?>