<?php
// Database configuration
$config = [
    'db' => [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => 'test_db',
        'user' => 'postgres',
        'password' => 'tu_contraseña' // Cambia esto por tu contraseña de PostgreSQL
    ]
];

// Error reporting (útil en laboratorio)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $dsn = "pgsql:host={$config['db']['host']};port={$config['db']['port']};dbname={$config['db']['dbname']};";
    $conn = new PDO($dsn, $config['db']['user'], $config['db']['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $message = '';
    $users = [];

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
        $username = trim($_POST['username']);

        // ---------- VULNERABLE CODE: concatenación directa (NO USAR en producción) ----------
        // Esto es vulnerable a inyección SQL porque inserta $username directamente en la consulta.
        $sql = "SELECT id, username, created_at FROM users WHERE username ILIKE '%" . $username . "%'";
        
        // Ejecuta la consulta directamente
        $stmt = $conn->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($users) === 0) {
            $message = "No users found matching '{$username}'";
        }
    }
} catch(PDOException $e) {
    $message = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Search (Vulnerable)</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; }
        .search-form { margin-bottom: 20px; padding: 20px; background: #f5f5f5; border-radius: 5px; }
        .results { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .error { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    </style>
</head>
<body>
    <?php require './components/header.php'; ?>
    <h1>User Search (Vulnerable)</h1>
    
    <div class="search-form">
        <form method="post" action="">
            <div>
                <label for="username">Search Users:</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES); ?>" 
                       placeholder="Enter username" required>
                <button type="submit">Search</button>
            </div>
        </form>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($users)): ?>
        <div class="results">
            <h2>Search Results</h2>
            <table>
                <thead>
                    <tr><th>ID</th><th>Username</th><th>Created At</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>

<?php
// Close connection
$conn = null;
?>
