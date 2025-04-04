<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/DB.php';

$db = DB::getInstance();

try {
    // Clear related tables first
    $db->getConnection()->exec("DELETE FROM quiz_submissions");
    $db->getConnection()->exec("DELETE FROM assignment_submissions");
    $db->getConnection()->exec("DELETE FROM student_classes");
    $db->getConnection()->exec("DELETE FROM teacher_subjects");
    $db->getConnection()->exec("DELETE FROM materials");
    $db->getConnection()->exec("DELETE FROM quizzes");
    $db->getConnection()->exec("DELETE FROM assignments");
    $db->getConnection()->exec("DELETE FROM students");
    $db->getConnection()->exec("DELETE FROM teachers");
    $db->getConnection()->exec("DELETE FROM users");

    // Read and execute schema_no_comments_v2.sql
    $schema = file_get_contents(__DIR__ . '/database/schema_no_comments_v2.sql');
    $db->getConnection()->exec($schema);

    // Read and execute init_data.sql if it exists
    $initDataPath = __DIR__ . '/database/init_data.sql';
    if (file_exists($initDataPath)) {
        $initData = file_get_contents($initDataPath);
        $db->getConnection()->exec($initData);
    }

    echo json_encode(['message' => 'Database reinitialized successfully.']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}