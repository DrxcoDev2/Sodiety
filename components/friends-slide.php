<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /lab/login/login.php');
    exit();
}

require '../config/database.php';

$user_id = (int)$_SESSION['user_id'];

// Consulta con JOIN para obtener los nombres de los amigos
$query = '
    SELECT u.username
    FROM friends f
    JOIN users u ON f.friend_id = u.id
    WHERE f.user_id = $1
';
$result = pg_query_params($db, $query, array($user_id));

?>
<div class="friends-container">
    <h2 class="friends-title">Friends</h2>
    <div class="friends-list">
        <?php
        if (!$result) {
            echo '<p class="error-message">Error al cargar la lista de amigos</p>';
        } elseif (pg_num_rows($result) === 0) {
            echo '<p class="no-friends">No friends yet</p>';
        } else {
            while ($friend = pg_fetch_assoc($result)) {
                echo '<p class="friend-item">' . htmlspecialchars($friend['username'], ENT_QUOTES, 'UTF-8') . '</p>';
            }
        }
        ?>
    </div>
</div>

<style>
.friends-container {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 20px;
    max-width: 200px;
    min-height: 500px;
    height: auto;
}

.friends-title {
    font-size: 20px;
    margin-bottom: 15px;
}

.friends-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.friend-item {
    padding: 8px 12px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.no-friends {
    color: #666;
    font-style: italic;
}

.error-message {
    color: #dc3545;
    font-weight: 500;
}
</style>
