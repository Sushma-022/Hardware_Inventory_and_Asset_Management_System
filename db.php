

<?php
$conn = new mysqli("localhost", "root", "", "hardware_db",3307);

if ($conn->connect_error) {
    die("DB Connection Failed");
}
?>