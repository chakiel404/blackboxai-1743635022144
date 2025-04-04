<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/DB.php';

$db = DB::getInstance();

try {
    // Clear related tables first
    $db->getConnection()->exec("DROP TABLE IF EXISTS quiz_submissions");
    $db->getConnection()->exec("DROP TABLE IF EXISTS assignment_submissions");
    $db->getConnection()->exec("DROP TABLE IF EXISTS student_classes");
    $db->getConnection()->exec("DROP TABLE IF EXISTS teacher_subjects");
    $db->getConnection()->exec("DROP TABLE IF EXISTS materials");
    $db->getConnection()->exec("DROP TABLE IF EXISTS quiz_questions");
    $db->getConnection()->exec("DROP TABLE IF EXISTS quizzes");
    $db->getConnection()->exec("DROP TABLE IF EXISTS assignments");
    $db->getConnection()->exec("DROP TABLE IF EXISTS students");
    $db->getConnection()->exec("DROP TABLE IF EXISTS teachers");
    $db->getConnection()->exec("DROP TABLE IF EXISTS subjects");
    $db->getConnection()->exec("DROP TABLE IF EXISTS settings");
    $db->getConnection()->exec("DROP TABLE IF EXISTS users");
    $db->getConnection()->exec("DROP TABLE IF EXISTS classes");

    // Read and execute schema_fixed.sql
    $schema = file_get_contents(__DIR__ . '/database/schema_fixed.sql');
    $db->getConnection()->exec($schema);

    // Read and execute init_data.sql
    $initData = file_get_contents(__DIR__ . '/database/init_data.sql');
    $db->getConnection()->exec($initData);

    echo json_encode(['message' => 'Database reinitialized successfully.']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}