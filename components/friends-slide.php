<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /lab/login/login.php');
    exit();
}

require '../config/database.php';
$user_id = (int)$_SESSION['user_id'];

// --- Lista de amigos ---
$query_friends = '
    SELECT u.username
    FROM friends f
    JOIN users u ON f.friend_id = u.id
    WHERE f.user_id = $1 AND f.status = $2
';
$result_friends = pg_query_params($db, $query_friends, [$user_id, 'accepted']);

// --- Lista de solicitudes recibidas ---
$query_requests = '
    SELECT f.id AS request_id, u.username
    FROM friends f
    JOIN users u ON f.user_id = u.id
    WHERE f.friend_id = $1 AND f.status = $2
';
$result_requests = pg_query_params($db, $query_requests, [$user_id, 'pending']);
?>

<div class="friends-container">
    <h2 class="friends-title">Friends</h2>
    <div class="friends-list">
        <?php
        if (!$result_friends) {
            echo '<p class="error-message">Error al cargar la lista de amigos</p>';
        } elseif (pg_num_rows($result_friends) === 0) {
            echo '<p class="no-friends">No friends yet</p>';
        } else {
            while ($friend = pg_fetch_assoc($result_friends)) {
                echo '<p class="friend-item">' . htmlspecialchars($friend['username'], ENT_QUOTES, 'UTF-8') . '</p>';
            }
        }
        ?>
    </div>

    <h2 class="friends-title" style="margin-top:20px;">Solicitudes recibidas</h2>
    <div class="friends-list">
        <?php
        if (!$result_requests) {
            echo '<p class="error-message">Error al cargar solicitudes</p>';
        } elseif (pg_num_rows($result_requests) === 0) {
            echo '<p class="no-friends">No hay solicitudes</p>';
        } else {
            while ($req = pg_fetch_assoc($result_requests)) {
                ?>
                <div class="friend-item">
                    <?php echo htmlspecialchars($req['username']); ?>
                    <form style="display:inline;" method="post" action="../components/respond_friend_request.php">
                        <input type="hidden" name="request_id" value="<?php echo $req['request_id']; ?>">
                        <button name="response" value="accepted">Aceptar</button>
                        <button name="response" value="rejected">Rechazar</button>
                    </form>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <h2 class="friends-title" style="margin-top:20px;">Enviar solicitud</h2>
    <form method="post" action="../components/send_friend_request.php">
        <input type="text" name="friend_username" placeholder="Nombre de usuario" required>
        <button type="submit">Enviar</button>
    </form>

</div>

<style>
.friends-container {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 20px;
    max-width: 250px;
    min-height: 500px;
    height: auto;
}

.friends-title {
    font-size: 18px;
    margin-bottom: 10px;
}

.friends-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.friend-item {
    padding: 6px 10px;
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

button {
    margin-left: 5px;
    padding: 2px 6px;
}
</style>
