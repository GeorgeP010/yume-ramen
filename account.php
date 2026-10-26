<?php
/**
 * Account Page Entry Point
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load controllers
require_once CONTROLLER_PATH . '/AuthController.php';
require_once CONTROLLER_PATH . '/AccountController.php';

// Instantiate controller
$controller = new AccountController();

// Simple routing for AJAX actions
if (isset($_GET['action']) && $_GET['action'] === 'orderDetails') {
    $controller->getOrderDetails();
    exit;
}

// Default: show account page
$controller->index();
