<?php
$username = "campuspulse_user";
$password = "root";
$connection_string = "localhost/XE";

$conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    die("<h3 style='color:red;'>Oracle DB Connection Failed: " . htmlspecialchars($e['message']) . "</h3>");
}
// Commented out to keep UI clean
// echo "<h3 style='color:green;'>Connected to Oracle Database Successfully!</h3>";
?>