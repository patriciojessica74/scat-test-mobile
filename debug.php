<?php
// This bypasses all config files to see if your server is even working
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Step 1: PHP is working.</h1>";

$path = $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php';
echo "Checking path: " . $path . "<br>";

if (file_exists($path)) {
    echo "<h1>Step 2: Config file found.</h1>";
    include_once $path;
    echo "<h1>Step 3: Config loaded.</h1>";
} else {
    echo "<h1 style='color:red;'>Step 2 FAILED: Config file NOT found at that path!</h1>";
}

if (isset($conn)) {
    echo "<h1>Step 4: Database variable \$conn exists.</h1>";
    $test = mysqli_query($conn, "SELECT 1");
    if ($test) {
        echo "<h1>Step 5: Database query SUCCESSFUL.</h1>";
    } else {
        echo "<h1 style='color:red;'>Step 5 FAILED: " . mysqli_error($conn) . "</h1>";
    }
}
?>