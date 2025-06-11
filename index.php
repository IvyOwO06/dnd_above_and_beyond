<?php
require 'inc/navFunctions.php';
require 'inc/blogFunctions.php';
$posts = getBlogs();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
    </head>
<body>
    <?php
    displayHeader();
    ?>

<div class="hero">
  <div class="hero-overlay">
    <h1>Dungeons and Monsters</h1>
    <p>Your gateway to unforgettable adventures</p>
  </div>
</div>


    <main>
        <section class="welcome">
            <h2>PLACEHOLDER TEXT</h2>
            <P>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum in sit sapiente consectetur laudantium repudiandae error, maxime temporibus? Cumque recusandae ipsum quisquam nesciunt voluptatum quod perspiciatis natus iusto error ut.</P>
        </section>
    </main>

<section class="image-button-row">
    <a href="createCharacter.php">
        <div class="image-container">
            <img src="https://pngimg.com/d/wizard_PNG5.png" alt="image description">
            <div class="text-overlay">
                <h2>Character Creation</h2>
            </div>
        </div>
    </a>
    
    <a href="#">
        <div class="image-container">
            <img src="Uploads/beholder.png" alt="image description">
            <div class="text-overlay">
                <h2>Campaign Management</h2>
            </div>
        </div>
    </a>

    <a href="#">
        <div class="image-container">
            <img src="Uploads/book.png">
            <div class="text-overlay">
                <h2>Sourcebooks</h2>
            </div>
        </div>
    </a>
</section>

<section class="quick-links">
    <div class="image-link-grid">
        <div class="image-link-stack">
            <a href="page1.php" class="image-link image-link-small">
                <img src="https://placehold.co/300x300" class="slide-image" alt="Featured Adventure: Upper Realm Quest">
            </a>
            <a href="page2.php" class="image-link image-link-small">
                <img src="https://placehold.co/300x300" class="slide-image" alt="Featured Guide: Character Creation Tips">
            </a>
        </div>
        <a href="page3.php" class="image-link image-link-large">
            <img src="https://placehold.co/300x300" class="slide-image" alt="Featured Campaign: Dragon's Lair">
        </a>
    </div>
</section>

    <section class="features">
        <div class="card-grid">
            <div class="card">
                <h3>Character Vault</h3>
                <p>Create, manage, and evolve your adventurers across epic campaigns.</p>
            </div>
            <div class="card"> 
                <h3>Campaign Management</h3>
                <p>Manage and create your campaigns, maps, timelines, as a game master.</p>
            </div>
            <div class="card">
                <h3>Quality-of-life tools</h3>
                <p>Dice rollers, initiative trackers, and more, made for easy and quick access during sessions.</p>
            </div>
            <div class="card">
                <h3>NPC & Quest logs</h3>
                <p>Keep track of your favorite shopkeeper and hot BBEG.</p>
            </div>
        </div>
    </section>

    <section class="blog-preview">
    <h1 class="blog-preview-title">Recent Articles</h1>
    <div class="blog-grid">
        <?php
        displayBlogs($posts);
        ?>
    </div>
    <a href="blog.php" class="see-all-posts">See All Posts</a>
</section>

    <?php
    displayFooter();
    ?>
</body>
</html>