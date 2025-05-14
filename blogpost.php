<?php
$blogId = $_GET['id'];
include("inc/inc.php");
htmlHead();
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

