<?php
session_start();
require_once '../config/database.php';

$request_id = (int)$_POST['request_id'];
$response = $_POST['response']; // 'accepted' o 'rejected'

if (!in_array($response, ['accepted', 'rejected'])) {
    die('Invalid response');
}

// Actualizar solicitud
$query = 'UPDATE friends SET status = $1 WHERE id = $2 RETURNING *';
$result = pg_query_params($db, $query, [$response, $request_id]);

if ($result) {
    // Si aceptada, opcionalmente crear la relación inversa para que sea bidireccional
    $row = pg_fetch_assoc($result);
    if ($response === 'accepted') {
        $check = pg_query_params($db, 'SELECT * FROM friends WHERE user_id=$1 AND friend_id=$2', [$row['friend_id'], $row['user_id']]);
        if (pg_num_rows($check) === 0) {
            pg_query_params($db, 'INSERT INTO friends (user_id, friend_id, status) VALUES ($1,$2,$3)', [$row['friend_id'], $row['user_id'], 'accepted']);
        }
    }

    echo json_encode(['status' => 'success']);
    
} else {
    echo json_encode(['status' => 'error']);
}
