<?php
// Use the absolute server path to reach config.php from inside the function folder
require_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        // Ensure keys match what the JS is sending
        $code = mysqli_real_escape_string($conn, strtoupper($_POST['code']));
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $room = mysqli_real_escape_string($conn, $_POST['room']);

        if (!empty($code) && !empty($name)) {
            $sql = "INSERT INTO subject_catalog (subject_code, subject_name, room) 
                    VALUES ('$code', '$name', '$room')";
            if (mysqli_query($conn, $sql)) {
                echo "success";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Please fill in all fields.";
        }
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        if (mysqli_query($conn, "DELETE FROM subject_catalog WHERE id = $id")) {
            echo "success";
        }
    }
    exit();
}
?>