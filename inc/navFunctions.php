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
            <div class="footer-nav">
                <a href="index.php" class="footer-link">Home</a>
                <a href="builder.php" class="footer-link">Create Character</a>
                <a href="about.php" class="footer-link">About</a>
            </div>
            <div class="footer-social">
                <a href="https://discord.com" target="_blank" class="social-link discord" title="Join our Discord community">
                    <svg class="social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20.3 5.2c-1.5-.7-3.1-1.2-4.8-1.5-.3.5-.6 1.1-.8 1.7-1.7-.3-3.4-.3-5.1 0-.2-.6-.5-1.2-.8-1.7-1.7.3-3.3.8-4.8 1.5C1.5 8.2.7 12.1 1.3 15.8c1.2.9 2.5 1.6 3.9 2.1.3-.5.6-1.1.8-1.7-.5-.2-1-.5-1.4-.8 1.1.7 2.3 1.3 3.6 1.6 1.3.3 2.7.3 4 0 1.3-.3 2.5-.9 3.6-1.6-.5.3-1 .6-1.4.8.2.6.5 1.2.8 1.7 1.4-.5 2.7-1.2 3.9-2.1.7-4.2-.2-8-2.5-10.8zM8.3 14.6c-.7 0-1.3-.7-1.3-1.5s.6-1.5 1.3-1.5c.7 0 1.3.7 1.3 1.5s-.6 1.5-1.3 1.5zm7.4 0c-.7 0-1.3-.7-1.3-1.5s.6-1.5 1.3-1.5c.7 0 1.3.7 1.3 1.5s-.6 1.5-1.3 1.5z"/>
                    </svg>
                </a>
                <a href="https://x.com" target="_blank" class="social-link twitter" title="Follow us on X">
                    <svg class="social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M18.9 1.5h3.6l-7.9 9.1 9.3 12.3h-7.3l-5.7-7.5-6.5 7.5H2.1l8.5-9.7L1.5 1.5h7.5l5.1 6.8 5.8-6.8zm-2.2 19.3h2l-13-17.2h-2l13 17.2z"/>
                    </svg>
                </a>
                <a href="https://github.com" target="_blank" class="social-link github" title="Check our GitHub">
                    <svg class="social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.9c0-1 .3-1.6.8-2 3.1-1.5 6-5 6-9.1 0-1.3-.5-2.6-1.3-3.6.4-1 .4-2.1 0-3.1 0 0-1-.3-3.3 1.2-1.9-.5-3.9-.5-5.9 0-2.3-1.5-3.3-1.2-3.3-1.2-.4 1-.4 2.1 0 3.1-.8 1-1.3 2.3-1.3 3.6 0 4.1 2.9 7.6 6 9.1.5.4.8 1 .8 2v3.9"/>
                    </svg>
                </a>
            </div>
            <div class="footer-copyright">
                <p>© 2025 <a href="files/copyright.pdf" target="_blank" class="copyright-link">Dungeons and Monsters</a>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <?php
}