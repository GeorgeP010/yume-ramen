<?php
/**
 * API endpoint voor favorieten toggle
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once CONTROLLER_PATH . '/MenuController.php';

$controller = new MenuController();
$controller->toggleFavorite();
