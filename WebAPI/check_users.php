<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/DB.php';

try {
    $db = DB::getInstance();
    $users = $db->fetchAll("SELECT * FROM users WHERE role = 'admin'");
    echo json_encode($users, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}