<?php
/**
 * Test script om favorieten te controleren
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once MODEL_PATH . '/Favorite.php';

// Check of user is ingelogd
if (!isset($_SESSION['user_id'])) {
    die("Je moet ingelogd zijn. <a href='account.php'>Login</a>");
}

$customerId = $_SESSION['user_id'];
$favoriteModel = new Favorite();

echo "<h2>Favorieten Test voor Customer ID: $customerId</h2>";

// Test: Get alle favorieten
echo "<h3>Alle favorieten:</h3>";
$favorites = $favoriteModel->getByCustomer($customerId);
echo "<pre>";
print_r($favorites);
echo "</pre>";

// Test: Check database direct
echo "<h3>Directe database query:</h3>";
$db = Database::getInstance();
$sql = "SELECT * FROM favorites WHERE customer_id = :customer_id";
$results = $db->fetchAll($sql, [':customer_id' => $customerId]);
echo "<pre>";
print_r($results);
echo "</pre>";

// Test: Check tabel structuur
echo "<h3>Tabel structuur:</h3>";
$sql = "DESCRIBE favorites";
$structure = $db->fetchAll($sql);
echo "<pre>";
print_r($structure);
echo "</pre>";
