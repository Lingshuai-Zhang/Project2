<?php
// Set page specific variables
$page_title = "Riot Games - About";
$header_title = "About Our Team";

// Include header file
include("header.inc");

// Include navigation
include("nav.inc");
?>

<main>
    <!-- Nested list: Group name, class time, student IDs -->
    <section>
        <h2>Group Info</h2>
        <ul>
            <li>Group Name: Top player
                <ul>
                    <li>Class Time: Monday 2:30 PM</li>
                    <li>Class Day: Monday</li>
                </ul>
            </li>
            <li>Student IDs:
                <ul class="student-id-list">
                    <li class="student-id">Changge Sun<br>104509954</li>
                    <li class="student-id">Lingshuai Zhang<br>105520095</li>
                    <li class="student-id">Shuhuan Gao<br>105520244</li>
                    <li class="student-id">Xudong Gao<br>104511108</li>
                </ul>
            </li>
            <li>Tutor: Enrique Nicolás Ketterer (Nick)</li>
        </ul>
    </section>

    <!-- Definition list: Member contributions -->
    <section>
        <h2>Member Contributions</h2>
        <dl>
            <dt>Lingshuai Zhang</dt>
            <dd>Web developer and information collector —— responsible for the development and design of about.html page, and finding suitable images and references for the web page. Contributed to the PHP conversion of the project including common elements extraction.</dd>

            <dt>Xudong Gao</dt>
            <dd>Backend Developer - Handled form and logic. Implemented server-side validation for the EOI form and created the database schema for storing job applications.</dd>

            <dt>Changge Sun</dt>
            <dd>Program developer and page designer —— mainly responsible for the development and design of jobs.html and style.css, and building the overall layout of the page. Also created the database structure for the jobs listings.</dd>

            <dt>Shuhuan Gao</dt>
            <dd>Project manager and web developer —— mainly responsible for designing index.html and apply.html, and responsible for task assignment and supervision to promote project progress. Implemented the manager functionality and enhancements for the PHP project.</dd>
        </dl>
    </section>

    <!-- Group photo, figure element -->
    <section>
        <h2>Our Team Photo</h2>
        <figure class="team-photo">
            <img src="images/group-photo.jpg" alt="Our Team Photo" width="300" loading="lazy">
            <figcaption>Team Riot Devs - United for Innovation</figcaption>
        </figure>
    </section>

    <!-- HTML table: Member interests, with merged cells -->
    <section>
        <h2>Team Interests</h2>
        <table class="team-table">
            <caption>Members' Interests</caption>
            <thead>
                <tr>
                    <th>Name</th>
                    <th colspan="2">Interests</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Lingshuai Zhang</td>
                    <td>FPS Games</td>
                    <td>Gaming UI Design</td>
                </tr>
                <tr>
                    <td>Xudong Gao</td>
                    <td>Backend Programming</td>
                    <td>Football</td>
                </tr>
                <tr>
                    <td>Changge Sun</td>
                    <td>Motion Design</td>
                    <td>Comics</td>
                </tr>
                <tr>
                    <td>Shuhuan Gao</td>
                    <td>Project Planning</td>
                    <td>Strategy Games</td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- Extended content (optional bonus points) -->
    <section>
        <h2>Extra Information</h2>
        <p>We come from diverse backgrounds and cities across China and Australia.</p>
        <ul>
            <li>We love working with HTML/CSS/JavaScript/PHP</li>
            <li>Some members enjoy music composition and visual storytelling</li>
            <li>All members are experienced in Git and VS Code</li>
            <li>We've successfully converted this project from static HTML to dynamic PHP</li>
        </ul>
    </section>
</main>

<?php
// Include footer
include("footer.inc");
?>