

<?php
include "db.php";

$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$passwordRaw = $_POST['password'] ?? '';

if ($username === '' || $email === '' || $passwordRaw === '') {
    echo "<script>alert('All fields are required'); window.location='index.html';</script>";
    exit();
}

$password = password_hash($passwordRaw, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    echo "<script>alert('Account created successfully'); window.location='index.html';</script>";
} else {
    echo "<script>alert('Username already exists'); window.location='index.html';</script>";
}
?>