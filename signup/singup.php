<?php
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
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

        // Basic validation
        if (empty($username) || empty($password) || empty($email)) {
            throw new Exception('All fields are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address.');
        }

        if (strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters long.');
        }

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            throw new Exception('Username or email already exists.');
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user
        $stmt = $conn->prepare("
            INSERT INTO users (username, password_hash, email, created_at) 
            VALUES (:username, :password, :email, NOW())
        ");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            $message = 'Registration successful! You can now login.';
            $messageClass = 'success';
            // Clear form
            $_POST = [];
        } else {
            throw new Exception('Registration failed. Please try again.');
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="../styles/globals.css">
    <script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.7/dist/handlebars.min.js"></script>
</head>
<body>
    <?php require '../components/header.php'; ?>
    <div class="container">
        <div class="form-container">
            <h1>Sign Up</h1>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageClass; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="singup.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn">Sign Up</button>
            </form>
            
            <p>Already have an account? <a href="../login/login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>