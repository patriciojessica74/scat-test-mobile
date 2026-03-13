<?php
// Go up two levels (../../) to reach the SCAT1 root, then into includes
include_once __DIR__ . '/../../includes/config.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();

// BASE_URL will now work because config.php is correctly loaded
$login_url = BASE_URL . "login.php";

echo "<script>
    localStorage.clear();
    window.location.href = '$login_url';
</script>";
exit();
?>