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
    <meta http-equiv="refresh" content="1800">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
    </head>
<body>
    <?php
    displayHeader();
    timer();
    ?>

<div class="hero">
  <div class="hero-overlay">
    <h1>Dungeons and Monsters</h1>
  </div>
</div>


    <main>
        <section class="welcome">
            <h2>DUNGEONS AND MONSTERS</h2>
            <P>Step into a world where your D&D stories come alive! Our site lets you create, manage, and dive deep into your campaigns and characters! Complete with sleek sheets, DM tools, and even dice rolling at your fingertips. Whether you’re crafting epic quests or building legendary heroes, we’ve got your back. Ready to level up your game? Join us and bring your adventures to life like never before!</P>
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
    
    <a href="campaigns?userId=<?php echo isset($_SESSION['user']['id']); ?>">
        <div class="image-container">
            <img src="Uploads/beholder.png" alt="image description">
            <div class="text-overlay">
                <h2>Campaign Management</h2>
            </div>
        </div>
    </a>

    <a href="classes.php">
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
        <div class="image-link image-link-small">
            <img src="https://www.wargamer.com/wp-content/sites/wargamer/2024/07/dnd-art-video-backgrounds.jpg" class="slide-image" alt="Featured Adventure: Upper Realm Quest">
        </div>
        <div class="image-link image-link-small">
            <img src="https://cdn.mos.cms.futurecdn.net/ac5WH9LkYKbWMVYrXborF7.jpg" class="slide-image" alt="Featured Guide: Character Creation Tips">
        </div>
    </div>
    <div class="image-link image-link-large">
        <img src="https://cdn.mos.cms.futurecdn.net/neigNTpGV7TYP4LLrJuq9A-1200-80.png" class="slide-image" alt="Featured Campaign: Dragon's Lair">
    </div>
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
    <h1 class="blog-preview-title">Join the conversation!</h1>
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
