<?php

// This script must only be executed from the command line.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

require_once __DIR__ . '/../config/database.php';

echo "=== GoalTicket Admin Setup ===\n\n";

$username = trim(readline("Username: "));
$fullName = trim(readline("Full name: "));
$password = readline("Password: ");

if ($username === '' || $fullName === '' || $password === '') {
    exit("All fields are required.\n");
}

if (strlen($password) < 8) {
    exit("Password must contain at least 8 characters.\n");
}

// Check whether username already exists
$stmt = $pdo->prepare(
    "SELECT admin_id FROM admins WHERE username = :username LIMIT 1"
);

$stmt->execute([
    'username' => $username
]);

if ($stmt->fetch()) {
    exit("An administrator with that username already exists.\n");
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO admins (username, password_hash, full_name, role)
     VALUES (:username, :password_hash, :full_name, 'admin')"
);

$stmt->execute([
    'username'      => $username,
    'password_hash' => $passwordHash,
    'full_name'     => $fullName
]);

echo "\nAdmin account created successfully.\n";