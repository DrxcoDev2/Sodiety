<?php
session_start();
require_once '../config/database.php';

$user_id = (int)$_SESSION['user_id'];

$query = '
SELECT f.id AS request_id, u.id AS sender_id, u.username
FROM friends f
JOIN users u ON f.user_id = u.id
WHERE f.friend_id = $1 AND f.status = $2
';
$result = pg_query_params($db, $query, [$user_id, 'pending']);

$requests = pg_fetch_all($result);
echo json_encode($requests ?: []);
