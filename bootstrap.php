<?php
// Bootstrap file - loads all necessary configurations
session_start();

// Define root path
define('ROOT_PATH', __DIR__);

// Load configuration
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/session.php';

// Load utility functions
if (file_exists(ROOT_PATH . '/includes/functions.php')) {
    require_once ROOT_PATH . '/includes/functions.php';
}
