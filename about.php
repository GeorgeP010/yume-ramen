<?php
/**
 * About Page Entry Point
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load controller
require_once CONTROLLER_PATH . '/AboutController.php';

// Instantiate and run controller
$controller = new AboutController();
$controller->index();
