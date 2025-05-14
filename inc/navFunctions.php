<?php

require_once 'functions.php';



function displayHeader()
{
    ?>

    <header>
        <div id="Logo"><a href="index.php">D&M</a></div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="character/classes.php">Classes</a></li>
                <li><a href="character/races.php">Races</a></li>
                <?php
                if (isset($_SESSION['user']))
                {
                    ?>
                    <li><a href="login/profile.php?userId=<?php echo $_SESSION['user']['id']; ?>"><?php echo $_SESSION['user']['username']; ?></a></li>
                    <li><a href="login/logout.php">Log Out</a></li>
                    <?php
                } 
                else
                {
                    ?>
                    <li><a href="login/login.php">Log In</a></li>
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

    <?php
}