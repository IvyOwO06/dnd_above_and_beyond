<?php
include("inc/blogFunctions.php");
include("inc/navFunctions.php");
postBlog();
$categoryId = isset($_GET['categoryId']) ? intval($_GET['categoryId']) : null;
$posts = getBlogs($categoryId);
?>
<head>
    <!DOCTYPE html>
    <html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<?Php 
displayHeader();
?>

<div class="blog-page-containerr">
<aside class="category-sidebar" >
    <?php displayBlogCategories(); ?>
</aside>

<?php 
displayBlogs($posts);
?>
</div>  
<h2>Create a New Blog Post</h2> <!-- form to create a new blog post -->
<form class="form" method="POST" action="blog.php">
    <label for="title">Title:</label>
    <input type="text" class="form-control" name="title" required>

    <label for="content">Content:</label>
    <textarea class="form-control" name="content" required></textarea>

    <label for="categoryId">Category:</label>
    <select id="categoryId" name="categoryId" required>
        <option value="">-- Select a Category --</option>
        <?php
        $categories = getBlogCategories();
        foreach ($categories as $category) {
            echo "<option value='" . htmlspecialchars($category['blogCategoryId']) . "'>" . htmlspecialchars($category['blogCategoryName']) . "</option>";
        }
        ?>
    </select>
    <button type="submit" class="submit">Post Blog</button>
</form>