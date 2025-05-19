<?php

require_once 'functions.php';



function displayHeader()
{
?>

<?php
    ?>
    <header>
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
                    <li><a href="profile.php?userId=<?php echo $_SESSION['user']['id']; ?>"><?php echo $_SESSION['user']['username']; ?></a></li>
                    <li><a href="creations.php?userId=<?php echo $_SESSION['user']['id']; ?>">Creations</a></li>
                    <li><a href="logout.php">Log Out</a></li>
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