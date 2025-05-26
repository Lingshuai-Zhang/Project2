<?php
// Set page specific variables
$page_title = "Riot Games - Job Application";
$header_title = "Job Application";

// Include header file
include("header.inc");

// Include navigation
include("nav.inc");

// Include database settings
require_once("settings.php");

// Create database connection
$conn = @mysqli_connect($host, $user, $password, $dbname);

// Generate options for job reference dropdown from the jobs table
$job_options = "";
if ($conn) {
    // Check if jobs table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE '$jobs_table'");
    
    if (mysqli_num_rows($check_table) > 0) {
        $query = "SELECT job_reference, position_title FROM $jobs_table";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $job_options .= "<option value=\"" . $row['job_reference'] . "\">" . 
                    $row['job_reference'] . " - " . $row['position_title'] . "</option>";
            }
        } else {
            // Default options if no jobs are found
            $job_options = '
                <option value="G001">G001 - Game Dev</option>
                <option value="U002">U002 - UX Designer</option>
                <option value="Q003">Q003 - QA Tester</option>
            ';
        }
    } else {
        // Default options if table doesn't exist
        $job_options = '
            <option value="G001">G001 - Game Dev</option>
            <option value="U002">U002 - UX Designer</option>
            <option value="Q003">Q003 - QA Tester</option>
        ';
    }
} else {
    // Default options if connection fails
    $job_options = '
        <option value="G001">G001 - Game Dev</option>
        <option value="U002">U002 - UX Designer</option>
        <option value="Q003">Q003 - QA Tester</option>
    ';
}
?>

<main>
  <section class="application-form">
    <h2>Apply for a Position</h2>
    <form action="process_eoi.php" method="post" id="application-form" novalidate="novalidate">
      <!-- Work Information -->
      <div class="form-section">
        <h3>Job Information</h3>
        <div class="form-group">
          <label for="job-ref">Job Reference Number:</label>
          <select id="job-ref" name="job-ref" required>
            <option value="">Select Reference</option>
            <?php echo $job_options; ?>
          </select>
          <div class="error-message" id="job-ref-error"></div>
        </div>
      </div>

      <!--personal information -->
      <div class="form-section">
        <h3>Personal Information</h3>
        <div class="form-row">
          <div class="form-group">
            <label for="first-name">First Name:</label>
            <input type="text" id="first-name" name="first-name" required maxlength="20" pattern="[A-Za-z]{1,20}">
            <div class="error-message" id="first-name-error"></div>
            <small class="help-text">Maximum 20 alphabetic characters</small>
          </div>
          <div class="form-group">
            <label for="last-name">Last Name:</label>
            <input type="text" id="last-name" name="last-name" required maxlength="20" pattern="[A-Za-z]{1,20}">
            <div class="error-message" id="last-name-error"></div>
            <small class="help-text">Maximum 20 alphabetic characters</small>
          </div>
        </div>
        
        <div class="form-group">
          <label for="dob">Date of Birth (DD/MM/YYYY):</label>
          <input type="text" id="dob" name="dob" required placeholder="DD/MM/YYYY" pattern="^(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/(19|20)\d\d$">
          <div class="error-message" id="dob-error"></div>
          <small class="help-text">Format: DD/MM/YYYY (e.g., 01/01/2000)</small>
        </div>

        <div class="form-group">
          <fieldset>
            <legend>Gender</legend>
            <div class="radio-group">
              <label><input type="radio" name="gender" value="male" required> Male</label>
              <label><input type="radio" name="gender" value="female"> Female</label>
              <label><input type="radio" name="gender" value="other"> Other</label>
            </div>
            <div class="error-message" id="gender-error"></div>
          </fieldset>
        </div>
      </div>

      <!-- Contact Details -->
      <div class="form-section">
        <h3>Contact Information</h3>
        <div class="form-row">
          <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>
            <div class="error-message" id="email-error"></div>
          </div>
          <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" pattern="[0-9\s]{8,12}" maxlength="12" required
              title="Please enter 8 to 12 digits.">
            <div class="error-message" id="phone-error"></div>
            <small class="help-text">8 to 12 digits or spaces</small>
          </div>
        </div>
      </div>

      <!-- Address Information -->
      <div class="form-section">
        <h3>Address Information</h3>
        <div class="form-group">
          <label for="address">Street Address:</label>
          <input type="text" id="address" name="address" required maxlength="40">
          <div class="error-message" id="address-error"></div>
          <small class="help-text">Maximum 40 characters</small>
        </div>
        <div class="form-group">
          <label for="suburb">Suburb/Town:</label>
          <input type="text" id="suburb" name="suburb" required maxlength="40">
          <div class="error-message" id="suburb-error"></div>
          <small class="help-text">Maximum 40 characters</small>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="state">State:</label>
            <select id="state" name="state" required>
              <option value="">Select State</option>
              <option value="VIC">VIC</option>
              <option value="NSW">NSW</option>
              <option value="QLD">QLD</option>
              <option value="NT">NT</option>
              <option value="WA">WA</option>
              <option value="SA">SA</option>
              <option value="TAS">TAS</option>
              <option value="ACT">ACT</option>
            </select>
            <div class="error-message" id="state-error"></div>
          </div>
          <div class="form-group">
            <label for="postcode">Postcode:</label>
            <input type="text" id="postcode" name="postcode" pattern="^[0-9]{4}$" maxlength="4" required
              title="Please enter a 4-digit postcode.">
            <div class="error-message" id="postcode-error"></div>
            <small class="help-text">4 digits matching state</small>
          </div>
        </div>
      </div>

      <!-- Skill Information -->
      <div class="form-section">
        <h3>Skills Information</h3>
        <div class="form-group">
          <fieldset>
            <legend>Required Technical Skills:</legend>
            <div class="checkbox-group">
              <label><input type="checkbox" name="skills[]" value="html" class="skill-checkbox"> HTML</label>
              <label><input type="checkbox" name="skills[]" value="css" class="skill-checkbox"> CSS</label>
              <label><input type="checkbox" name="skills[]" value="js" class="skill-checkbox"> JavaScript</label>
              <label><input type="checkbox" name="skills[]" value="php" class="skill-checkbox"> PHP</label>
            </div>
            <div class="error-message" id="skills-error"></div>
          </fieldset>
        </div>
        <div class="form-group">
          <label for="other-skills">Other Skills:</label>
          <textarea id="other-skills" name="other-skills" rows="3"></textarea>
          <div class="error-message" id="other-skills-error"></div>
          <small class="help-text">Required if any technical skill is selected</small>
        </div>
      </div>

      <!-- Submit button -->
      <div class="form-group submit-group">
        <button type="submit">Submit Application</button>
        <button type="reset" class="btn-secondary">Reset Form</button>
      </div>
    </form>
  </section>
</main>



<?php
// Include footer
include("footer.inc");
?>