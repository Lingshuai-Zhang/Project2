<?php
// Start session
session_start();

// Include database settings
require_once("settings.php");

// Set page specific variables
$page_title = "Riot Games - Manager Login";
$header_title = "Manager Login";

// Initialize variables
$username = "";
$error_message = "";
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'manage.php';

// Create database connection
$conn = @mysqli_connect($host, $user, $password, $dbname);

// Check if already logged in
if (isset($_SESSION['manager_id']) && isset($_SESSION['username'])) {
    // Already logged in, redirect to manage page
    header("Location: $redirect");
    exit();
}

// Create necessary tables and default admin account
if ($conn) {
    // Create login_attempts table for tracking failed attempts
    $login_attempts_sql = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        time INT NOT NULL,
        ip VARCHAR(50) NOT NULL
    )";
    
    mysqli_query($conn, $login_attempts_sql);
    
    // Create managers table if it doesn't exist
    $managers_table_sql = "CREATE TABLE IF NOT EXISTS $manager_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    mysqli_query($conn, $managers_table_sql);
    
    // Check if admin user exists, create if not
    $check_admin = mysqli_query($conn, "SELECT id FROM $manager_table WHERE username = 'admin'");
    
    if (mysqli_num_rows($check_admin) == 0) {
        // Create default admin account
        $default_username = "admin";
        $default_password = password_hash("Admin123", PASSWORD_DEFAULT);
        
        $insert_query = "INSERT INTO $manager_table (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "ss", $default_username, $default_password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Get form data
    $username = isset($_POST['username']) ? trim($_POST['username']) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else if ($conn) {
        // Check for login attempts
        $attempts_query = "SELECT COUNT(*) as count FROM login_attempts
                          WHERE username = ? AND time > ?";
        $stmt = mysqli_prepare($conn, $attempts_query);
        $lockout_time = time() - (30 * 60); // 30 minutes ago
        mysqli_stmt_bind_param($stmt, "si", $username, $lockout_time);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['count'] >= 3) {
            $error_message = "Account locked due to too many failed attempts. Please try again later.";
        } else {
            // Check if username exists
            $query = "SELECT id, username, password FROM $manager_table WHERE username = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) == 1) {
                $manager = mysqli_fetch_assoc($result);
                
                // Verify password
                if (password_verify($password, $manager['password'])) {
                    // Password is correct, set session variables
                    $_SESSION['manager_id'] = $manager['id'];
                    $_SESSION['username'] = $manager['username'];
                    
                    // Clear any failed login attempts
                    $delete_query = "DELETE FROM login_attempts WHERE username = ?";
                    $stmt = mysqli_prepare($conn, $delete_query);
                    mysqli_stmt_bind_param($stmt, "s", $username);
                    mysqli_stmt_execute($stmt);
                    
                    // Redirect to manage page
                    header("Location: $redirect");
                    exit();
                } else {
                    // Record failed attempt
                    $insert_query = "INSERT INTO login_attempts (username, time, ip) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insert_query);
                    $time = time();
                    $ip = $_SERVER['REMOTE_ADDR'];
                    mysqli_stmt_bind_param($stmt, "sis", $username, $time, $ip);
                    mysqli_stmt_execute($stmt);
                    
                    $error_message = "Invalid username or password.";
                }
            } else {
                $error_message = "Invalid username or password.";
            }
        }
    } else {
        $error_message = "Database connection error. Please try again later.";
    }
}

// Include header file
include("header.inc");

// Include navigation
include("nav.inc");
?>

<main>
    <section class="application-form">
        <h2>Manager Login</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="message error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php<?php echo !empty($redirect) ? '?redirect=' . urlencode($redirect) : ''; ?>" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group submit-group">
                <button type="submit" name="login" value="1">Login</button>
            </div>
            
            <p class="login-links">
                Don't have an account? <a href="register.php">Register</a>
            </p>
        </form>
        
        <div class="login-info">
            <h3>Login Information</h3>
            <p>Use the following credentials for testing:</p>
            <ul>
                <li><strong>Username:</strong> admin</li>
                <li><strong>Password:</strong> Admin123</li>
            </ul>
            <p>Note: After 3 failed login attempts, your account will be temporarily locked for 30 minutes.</p>
        </div>
    </section>
</main>

<?php
// Include footer
include("footer.inc");

// Close database connection
if ($conn) {
    mysqli_close($conn);
}
?> 