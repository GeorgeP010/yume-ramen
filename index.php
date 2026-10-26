<?php
/**
 * Homepage Entry Point
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load controller
require_once CONTROLLER_PATH . '/HomeController.php';

// Instantiate and run controller
$controller = new HomeController();
$controller->index();
