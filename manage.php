<?php
// Start session
session_start();

// Include database settings
require_once("settings.php");

// Check if user is logged in
if (!isset($_SESSION['manager_id']) || !isset($_SESSION['username'])) {
    // Redirect to login page
    header("Location: login.php?redirect=manage.php");
    exit();
}

// Set page specific variables
$page_title = "Riot Games - HR Management";
$header_title = "HR Management Portal";

// Create database connection
$conn = @mysqli_connect($host, $user, $password, $dbname);

// Initialize variables
$eois = array();
$message = "";
$search_job_ref = "";
$search_first_name = "";
$search_last_name = "";
$error = "";
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'EOInumber';

// Check if database connection was successful
if (!$conn) {
    $error = "Database connection error: " . mysqli_connect_error();
} else {
    // Check if EOI table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE '$eoi_table'");
    
    if (mysqli_num_rows($check_table) == 0) {
        $error = "No applications have been submitted yet.";
    } else {
        // Process form actions
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Delete EOIs by job reference
            if (isset($_POST['delete_job_ref']) && !empty($_POST['delete_job_ref'])) {
                $job_ref = mysqli_real_escape_string($conn, $_POST['delete_job_ref']);
                $delete_query = "DELETE FROM $eoi_table WHERE job_reference = '$job_ref'";
                
                if (mysqli_query($conn, $delete_query)) {
                    $affected_rows = mysqli_affected_rows($conn);
                    if ($affected_rows > 0) {
                        $message = "$affected_rows application(s) for job reference $job_ref deleted successfully.";
                    } else {
                        $message = "No applications found for job reference $job_ref.";
                    }
                } else {
                    $error = "Error deleting applications: " . mysqli_error($conn);
                }
            }
            
            // Change EOI status
            if (isset($_POST['change_status']) && isset($_POST['eoi_number']) && isset($_POST['new_status'])) {
                $eoi_number = mysqli_real_escape_string($conn, $_POST['eoi_number']);
                $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
                
                // Validate status value
                if (in_array($new_status, array('New', 'Current', 'Final'))) {
                    $update_query = "UPDATE $eoi_table SET status = '$new_status' WHERE EOInumber = $eoi_number";
                    
                    if (mysqli_query($conn, $update_query)) {
                        $message = "Status of EOI #$eoi_number updated to $new_status.";
                    } else {
                        $error = "Error updating status: " . mysqli_error($conn);
                    }
                } else {
                    $error = "Invalid status value.";
                }
            }
        }
        
        // Search filters
        $where_clauses = array();
        
        if (isset($_GET['search_job_ref']) && !empty($_GET['search_job_ref'])) {
            $search_job_ref = mysqli_real_escape_string($conn, $_GET['search_job_ref']);
            $where_clauses[] = "job_reference = '$search_job_ref'";
        }
        
        if (isset($_GET['search_first_name']) && !empty($_GET['search_first_name'])) {
            $search_first_name = mysqli_real_escape_string($conn, $_GET['search_first_name']);
            $where_clauses[] = "first_name LIKE '%$search_first_name%'";
        }
        
        if (isset($_GET['search_last_name']) && !empty($_GET['search_last_name'])) {
            $search_last_name = mysqli_real_escape_string($conn, $_GET['search_last_name']);
            $where_clauses[] = "last_name LIKE '%$search_last_name%'";
        }
        
        // Construct WHERE clause
        $where_clause = "";
        if (!empty($where_clauses)) {
            $where_clause = "WHERE " . implode(" AND ", $where_clauses);
        }
        
        // Validate sort field
        $valid_sort_fields = array('EOInumber', 'job_reference', 'first_name', 'last_name', 'status');
        if (!in_array($sort_field, $valid_sort_fields)) {
            $sort_field = 'EOInumber';
        }
        
        // Get EOIs from database
        $query = "SELECT * FROM $eoi_table $where_clause ORDER BY $sort_field";
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $eois[] = $row;
            }
        } else {
            $error = "Error fetching data: " . mysqli_error($conn);
        }
    }
}

// Include header file
include("header.inc");

// Include navigation
include("nav.inc");
?>

<main>
    <section class="admin-dashboard">
        <div class="dashboard-header">
            <h2>HR Management Portal</h2>
            
            <div class="user-panel">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                </div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php else: ?>
            <div class="dashboard-grid">
                <!-- Search Panel -->
                <div class="dashboard-card search-panel">
                    <div class="card-header">
                        <h3><i class="fas fa-search"></i> Search Applications</h3>
                    </div>
                    <div class="card-body">
                        <form action="manage.php" method="get" class="search-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="search-job-ref">Job Reference:</label>
                                    <input type="text" id="search-job-ref" name="search_job_ref" value="<?php echo htmlspecialchars($search_job_ref); ?>" placeholder="Enter job reference">
                                </div>
                                <div class="form-group">
                                    <label for="search-first-name">First Name:</label>
                                    <input type="text" id="search-first-name" name="search_first_name" value="<?php echo htmlspecialchars($search_first_name); ?>" placeholder="Enter first name">
                                </div>
                                <div class="form-group">
                                    <label for="search-last-name">Last Name:</label>
                                    <input type="text" id="search-last-name" name="search_last_name" value="<?php echo htmlspecialchars($search_last_name); ?>" placeholder="Enter last name">
                                </div>
                            </div>
                            
                            <div class="form-row sort-and-actions">
                                <div class="form-group sort-group">
                                    <label for="sort">Sort By:</label>
                                    <select id="sort" name="sort">
                                        <option value="EOInumber" <?php echo $sort_field == 'EOInumber' ? 'selected' : ''; ?>>EOI Number</option>
                                        <option value="job_reference" <?php echo $sort_field == 'job_reference' ? 'selected' : ''; ?>>Job Reference</option>
                                        <option value="first_name" <?php echo $sort_field == 'first_name' ? 'selected' : ''; ?>>First Name</option>
                                        <option value="last_name" <?php echo $sort_field == 'last_name' ? 'selected' : ''; ?>>Last Name</option>
                                        <option value="status" <?php echo $sort_field == 'status' ? 'selected' : ''; ?>>Status</option>
                                    </select>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                                    <a href="manage.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Delete Applications Panel -->
                <div class="dashboard-card delete-panel">
                    <div class="card-header">
                        <h3><i class="fas fa-trash-alt"></i> Delete Applications</h3>
                    </div>
                    <div class="card-body">
                        <form action="manage.php" method="post" onsubmit="return confirm('Are you sure you want to delete all applications for this job reference?');" class="delete-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="delete-job-ref">Job Reference:</label>
                                    <input type="text" id="delete-job-ref" name="delete_job_ref" required placeholder="Enter job reference to delete">
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete All</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- EOI Results Table -->
            <div class="dashboard-card results-panel">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Application Results <?php if (!empty($eois)): ?><span class="badge"><?php echo count($eois); ?></span><?php endif; ?></h3>
                </div>
                <div class="card-body">
                    <?php if (empty($eois)): ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <p>No applications found matching your criteria.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> EOI No.</th>
                                        <th><i class="fas fa-briefcase"></i> Job Ref.</th>
                                        <th><i class="fas fa-user"></i> Name</th>
                                        <th><i class="fas fa-envelope"></i> Email</th>
                                        <th><i class="fas fa-tag"></i> Status</th>
                                        <th><i class="fas fa-cog"></i> Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($eois as $eoi): ?>
                                        <tr>
                                            <td><?php echo $eoi['EOInumber']; ?></td>
                                            <td><?php echo htmlspecialchars($eoi['job_reference']); ?></td>
                                            <td><?php echo htmlspecialchars($eoi['first_name'] . ' ' . $eoi['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($eoi['email']); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($eoi['status']); ?>">
                                                    <?php echo htmlspecialchars($eoi['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form action="manage.php" method="post" class="status-form">
                                                    <input type="hidden" name="eoi_number" value="<?php echo $eoi['EOInumber']; ?>">
                                                    <input type="hidden" name="change_status" value="1">
                                                    <select name="new_status" class="status-select">
                                                        <option value="New" <?php echo $eoi['status'] == 'New' ? 'selected' : ''; ?>>New</option>
                                                        <option value="Current" <?php echo $eoi['status'] == 'Current' ? 'selected' : ''; ?>>Current</option>
                                                        <option value="Final" <?php echo $eoi['status'] == 'Final' ? 'selected' : ''; ?>>Final</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Update</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
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