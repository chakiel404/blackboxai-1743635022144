<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/DB.php';

try {
    $db = DB::getInstance();
    $pdo = $db->getConnection();
    
    // Enable foreign key support
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    // Begin transaction
    $pdo->beginTransaction();
    
    try {
        // Drop existing tables in reverse order of dependencies
        $tables = [
            'student_classes', 'assignment_submissions', 'quiz_submissions',
            'quiz_questions', 'quizzes', 'materials', 'teacher_subjects',
            'assignments', 'subjects', 'students', 'teachers', 'settings', 'users'
        ];
        
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS $table");
        }
        
        // Read and execute schema
        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($schema);
        
        // Read and execute initial data if exists
        $initDataPath = __DIR__ . '/database/init_data.sql';
        if (file_exists($initDataPath)) {
            $initData = file_get_contents($initDataPath);
            $pdo->exec($initData);
        }
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode(['message' => 'Database reinitialized successfully']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}