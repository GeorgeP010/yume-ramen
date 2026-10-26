<?php
/**
 * Menu Page Entry Point
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load controller
require_once CONTROLLER_PATH . '/MenuController.php';

// Instantiate and run controller
$controller = new MenuController();
$controller->index();
