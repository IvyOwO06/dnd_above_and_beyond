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
                <li><a href="login.php">login</a></li>
            </ul>
        </nav>
    </header>

    <?php
}

function displayFooter()
{
    ?>

    <a href="files/copyright.pdf" target="_blank"><p>&copy; 2025 Dungeons and Monsters</p></a>

    <?php
}