<?php
include("../inc/blogFunctions.php");
postBlog();
$categoryId = isset($_GET['categoryId']) ? intval($_GET['categoryId']) : null;
$posts = getBlogs($categoryId);
?>

<h1><a href="blog.php">Gameworld Blog</a></h1>

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

    <label for="author">Your Name:</label>
    <input type="text" class="form-control" name="author" required>

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