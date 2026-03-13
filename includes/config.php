<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Determine if we are on localhost
$is_localhost = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1');

// --- FIXED BASE URL ---
define('BASE_URL', '/SCAT1/'); 

// Root Path for internal PHP checks
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/');

// --- MODULE PATHS ---
define('ADMIN_URL', BASE_URL . 'admin/');
define('FACULTY_URL', BASE_URL . 'faculty/');
define('FUNC_URL', BASE_URL . 'includes/function/');

// --- ASSET PATHS ---
define('CSS_PATH', BASE_URL . 'assets/css/'); 
define('JS_PATH', BASE_URL . 'assets/js/'); 
define('IMG_PATH', BASE_URL . 'assets/Img/');

// --- UPLOAD PATHS ---
define('UPLOAD_URL', BASE_URL . 'uploads/');
define('UPLOAD_DIR', ROOT_PATH . 'uploads/');

// Database Connection
require_once __DIR__ . '/connection.php';
?>