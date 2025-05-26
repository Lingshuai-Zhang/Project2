<?php
// Start session
session_start();

// Include database settings
require_once("settings.php");

// Verify form submission
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Redirect to apply.php if accessed directly
    header("Location: apply.php");
    exit();
}

// Create database connection
$conn = @mysqli_connect($host, $user, $password, $dbname);

// Check if database connection was successful
if (!$conn) {
    // Try to create the database if it doesn't exist
    $conn_without_db = @mysqli_connect($host, $user, $password);
    
    if ($conn_without_db) {
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        if (mysqli_query($conn_without_db, $sql)) {
            // Connect to the newly created database
            $conn = @mysqli_connect($host, $user, $password, $dbname);
        } else {
            // If database creation fails
            showError("Database connection error: Unable to create database.");
            exit();
        }
        mysqli_close($conn_without_db);
    } else {
        // If connection fails completely
        showError("Database connection error: " . mysqli_connect_error());
        exit();
    }
}

// Create EOI table if it doesn't exist
$eoi_table_sql = "CREATE TABLE IF NOT EXISTS $eoi_table (
    EOInumber INT AUTO_INCREMENT PRIMARY KEY,
    job_reference VARCHAR(10) NOT NULL,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    street_address VARCHAR(40) NOT NULL,
    suburb VARCHAR(40) NOT NULL,
    state VARCHAR(3) NOT NULL,
    postcode VARCHAR(4) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skill1 VARCHAR(20),
    skill2 VARCHAR(20),
    skill3 VARCHAR(20),
    skill4 VARCHAR(20),
    other_skills TEXT,
    status VARCHAR(10) DEFAULT 'New'
)";

if (!mysqli_query($conn, $eoi_table_sql)) {
    showError("Error creating table: " . mysqli_error($conn));
    exit();
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to display error with proper HTML structure
function showError($error_message) {
    $page_title = "Error";
    $header_title = "Application Error";
    include("header.inc");
    include("nav.inc");
    echo "<main class='application-form'>";
    echo "<h2>Error Processing Application</h2>";
    echo "<p class='error'>$error_message</p>";
    echo "<p><a href='apply.php'>Return to Application Form</a></p>";
    echo "</main>";
    include("footer.inc");
    exit();
}

function validate_dob($dob) {
    $parts = explode('/', $dob);
    if (count($parts) !== 3) return false;

    [$day, $month, $year] = $parts;
    if (!checkdate((int)$month, (int)$day, (int)$year)) return false;

    $currentYear = date("Y");
    if ((int)$year < 1900 || (int)$year > $currentYear - 15) {
        return false;
    }
    return true;
}

// Initialize error message
$error_message = "";

// Get and sanitize form data
$job_ref = isset($_POST['job-ref']) ? sanitizeInput($_POST['job-ref']) : "";
$first_name = isset($_POST['first-name']) ? sanitizeInput($_POST['first-name']) : "";
$last_name = isset($_POST['last-name']) ? sanitizeInput($_POST['last-name']) : "";
$dob = isset($_POST['dob']) ? sanitizeInput($_POST['dob']) : "";
$gender = isset($_POST['gender']) ? sanitizeInput($_POST['gender']) : "";
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : "";
$phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : "";
$address = isset($_POST['address']) ? sanitizeInput($_POST['address']) : "";
$suburb = isset($_POST['suburb']) ? sanitizeInput($_POST['suburb']) : "";
$state = isset($_POST['state']) ? sanitizeInput($_POST['state']) : "";
$postcode = isset($_POST['postcode']) ? sanitizeInput($_POST['postcode']) : "";
$skills = isset($_POST['skills']) ? $_POST['skills'] : array();
$other_skills = isset($_POST['other-skills']) ? sanitizeInput($_POST['other-skills']) : "";

// Server-side validation
// Check required fields
if (empty($job_ref)) {
    $error_message .= "Job reference is required. ";
}

if (empty($first_name) || !preg_match("/^[A-Za-z]{1,20}$/", $first_name)) {
    $error_message .= "First name must contain only letters (max 20 characters). ";
}

if (empty($last_name) || !preg_match("/^[A-Za-z]{1,20}$/", $last_name)) {
    $error_message .= "Last name must contain only letters (max 20 characters). ";
}

// Validate date format
if (empty($dob)) {
    $error_message .= "Date of birth is required.";
} else if (!validate_dob($dob)) {
    $error_message .= "Invalid date of birth. Please use a valid date (dd/mm/yyyy).";
}

// Validate gender
if (empty($gender)) {
    $error_message .= "Gender selection is required. ";
}

// Validate address
if (empty($address) || strlen($address) > 40) {
    $error_message .= "Street address is required (max 40 characters). ";
}

if (empty($suburb) || strlen($suburb) > 40) {
    $error_message .= "Suburb/town is required (max 40 characters). ";
}

// Validate state
$valid_states = array("VIC", "NSW", "QLD", "NT", "WA", "SA", "TAS", "ACT");
if (empty($state) || !in_array($state, $valid_states)) {
    $error_message .= "Valid state selection is required. ";
}

// Validate postcode
if (empty($postcode) || !preg_match("/^[0-9]{4}$/", $postcode)) {
    $error_message .= "Postcode must be exactly 4 digits. ";
}

if (!empty($skills) && empty($other_skills)) {
    $error_message .= "Please describe your other skills if any skill is selected.";
}

// State-postcode matching (basic validation)
$postcode_prefixes = array(
    "VIC" => array("3", "8"),
    "NSW" => array("1", "2"),
    "QLD" => array("4", "9"),
    "NT" => array("0"),
    "WA" => array("6"),
    "SA" => array("5"),
    "TAS" => array("7"),
    "ACT" => array("0", "2")
);

if (!empty($state) && !empty($postcode)) {
    $first_digit = substr($postcode, 0, 1);
    if (!in_array($first_digit, $postcode_prefixes[$state])) {
        $error_message .= "Postcode does not match the selected state. ";
    }
}

// Validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_message .= "Valid email address is required. ";
}

// Validate phone number
if (empty($phone) || !preg_match("/^[0-9\s]{8,12}$/", $phone)) {
    $error_message .= "Phone number must contain 8-12 digits or spaces. ";
}

// Validate skills
if (empty($skills)) {
    $error_message .= "At least one technical skill must be selected. ";
}

// If other skills checkbox selected but description empty
if (!empty($skills) && empty($other_skills)) {
    foreach ($skills as $skill) {
        if ($skill == "other") {
            $error_message .= "Please describe your other skills. ";
            break;
        }
    }
}

// If there are validation errors, show them
if (!empty($error_message)) {
    showError($error_message);
    exit();
}

// Convert date format from HTML date input to MySQL date format
$dob_formatted = date("Y-m-d", strtotime($dob));

// Prepare skill variables for database insertion
$skill1 = isset($skills[0]) ? $skills[0] : NULL;
$skill2 = isset($skills[1]) ? $skills[1] : NULL;
$skill3 = isset($skills[2]) ? $skills[2] : NULL;
$skill4 = isset($skills[3]) ? $skills[3] : NULL;

// Prepare database query
$query = "INSERT INTO $eoi_table 
    (job_reference, first_name, last_name, dob, gender, street_address, suburb, state, postcode, email, phone, skill1, skill2, skill3, skill4, other_skills, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'New')";

// Prepare and bind parameters
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
    $stmt, 
    "ssssssssssssssss", 
    $job_ref, 
    $first_name, 
    $last_name, 
    $dob_formatted, 
    $gender, 
    $address, 
    $suburb, 
    $state, 
    $postcode, 
    $email, 
    $phone, 
    $skill1, 
    $skill2, 
    $skill3, 
    $skill4, 
    $other_skills
);

// Execute statement
if (mysqli_stmt_execute($stmt)) {
    // Get the auto-generated EOInumber
    $eoi_number = mysqli_insert_id($conn);
    
    // Show success page
    $page_title = "Application Submitted";
    $header_title = "Application Confirmation";
    include("header.inc");
    include("nav.inc");
    echo "<main class='application-form'>";
    echo "<h2>Application Submitted Successfully</h2>";
    echo "<p>Thank you for your application!</p>";
    echo "<p>Your EOI Reference Number is: <strong>EOI" . str_pad($eoi_number, 5, '0', STR_PAD_LEFT) . "</strong></p>";
    echo "<p>Please keep this reference number for future correspondence.</p>";
    echo "<p><a href='index.php'>Return to Homepage</a></p>";
    echo "</main>";
    include("footer.inc");
} else {
    showError("Error submitting application: " . mysqli_error($conn));
}

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?> 