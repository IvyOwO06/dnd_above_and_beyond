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
                <li><a href="classes.php">Classes</a></li>
                <li><a href="races.php">Races</a></li>
                <li><a href="login.php">Log In</a></li>
                <li><a href="logout.php">Log Out</a></li>
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