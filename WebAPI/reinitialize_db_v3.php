<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/DB.php';

$db = DB::getInstance();

try {
    $db->getConnection()->exec("DELETE FROM users");
    $schema = file_get_contents(__DIR__ . '/database/schema_no_comments.sql');
    $db->getConnection()->exec($schema);

    $initDataPath = __DIR__ . '/database/init_data.sql';
    if (file_exists($initDataPath)) {
        $initData = file_get_contents($initDataPath);
        $db->getConnection()->exec($initData);
    }

    echo json_encode(['message' => 'Database reinitialized successfully.']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}