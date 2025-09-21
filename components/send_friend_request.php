<?php
// send_friend_request.php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die('Usuario no logueado');
}

$sender_id = (int)$_SESSION['user_id'];
$friend_username = $_POST['friend_username'] ?? '';

// Buscar el id del usuario a agregar
$query = 'SELECT id FROM users WHERE username = $1';
$result = pg_query_params($db, $query, [$friend_username]);

if (!$result || pg_num_rows($result) === 0) {
    die('Usuario no encontrado');
}

$friend = pg_fetch_assoc($result);
$receiver_id = (int)$friend['id'];

// Verificar que no exista ya la solicitud
$query_check = 'SELECT * FROM friends WHERE user_id = $1 AND friend_id = $2';
$result_check = pg_query_params($db, $query_check, [$sender_id, $receiver_id]);

if (pg_num_rows($result_check) > 0) {
    die('Solicitud ya enviada o ya son amigos');
}

// Insertar solicitud pendiente
$query_insert = 'INSERT INTO friends (user_id, friend_id, status) VALUES ($1, $2, $3)';
pg_query_params($db, $query_insert, [$sender_id, $receiver_id, 'pending']);

header('Location: dashboard.php'); // o a la página donde está la lista de amigos
