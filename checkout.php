<?php
/**
 * Checkout Page Entry Point
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load controller
require_once CONTROLLER_PATH . '/CheckoutController.php';

// Instantiate controller
$controller = new CheckoutController();

// 2-staps flow:
// GET  -> stap 1 (adres + betaling)
// POST -> stap 2 (overzicht)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->review();
} else {
    $controller->index();
}
