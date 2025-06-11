<?php

require_once 'functions.php';




function displayHeader()
{
    if (isset($_SESSION['user']))
    {
        $userId = $_SESSION['user']['id'];
        $user = getUser($userId);
    }
    ?>
    <header>
        <a href="index.php" class="logo">
            <img src="images/LOGO2.png" class="logoimg">
        </a>
        <img src="https://img.icons8.com/ios_filled/512/FFFFFF/search.png" class="search-img">
        <input type="text" id="searchBar" placeholder="Search anything..." />
        <nav>
            <ul class="navigation-links">
                <li>
                    <a href="index.php">Home</a>
                </li>
                <li><a href="classes.php">Classes</a></li>
                <li><a href="races.php">Races</a></li>
                <?php
                if (isset($_SESSION['user']))
                {
                    ?>
                    <div class="dropdown">
                        <button class="dropbtn"><?php echo $_SESSION['user']['username']; ?> 
                        <i class="fa fa-caret-down"><img src="<?= $user['profilePicture'] ?? 'assets/default-avatar.png'; ?>" alt="Profile Picture" class="pfp"></i>
                        </button>
                        <div class="dropdown-content">  
                            <a href="profile.php?userId=<?php echo $_SESSION['user']['id']; ?>">Profile</a>
                            <a href="creations.php?userId=<?php echo $_SESSION['user']['id']; ?>">Creations</a>
                            <a href="createCharacter.php" onclick="return confirm('Do you want to create a character?')">Create Character</a>
                            <a href="logout.php">Log Out</a>
                        </div>
                    </div> 
                    <?php
                }
                else
                {
                    ?>
                    <li><a href="login.php" class="loginbtn">Log In</a></li>
                    <li><a href="register.php" class="signinbtn">Sign up</a></li>
                    <?php
                }
                ?>
            </ul>
        </nav>
    </header>
    <?php
}

function displayFooter()
{
    ?>
    <footer class="site-footer">
        <div class="footer-content">
            <!-- Tagline -->
            <div class="footer-column footer-tagline">
                <h3 class="footer-title">Unleash Your Adventure</h3>
                <p>Join our community to create epic characters, manage campaigns, and explore a world of fantasy.</p>
            </div>

            <!-- Navigation Links -->
            <div class="footer-column footer-nav">
                <h3 class="footer-title">Explore</h3>
                <a href="index.php" class="footer-link">Home</a>
                <a href="builder.php" class="footer-link">Create Character</a>
                <a href="about.php" class="footer-link">About</a>
                <a href="resources.php" class="footer-link">Resources</a>
                <a href="contact.php" class="footer-link">Contact</a>
            </div>

            <!-- Newsletter Signup -->
            <div class="footer-column footer-newsletter">
                <h3 class="footer-title">Stay Updated</h3>
                <p>Subscribe for news, updates, and exclusive content.</p>
                <form action="subscribe.php" method="POST" class="newsletter-form">
                    <input type="email" name="email" placeholder="Enter your email" required aria-label="Email address">
                    <button type="submit" class="newsletter-button">Subscribe</button>
                </form>
            </div>

            <!-- Social Links -->
            <div class="footer-column footer-social">
                <h3 class="footer-title">Connect</h3>
                <div class="social-links">
                    <a href="https://discord.com" target="_blank" class="social-link discord" title="Join our Discord community">
                        
                    </a>
                    <a href="https://x.com" target="_blank" class="social-link twitter" title="Follow us on X">

                    </a>
                    <a href="https://github.com" target="_blank" class="social-link github" title="Check our GitHub">

                    </a>
                </div>
            </div>  
        </div>
        
    </footer>
        
<section class="legal-section">
    <div class="legal-content">
        <div class="legal-text-buttons">
            <div class="legal-text">
                <p class="legal-copyright">© 2025 Dungeons and Monsters. All rights reserved.</p>
                <p class="legal-disclaimer">Dungeons and Monsters, D&D Beyond 2.0, and all original content, tools, and resources found on this site are the intellectual property of Dungeons and Monsters unless otherwise stated.</p>
                <p class="legal-disclaimer">This site is an independent creation and is not affiliated with, endorsed by, or sponsored by Wizards of the Coast. Dungeons & Dragons and related marks are trademarks of Wizards of the Coast LLC. Used under fair use for fan content.</p>
            </div>
            <div class="legal-links">
                <a href="tos.php" class="legal-button">Terms of Service</a>
                <a href="privacy.php" class="legal-button">Privacy Policy</a>
                <a href="files/copyright.pdf" class="legal-button">Copyright</a>
            </div>
        </div>
    </div>
</section>
    <?php
}

