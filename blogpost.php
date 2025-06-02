<?php
$blogId = $_GET['id'];
require 'inc/navFunctions.php';
require 'inc/blogFunctions.php';
?>
<head>
    <!DOCTYPE html>
    <html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<?php
displayHeader();

displayBlog($blogId); 
submitComment($blogId);
?>
<h2>Submit a Comment</h2>
<form class="form" method="POST" action="blogpost.php?id=<?php echo $blogId; ?>">
    <label for="author">Your Name:</label>
    <input class="form-control" id="author" name="author" required>

    <label for="content">Comment:</label>
    <textarea class="form-control" name="content" required></textarea>

    <button type="submit" class="submit">Submit Comment</button>
</form>

<?php
displayFooter();
?>

