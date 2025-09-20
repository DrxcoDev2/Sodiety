<?php
session_start();

// Database configuration
$config = [
    'db' => [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => 'test_db',
        'user' => 'postgres',
        'password' => '2808'
    ]
];

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';
$messageClass = '';

try {
    // Create connection
    $dsn = "pgsql:host={$config['db']['host']};port={$config['db']['port']};dbname={$config['db']['dbname']}";
    $conn = new PDO($dsn, $config['db']['user'], $config['db']['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($username) || empty($password)) {
            throw new Exception('Username and password are required.');
        }

        // Get user from database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to home page
            header('Location: /lab/index/dashboard.php');
            exit();
        } else {
            throw new Exception('Invalid username or password.');
        }
    }
} catch (Exception $e) {
    $message = $e->getMessage();
    $messageClass = 'error';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../styles/globals.css">
    <script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.7/dist/handlebars.min.js"></script>
</head>
<body>
    <?php require '../components/header.php'; ?>
    <div class="container">
        <div class="form-container">
            <h1>Login</h1>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageClass; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn">Login</button>
            </form>
            
            <p>Don't have an account? <a href="../signup/singup.php">Sign up here</a></p>
        </div>
    </div>
</body>
</html>