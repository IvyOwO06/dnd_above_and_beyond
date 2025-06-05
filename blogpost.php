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
    <link rel="styesheet" href="css/blog.css">

</head>
<script>
    function toggleCommentForm() {
        const form = document.getElementById("commentForm");
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
</script>

<?php
displayHeader();

displayBlog($blogId); 
submitComment($blogId);
?>
<h2>Submit a Comment</h2>
<button class="button" onclick="toggleCommentForm()">Leave a Comment</button>
<div id="commentForm" style="display: none;">
    <form class="form" method="POST" action="blogpost.php?id=<?php echo $blogId; ?>">

        <label for="content">Comment:</label>
        <textarea class="form-control" name="content" required></textarea>

        <button type="submit" class="submit">Submit Comment</button>
    </form>
</div>

<?php
displayFooter();
?>

