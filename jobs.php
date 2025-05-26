<?php
// Set page specific variables
$page_title = "Riot Games - Careers";
$header_title = "Riot Games Careers";

// Include header file
include("header.inc");

// Include navigation
include("nav.inc");

// Include database settings
require_once("settings.php");

// Create database connection
$conn = @mysqli_connect($host, $user, $password, $dbname);

// Function to display jobs from the database
function displayJobs($conn, $jobs_table) {
    // Create jobs table if it doesn't exist
    $jobs_table_sql = "CREATE TABLE IF NOT EXISTS $jobs_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        job_reference VARCHAR(10) NOT NULL UNIQUE,
        position_title VARCHAR(50) NOT NULL,
        reports_to VARCHAR(50) NOT NULL,
        salary_range VARCHAR(50) NOT NULL,
        position_description TEXT NOT NULL,
        key_responsibilities TEXT NOT NULL,
        essential_requirements TEXT NOT NULL,
        preferable_requirements TEXT NOT NULL
    )";
    
    if (!mysqli_query($conn, $jobs_table_sql)) {
        echo "<p class='error'>Error creating jobs table: " . mysqli_error($conn) . "</p>";
        return;
    }
    
    // Check if the table has data
    $check_data = mysqli_query($conn, "SELECT COUNT(*) as count FROM $jobs_table");
    $row = mysqli_fetch_assoc($check_data);
    
    // If table is empty, insert sample job data
    if ($row['count'] == 0) {
        // Sample job data
        $jobs_data = array(
            array(
                'G001',
                'Senior Game Developer',
                'Lead Game Developer',
                '$90,000 - $120,000 per annum',
                'We are seeking a talented Senior Game Developer to join our team. You will be responsible for developing and implementing game features, optimizing performance, and collaborating with designers and artists to create immersive gaming experiences.',
                'Develop and implement game features using C++ and Unreal Engine;Optimize game performance and memory usage;Collaborate with designers and artists to implement game mechanics;Write clean, maintainable, and well-documented code;Participate in code reviews and provide technical guidance;Debug and fix issues in existing game systems',
                'Bachelor\'s degree in Computer Science or related field;5+ years of experience in game development;Strong proficiency in C++ and Unreal Engine;Experience with 3D mathematics and physics;Excellent problem-solving skills',
                'Experience with multiplayer game development;Knowledge of shader programming;Experience with version control systems (Git);Portfolio of completed game projects'
            ),
            array(
                'U002',
                'Senior UI/UX Designer',
                'Creative Director',
                '$85,000 - $110,000 per annum',
                'We are looking for a creative UI/UX Designer to join our team. You will be responsible for creating intuitive and engaging user interfaces for our games, ensuring a seamless user experience across all platforms.',
                'Design and prototype user interfaces for games;Create wireframes, mockups, and interactive prototypes;Conduct user research and usability testing;Collaborate with developers to implement designs;Ensure consistency across all game interfaces;Stay updated with UI/UX trends and best practices',
                'Bachelor\'s degree in Design or related field;4+ years of experience in UI/UX design;Proficiency in Figma, Adobe XD, or similar tools;Strong portfolio demonstrating UI/UX work;Understanding of game design principles',
                'Experience with motion design;Knowledge of HTML/CSS;Experience in game industry;Understanding of accessibility standards'
            ),
            array(
                'Q003',
                'Senior QA Tester',
                'QA Manager',
                '$75,000 - $95,000 per annum',
                'We are seeking an experienced QA Tester to join our quality assurance team. You will be responsible for ensuring the quality of our games through systematic testing, bug reporting, and quality control processes.',
                'Develop and execute test plans and test cases;Identify and document software defects and issues;Perform regression testing and verify bug fixes;Collaborate with development teams to ensure quality standards;Participate in agile development processes;Create and maintain test documentation',
                'Bachelor\'s degree in Computer Science or related field;3+ years of experience in QA testing;Strong understanding of software testing methodologies;Experience with bug tracking systems (JIRA, Bugzilla);Excellent analytical and problem-solving skills',
                'Experience with automated testing tools;Knowledge of game development processes;Experience with performance testing;Understanding of agile development methodologies'
            )
        );
        
        // Insert sample data
        $insert_query = "INSERT INTO $jobs_table 
            (job_reference, position_title, reports_to, salary_range, position_description, key_responsibilities, essential_requirements, preferable_requirements) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $insert_query);
        
        foreach ($jobs_data as $job) {
            mysqli_stmt_bind_param($stmt, "ssssssss", $job[0], $job[1], $job[2], $job[3], $job[4], $job[5], $job[6], $job[7]);
            mysqli_stmt_execute($stmt);
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // Get all jobs from the database
    $query = "SELECT * FROM $jobs_table";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // Create navigation menu for job positions
        echo '<nav class="position-nav">';
        echo '<ul>';
        
        $job_ids = array();
        $result_copy = mysqli_query($conn, $query); // Create a copy of the result for iteration
        
        while ($job = mysqli_fetch_assoc($result_copy)) {
            $job_id = strtolower(str_replace(' ', '-', substr($job['position_title'], 7)));
            $job_ids[] = $job_id;
            echo '<li><a href="#' . $job_id . '">' . substr($job['position_title'], 7) . '</a></li>';
        }
        
        echo '</ul>';
        echo '</nav>';
        
        // Display each job
        $index = 0;
        while ($job = mysqli_fetch_assoc($result)) {
            echo '<article class="job-position" id="' . $job_ids[$index] . '">';
            echo '<h3>' . $job['position_title'] . ' (Reference: ' . $job['job_reference'] . ')</h3>';
            
            echo '<div class="job-overview">';
            echo '<p><strong>Position Title:</strong> ' . $job['position_title'] . '</p>';
            echo '<p><strong>Reports To:</strong> ' . $job['reports_to'] . '</p>';
            echo '<p><strong>Salary Range:</strong> ' . $job['salary_range'] . '</p>';
            echo '</div>';
            
            echo '<section class="job-description">';
            echo '<h4>Position Description</h4>';
            echo '<p>' . $job['position_description'] . '</p>';
            echo '</section>';
            
            echo '<section class="responsibilities">';
            echo '<h4>Key Responsibilities</h4>';
            echo '<ol>';
            $responsibilities = explode(';', $job['key_responsibilities']);
            foreach ($responsibilities as $responsibility) {
                echo '<li>' . $responsibility . '</li>';
            }
            echo '</ol>';
            echo '</section>';
            
            echo '<section class="requirements">';
            echo '<h4>Requirements</h4>';
            
            echo '<div class="essential">';
            echo '<h5>Essential</h5>';
            echo '<ul>';
            $essential = explode(';', $job['essential_requirements']);
            foreach ($essential as $requirement) {
                echo '<li>' . $requirement . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            
            echo '<div class="preferable">';
            echo '<h5>Preferable</h5>';
            echo '<ul>';
            $preferable = explode(';', $job['preferable_requirements']);
            foreach ($preferable as $requirement) {
                echo '<li>' . $requirement . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            
            echo '</section>';
            echo '</article>';
            
            $index++;
        }
    } else {
        echo "<p>No job positions available at this time.</p>";
    }
}
?>

<main>
    <!-- Job Listing Section: Contains all open positions -->
    <section class="job-listing">
        <h2>Open Positions</h2>
        
        <?php
        if ($conn) {
            displayJobs($conn, $jobs_table);
            mysqli_close($conn);
        } else {
            echo "<p class='error'>Database connection error: " . mysqli_connect_error() . "</p>";
            
            // Display static content as fallback
            echo '<nav class="position-nav">';
            echo '<ul>';
            echo '<li><a href="#game-dev">Game Developer</a></li>';
            echo '<li><a href="#ui-ux">UI/UX Designer</a></li>';
            echo '<li><a href="#qa-tester">QA Tester</a></li>';
            echo '</ul>';
            echo '</nav>';
            
            // Include static job descriptions
            include("static_jobs.php");
        }
        ?>
    </section>

    <!-- Sidebar: Contains additional information and application process -->
    <aside class="job-info">
        <!-- Company welfare information -->
        <h3>Additional Information</h3>
        <p>Learn more about our work environment and benefits:</p>
        <ul>
            <li>Competitive salary packages</li>
            <li>Health and wellness benefits</li>
            <li>Career development opportunities</li>
            <li>Flexible work arrangements</li>
            <li>Game development resources and tools</li>
            <li>Team building activities and events</li>
        </ul>

        <!-- Application Process Description -->
        <h3>Application Process</h3>
        <p>To apply for any of these positions, please follow these steps:</p>
        <ol>
            <li>Review the position requirements carefully</li>
            <li>Prepare your resume and portfolio</li>
            <li>Complete the online application form</li>
            <li>Include the position reference number in your application</li>
        </ol>
        <p>We look forward to receiving your application!</p>
    </aside>
</main>

<?php
// Include footer
include("footer.inc");
?> 