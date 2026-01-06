<?php
require_once 'autoload.php';

$db = new Database();
$conn = $db->dbConnection();
echo "<script>location.href = './auth/login.php';</script>";

// if(!$conn) {
//     echo "Database connection failed!\n";
// }

