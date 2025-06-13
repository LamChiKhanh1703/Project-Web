<?php
    $conn = mysqli_connect("localhost", "root", "", "webdochoi");
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>
