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
        <nav>
            <ul>
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
                        <a href="profile.php?userId=<?php echo $_SESSION['user']['id']; ?>" id="userNameNav">
                            <button class="dropbtn"><?php echo $_SESSION['user']['username']; ?> 
                            <i class="fa fa-caret-down"><img src="<?= $user['profilePicture'] ?? 'assets/default-avatar.png'; ?>" alt="Profile Picture" class="pfp"></i>
                            </button>
                        </a>
                        <div class="dropdown-content">
                            <a href="creations.php?userId=<?php echo $_SESSION['user']['id']; ?>">Creations</a>
                            <a href="logout.php">Log Out</a>
                        </div>
                    </div> 
                    <?php
                } 
                else
                {
                    ?>
                    <li><a href="login.php">Log In</a></li>
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

    <a href="files/copyright.pdf" target="_blank">
        <p>&copy; 2025 Dungeons and Monsters</p>
    </a>

    <a href="#" target="_blank">
        discord
    </a>

        <a href="#" target="_blank">
        discord
    </a>

        <a href="#" target="_blank">
        discord
    </a>

    <?php
}