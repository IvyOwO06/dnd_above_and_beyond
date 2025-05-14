<?php

require_once 'functions.php';



function displayHeader()
{
        $currentPage = basename($_SERVER['PHP_SELF']);

    ?>
    <header>
        <nav>
            <ul>
                <li>
                    <?php if ($currentPage === 'index.php'): ?>
                    <span class="nav-current">Home</span>
                    <?php else: ?>
                    <a href="../index.php">Home</a>
                    <?php endif; ?>
                </li>
                <li><a href="character/classes.php">Classes</a></li>
                <li><a href="character/races.php">Races</a></li>
                <?php
                if (isset($_SESSION['user']))
                {
                    ?>
<<<<<<< Updated upstream
                    <li><a href="../login/profile.php?userId=<?php echo $_SESSION['user']['id']; ?>"><?php echo $_SESSION['user']['username']; ?></a></li>
                    <li><a href="../login/logout.php">Log Out</a></li>
=======
                    <li><a href="../profile.php?userId=<?php echo $_SESSION['user']['id']; ?>"><?php echo $_SESSION['user']['username']; ?></a></li>
                    <li><a href="../creations.php?userId=<?php echo $_SESSION['user']['id']; ?>">Creations</a></li>
                    <li><a href="../logout.php">Log Out</a></li>
>>>>>>> Stashed changes
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