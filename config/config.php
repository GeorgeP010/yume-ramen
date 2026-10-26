<?php
/**
 * Yume Ramen Configuration File
 * Database en applicatie instellingen
 */

// Error reporting (zet uit in productie)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuratie
define('DB_HOST', 'localhost');
define('DB_NAME', 'yume');
define('DB_USER', 'yumeramen');
define('DB_PASS', 'yumeramen');

// Applicatie instellingen
define('BASE_URL', 'http://localhost/yume_ramen/');
define('SITE_NAME', 'Yume Ramen');

// Session configuratie
define('SESSION_LIFETIME', 3600); // 1 uur

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('MODEL_PATH', ROOT_PATH . '/models');
define('VIEW_PATH', ROOT_PATH . '/views');
define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Timezone
date_default_timezone_set('Europe/Amsterdam');

// Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
