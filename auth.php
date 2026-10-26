<?php
/**
 * Authentication Handler
 * Handles login, register, logout actions
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load controller
require_once CONTROLLER_PATH . '/AuthController.php';

// Instantiate controller
$authController = new AuthController();

// Get action from query string or POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Route to appropriate method
switch ($action) {
    case 'login':
        $authController->login();
        break;
        
    case 'register':
        $authController->register();
        break;
        
    case 'logout':
        $authController->logout();
        break;
        
    default:
        header('Location: account.php');
        exit;
}
