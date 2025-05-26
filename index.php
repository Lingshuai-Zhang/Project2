<?php
// Set page specific variables
$page_title = "Riot Games - Home";
$header_title = "Riot Games";

// Include header file
include("header.inc");

// Include navigation
include("nav.inc");
?>

<main>
    <section class="hero">
        <h2>Welcome to Riot Games</h2>
        <p>Experience the thrill of gaming with innovative titles and community-driven experiences. Join us on our journey to redefine interactive entertainment.</p>
        <img class="hero-img" src="images/002-rg-2021-full-lockup-offwhite.jpg" alt="Riot Games Hero Graphic" loading="lazy">
    </section>

    <section class="company-overview">
        <h2>About Riot Games</h2>
        <p>Riot Games is a global leader in the gaming industry, dedicated to creating experiences that inspire passion and engagement among millions of players worldwide. Our commitment to excellence drives our innovation and impact.</p>
        <h3>Our Services:</h3>
        <ul>
            <li>Innovative game development</li>
            <li>Esports tournaments and events</li>
            <li>Community engagement and support</li>
        </ul>
    </section>

    <section class="core-values">
        <h2>Our Core Values</h2>
        <p>We believe in a set of core values that shape every decision we make:</p>
        <ol>
            <li>Innovation – Continuously pushing creative boundaries.</li>
            <li>Integrity – Building trust through transparency and fairness.</li>
            <li>Community – Fostering a vibrant and inclusive gaming community.</li>
            <li>Excellence – Striving for outstanding quality in every project.</li>
        </ol>
    </section>

    <section class="latest-news">
        <h2>Latest News</h2>
        <article class="news-item">
            <h3>New Game Launch</h3>
            <p>Riot Games is excited to announce the launch of our newest title, which brings innovative gameplay and immersive storylines to our dedicated community.</p>
        </article>
        <article class="news-item">
            <h3>Esports Championship</h3>
            <p>Join us for the upcoming esports championship, where teams from around the globe will compete for the ultimate prize.</p>
        </article> 
    </section>

    <section class="community-engagement">
        <h2>Community Engagement</h2>
        <p>Our community is at the heart of everything we do. We organize regular events, tournaments, and meetups to foster interaction and build lasting relationships with our fans.</p>
        <img class="hero-img" src="images/community.jpg" alt="Community Engagement" loading="lazy">
    </section>

    <aside class="announcement">
        <h2>Announcements</h2>
        <p>Stay tuned for upcoming events and exclusive offers:</p>
        <ul>
            <li>Exclusive in-game rewards for active members</li>
            <li>Monthly live Q&A with the development team</li>
            <li>Beta testing opportunities for new features</li>
        </ul>
    </aside>
</main>

<?php
// Include footer
include("footer.inc");
?> 