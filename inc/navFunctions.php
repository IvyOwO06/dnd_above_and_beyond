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
        <form method="GET">
            <input type="search" id="searchBar" name="search_profile" placeholder="Search users..." />
        </form>
        <?php
        searchProfile();
        ?>
        <nav>
            <ul class="navigation-links">
                <li>
                    <a href="index.php">Home</a>
                </li>
                <div class="dropdown">
                        <button class="dropbtn">
                        <a class="fa fa-caret-down">Sourcebooks</a>
                        </button>
                        <div class="dropdown-content">  
                            <a href="classes.php">Classes</a>
                            <a href="races.php">Races</a>
                        </div>
                    </div> 
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
                            <a href="campaigns.php?userId=<?php echo $_SESSION['user']['id']; ?>">Campaigns</a>
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
                <a href="classes.php" class="footer-link">Resources</a>
            </div>

            <!-- Legal Part 1 (Copyright and First Disclaimer) -->
            <div class="footer-column footer-legal-part1">
                <h3 class="footer-title">Legal</h3>
                <p class="legal-copyright">© 2025 Dungeons and Monsters. All rights reserved.</p>
                <p class="legal-disclaimer">Dungeons and Monsters, D&D Beyond 2.0, and all original content, tools, and resources found on this site are the intellectual property of Dungeons and Monsters unless otherwise stated.</p>
            </div>
        </div>

        <!-- Legal Part 2 (Second Disclaimer and Links) -->
        <div class="footer-legal">
            <p class="legal-disclaimer">This site is an independent creation and is not affiliated with, endorsed by, or sponsored by Wizards of the Coast. Dungeons & Dragons and related marks are trademarks of Wizards of the Coast LLC. Used under fair use for fan content.</p>
            <div class="legal-links">
                <a href="files/copyright.pdf" class="legal-button">Copyright</a>
            </div>
        </div>
    </footer>
    <?php
}

function searchProfileByName($userName)
{
    $db = dbConnect();

    // Sanitize the input to prevent SQL injection
    $userName = $db->real_escape_string($userName);

    // Use LIKE for partial match or = for exact match
    $sql = "SELECT userId, userName FROM user WHERE userName LIKE '%$userName%'";

    $resource = $db->query($sql) or die($db->error);

    $results = [];
    while ($row = $resource->fetch_assoc()) {
        $results[] = $row; // Each $row will have userId and name
    }

    return $results;
}
function searchProfile()
{
    $userName = $_GET['search_profile'] ?? '';

    
    if ($userName) {
        $matches = searchProfileByName($userName);

        if (empty($matches)) {
            // Redirect to the first matching user profile
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
        foreach ($matches as $match) {
            header('Location: profile.php?userId=' . $match['userId']);
            exit;
        }
    }
}