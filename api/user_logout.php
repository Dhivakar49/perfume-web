<?php
/**
 * User Logout API
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

logoutUser();
redirect('../index.php');
?>
