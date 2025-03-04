<?php
// calculate.php
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['votes']) || !is_array($data['votes'])) {
    // ???????? field votes ??????? array
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input. Expected { "votes": [...] }'
    ]);
    exit;
}

// ??????????????????
echo json_encode([
    'status' => 'success',
    'message' => 'Liked successfully',
    // 'results' => ..., // ???????????????
]);
