<?php
// Set page specific variables
$page_title = "Riot Games - Enhancements";
$header_title = "Project Enhancements";

// Include header file
include("header.inc");

// Include navigation
include("nav.inc");
?>

<main>
    <section>
        <h2>Project Enhancements</h2>
        <p>This page describes the additional features we have implemented beyond the basic requirements.</p>
        
        <!-- Enhancement 1: Sorting -->
        <div class="enhancement">
            <h3>1. Sorting EOI Records</h3>
            <p>We have implemented the ability for managers to sort EOI records by different fields:</p>
            <ul>
                <li><strong>Feature:</strong> In the manage.php page, we've added a dropdown menu that allows sorting by EOI Number, Job Reference, First Name, Last Name, and Status.</li>
                <li><strong>Implementation:</strong> This feature is implemented by adding a 'sort' parameter to the query string and using it in the SQL ORDER BY clause.</li>
                <li><strong>Files Modified:</strong> manage.php</li>
                <li><strong>How to Test:</strong> Go to the manage.php page, select different options from the "Sort By" dropdown, and click Search. The results will be reordered according to your selection.</li>
            </ul>
            <p>This enhancement makes it easier for HR managers to organize and find applications, especially when dealing with a large number of EOIs.</p>
        </div>
        
        <!-- Enhancement 2: Manager Registration -->
        <div class="enhancement">
            <h3>2. Manager Registration</h3>
            <p>We have implemented a manager registration system with server-side validation:</p>
            <ul>
                <li><strong>Feature:</strong> A registration form for new managers with username and password fields.</li>
                <li><strong>Validation:</strong> Ensures usernames are unique and passwords meet complexity requirements.</li>
                <li><strong>Storage:</strong> Manager information is stored securely in the managers table.</li>
                <li><strong>Files Created:</strong> register.php</li>
                <li><strong>How to Test:</strong> Visit register.php, try to create a new manager account with both valid and invalid inputs to see validation in action.</li>
            </ul>
            <p>This enhancement adds a layer of security by ensuring only registered managers can access the management system.</p>
            
            <div class="code-example">
                <h4>Code Example:</h4>
                <pre><code>
// Validate username uniqueness
$check_query = "SELECT * FROM $manager_table WHERE username = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $error_message .= "Username already exists. ";
}

// Validate password complexity
if (strlen($password) < 8 || 
    !preg_match('/[A-Z]/', $password) || 
    !preg_match('/[a-z]/', $password) || 
    !preg_match('/[0-9]/', $password)) {
    $error_message .= "Password must be at least 8 characters and contain uppercase, lowercase, and numbers. ";
}
                </code></pre>
            </div>
        </div>
        
        <!-- Enhancement 3: Login System -->
        <div class="enhancement">
            <h3>3. Manager Login System</h3>
            <p>We have implemented a login system to control access to the manage.php page:</p>
            <ul>
                <li><strong>Feature:</strong> A login form that verifies manager credentials against the database.</li>
                <li><strong>Security:</strong> Passwords are stored securely using password_hash() and verified with password_verify().</li>
                <li><strong>Session Control:</strong> Uses PHP sessions to maintain login state across pages.</li>
                <li><strong>Access Control:</strong> The manage.php page checks for an active authenticated session before allowing access.</li>
                <li><strong>Files Created:</strong> login.php, logout.php</li>
                <li><strong>Files Modified:</strong> manage.php (to add authentication check)</li>
                <li><strong>How to Test:</strong> Try accessing manage.php directly - you should be redirected to login.php. After successful login, you will have access to the management features.</li>
            </ul>
            <p>This enhancement ensures that only authorized personnel can manage job applications, protecting sensitive applicant data.</p>
            
            <div class="code-example">
                <h4>Code Example (from manage.php):</h4>
                <pre><code>
// Check for active session
session_start();
if (!isset($_SESSION['manager_id']) || !isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php?redirect=manage.php");
    exit();
}
                </code></pre>
            </div>
        </div>
        
        <!-- Enhancement 4: Login Attempt Tracking -->
        <div class="enhancement">
            <h3>4. Login Attempt Limiting</h3>
            <p>We have implemented a security feature that disables access after multiple invalid login attempts:</p>
            <ul>
                <li><strong>Feature:</strong> Tracks the number of failed login attempts for a username.</li>
                <li><strong>Security Measure:</strong> Temporarily locks accounts after three failed attempts.</li>
                <li><strong>Time-Based Security:</strong> Locked accounts are automatically unlocked after a set period (30 minutes).</li>
                <li><strong>User Feedback:</strong> Provides clear messages about account status and wait time.</li>
                <li><strong>Files Modified:</strong> login.php, settings.php</li>
                <li><strong>How to Test:</strong> Try logging in with an incorrect password three times. The system will lock the account temporarily.</li>
            </ul>
            <p>This enhancement protects against brute force attacks and adds an extra layer of security to the management system.</p>
            
            <div class="code-example">
                <h4>Code Example (login attempt tracking):</h4>
                <pre><code>
// Check for existing login attempts
$query = "SELECT * FROM login_attempts WHERE username = ? AND time > ?";
$stmt = mysqli_prepare($conn, $query);
$lockout_time = time() - (30 * 60); // 30 minutes ago
mysqli_stmt_bind_param($stmt, "si", $username, $lockout_time);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Count attempts
$attempts = mysqli_num_rows($result);

if ($attempts >= 3) {
    $error_message = "Account temporarily locked due to too many failed attempts. Please try again later.";
} else {
    // Proceed with login authentication
    // ...
    
    // If failed, record attempt
    if (!$authenticated) {
        $query = "INSERT INTO login_attempts (username, time, ip) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        $current_time = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        mysqli_stmt_bind_param($stmt, "sis", $username, $current_time, $ip);
        mysqli_stmt_execute($stmt);
    }
}
                </code></pre>
            </div>
        </div>
    </section>
</main>

<?php
// Include footer
include("footer.inc");
?> 