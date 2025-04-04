<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/DB.php';

$email = 'admin@example.com';
$password = 'password123'; // Assuming this is the password for testing

$data = json_encode(['email' => $email, 'password' => $password]);

$ch = curl_init('http://' . $_SERVER['HTTP_HOST'] . '/api/auth/login?action=login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
curl_close($ch);

header('Content-Type: application/json');
echo $response;
?>