<?php
// Start session
session_start();

// Include database settings
require_once("settings.php");

// Set page specific variables
$page_title = "Riot Games - Manager Registration";
$header_title = "Manager Registration";

// Initialize variables
$username = "";
$email = "";
$error_message = "";
$success_message = "";

// Create database connection
$conn = @mysqli_connect($host, $user, $password, $dbname);

// Check if managers table exists, create if not
if ($conn) {
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE '$manager_table'");
    
    if (mysqli_num_rows($check_table) == 0) {
        // Create managers table
        $managers_table_sql = "CREATE TABLE IF NOT EXISTS $manager_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        mysqli_query($conn, $managers_table_sql);
    }
}

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Get form data
    $username = isset($_POST['username']) ? trim($_POST['username']) : "";
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";
    
    // Validate input
    if (empty($username)) {
        $error_message .= "Username is required. ";
    } else if (!preg_match('/^[a-zA-Z0-9_]{4,50}$/', $username)) {
        $error_message .= "Username must be 4-50 characters and contain only letters, numbers, and underscores. ";
    }
    
    if (empty($email)) {
        $error_message .= "Email is required. ";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message .= "Invalid email format. ";
    }
    
    if (empty($password)) {
        $error_message .= "Password is required. ";
    } else if (strlen($password) < 8) {
        $error_message .= "Password must be at least 8 characters. ";
    } else if (!preg_match('/[A-Z]/', $password)) {
        $error_message .= "Password must contain at least one uppercase letter. ";
    } else if (!preg_match('/[a-z]/', $password)) {
        $error_message .= "Password must contain at least one lowercase letter. ";
    } else if (!preg_match('/[0-9]/', $password)) {
        $error_message .= "Password must contain at least one number. ";
    }
    
    if ($password !== $confirm_password) {
        $error_message .= "Passwords do not match. ";
    }
    
    // Check if username already exists
    if (empty($error_message) && $conn) {
        $check_query = "SELECT id FROM $manager_table WHERE username = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error_message .= "Username already exists. ";
        }
    }
    
    // Register user if no errors
    if (empty($error_message) && $conn) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert manager into database
        $insert_query = "INSERT INTO $manager_table (username, password, email) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Registration successful! You can now login.";
            // Clear form data
            $username = "";
            $email = "";
        } else {
            $error_message = "Registration failed: " . mysqli_error($conn);
        }
    }
}

// Include header file
include("header.inc");

// Include navigation
include("nav.inc");
?>

<main>
    <section class="application-form">
        <h2>Manager Registration</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="message error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="message success">
                <?php echo $success_message; ?>
                <p>Click <a href="login.php">here</a> to login.</p>
            </div>
        <?php endif; ?>
        
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                <small>4-50 characters, letters, numbers, and underscores only</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <small>At least 8 characters including uppercase, lowercase, and numbers</small>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
            </div>
            
            <div class="form-group submit-group">
                <button type="submit" name="register" value="1">Register</button>
            </div>
            
            <p class="login-links">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </form>
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