<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/DB.php';

$db = DB::getInstance();

$statements = [
    "CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        full_name TEXT NOT NULL,
        role TEXT CHECK(role IN ('admin', 'guru', 'siswa')) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS students (
        student_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL UNIQUE,
        nisn TEXT NOT NULL UNIQUE,
        class TEXT,
        gender TEXT CHECK(gender IN ('L', 'P')),
        birth_date DATE,
        birth_place TEXT,
        address TEXT,
        phone TEXT,
        parent_name TEXT,
        parent_phone TEXT,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS teachers (
        teacher_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL UNIQUE,
        nip TEXT NOT NULL UNIQUE,
        gender TEXT CHECK(gender IN ('L', 'P')),
        birth_date DATE,
        birth_place TEXT,
        address TEXT,
        phone TEXT,
        education_level TEXT,
        major TEXT,
        join_date DATE,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )",
    // Add other CREATE TABLE statements here...
];

foreach ($statements as $sql) {
    try {
        $db->getConnection()->exec($sql);
        echo json_encode(['message' => 'Executed successfully: ' . $sql]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to execute: ' . $sql . ' - ' . $e->getMessage()]);
    }
}
?>