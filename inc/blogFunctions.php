<?php

require_once 'functions.php';

function getBlogs($categoryId = null) //fetch blogs, if no id default to null
{
    $db = dbConnect();
    if ($categoryId) {
        $stmt = $db->prepare("SELECT * FROM blogposts WHERE blogCategoryId = ? ORDER BY blogPostDate DESC");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->query("SELECT * FROM blogposts ORDER BY blogPostDate DESC") or die($db->error);
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createBlogPost($title, $content, $author) //inserts data into database
{
    $db = dbConnect();
    $stmt = $db->prepare("INSERT INTO blogposts (title, content, author) VALUES (?, ?, ?)");  //placeholder values that will later be bound
    return $stmt->execute();
}

function displayBlogs($posts) //displays blogs from database
{
    if (empty($posts)) {
        ?><p>No blog posts yet!</p><?php
        return;
    }
    ?>
    <div class='blog-container'>
        <div class='blog-list'>
            <?php foreach ($posts as $post): ?>
                <article class='blog-post'>
                    <h2><a href='blogpost.php?id=<?php echo htmlspecialchars($post['id']); ?>'><?php echo htmlspecialchars($post['blogTitle']); ?></a></h2>
                    <p><em>By <?php echo htmlspecialchars($post['blogAuthor']); ?> on <?php echo $post['blogPostDate']; ?></em></p>

                    <p><?php echo nl2br(htmlspecialchars(substr($post['blogContent'], 0, 200))); ?>...</p>
                    <a href='blogpost.php?id=<?php echo htmlspecialchars($post['blogId']); ?>'>Read more</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function displayBlog($blogId) //displays blog details from database
{
    $comments = getBlogComments($blogId);
    $blog = getBlog($blogId);

    if (!$blog) {
        echo "<p>Blog post not found</p>";
        return;
    }
    ?>
    <article class="blog">
            <h1><?php echo htmlspecialchars($blog["blogTitle"]); ?> </h1>
            <p>By: <?php echo htmlspecialchars($blog["blogAuthor"]); ?> | <?php echo $blog["blogPostDate"]; ?> </p>
            <p><?php echo nl2br(htmlspecialchars($blog["blogContent"])); ?> </p>
            <hr>
            <h2>Comments</h2>
            <?php
            if (count($comments) > 0) {
                foreach ($comments as $comment) {
                    echo "<div class='comment'>";
                    echo "<p><strong>" . htmlspecialchars($comment['commenterName']) . "</strong></p>";
                    echo "<p>" . nl2br(htmlspecialchars($comment['commentContent'])) . "</p>";
                    echo "<p><em>Posted on " . $comment['commentDate'] . "</em></p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No comments yet.</p>";
            }
            ?>
    </article>
    <?php
}

function getBlogCategories() //get blog per category
{
    $db = dbConnect();
    $sql = "SELECT * FROM blogCategories";
    $result = $db->query($sql) or die ($db->error);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function displayBlogCategories() //display blog per category
{
    $categories = getBlogCategories();
    echo "<nav class='sidebar-categories'>";
    echo "<h3>Categories</h3>";
    echo "<ul>";
    foreach ($categories as $category) {
        echo "<li><a href='?categoryId=" . htmlspecialchars($category['blogCategoryId']) . "'>" . htmlspecialchars($category['blogCategoryName']) . "</a></li>";
    }
    echo "</ul>";
    echo "</nav>";
}

function getBlogComments($blogId) //fetch blog comments from database
{
    $db = dbConnect();
    $sql = "SELECT * FROM blogComments WHERE postId = ? ORDER BY commentDate DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $blogId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function submitComment($blogId, $author = null, $comment = null) //insert blog comment into database
{
    // Check if the form is submitted and if the fields exist
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['author']) && isset($_POST['content']))
    {
        $author = trim($_POST['author']);
        $comment = trim($_POST['content']);
        $blogId = intval($blogId);

        if (!empty($author) && !empty($comment)) {
            date_default_timezone_set('Europe/Amsterdam');
            $date = date('Y-m-d H:i:s'); // Format for DATETIME
            $conn = dbConnect();
            $stmt = $conn->prepare("INSERT INTO blogComments (postId, commentDate, commenterName, commentContent) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $blogId, $date, $author, $comment);
            $stmt->execute();

            // Redirect to avoid resubmission on refresh
            header("Location: blogpost.php?id=" . $blogId);
            exit();
        }
    }
}

function postBlog() //insert blog into database
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" &&
        isset($_POST['title'], $_POST['content'], $_POST['author'], $_POST['categoryId']))
    {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $author = trim($_POST['author']);
        $categoryId = intval($_POST['categoryId']); 

        if (!empty($title) && !empty($content) && !empty($author) && $categoryId > 0) {
            date_default_timezone_set('Europe/Amsterdam');
            $date = date('Y-m-d H:i:s');

            $conn = dbConnect();
            $stmt = $conn->prepare("INSERT INTO blogPosts (blogTitle, blogContent, blogAuthor, blogPostDate, blogCategoryId) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $content, $author, $date, $categoryId);
            $stmt->execute();

            header("Location: blog.php");
            exit();
        }
    }
}

function getBlog($blogId) //fetch single blog from database by id
{
    $db = dbConnect();
    $sql = "SELECT * FROM blogPosts WHERE blogId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $blogId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}