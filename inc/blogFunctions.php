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

function displayBlogs($posts) // Displays blogs from database
{
    if (empty($posts)) {
        ?>
        <div class="no-posts">
            <p>No tales have been scribed yet. Begin your chronicle!</p>
        </div>
        <?php
        return;
    }
    ?>
    <div class="blog-container">
        <div class="blog-list">
            <?php foreach ($posts as $post): ?>
                <article class="blog-post" data-category="<?php echo htmlspecialchars($post['blogCategoryId'] ?? 'general'); ?>">
                    <div class="blog-image-container">
                        <img src="<?php echo !empty($post['blogImage']) ? 'Uploads/' . htmlspecialchars($post['blogImage']) : 'images/default_blog.png'; ?>" 
                             alt="<?php echo !empty($post['blogImage']) ? htmlspecialchars($post['blogTitle']) . ' header image' : 'Default blog header image'; ?>" 
                             class="blog-header-image">
                    </div>
                    <div class="blog-content">
                        <span class="blog-category"><?php echo htmlspecialchars($post['blogCategoryId'] ?? 'General'); ?></span>
                        <h2 class="blog-title">
                            <a href="blogpost.php?id=<?php echo htmlspecialchars($post['blogId']); ?>">
                                <?php echo htmlspecialchars($post['blogTitle']); ?>
                            </a>
                        </h2>
                        <p class="blog-meta">
                            By <?php echo htmlspecialchars($post['blogAuthor']); ?> | <?php echo htmlspecialchars($post['blogPostDate']); ?>
                        </p>
                        <p class="blog-excerpt"><?php echo nl2br(htmlspecialchars(substr($post['blogContent'], 0, 150))); ?>...</p>
                        <a href="blogpost.php?id=<?php echo htmlspecialchars($post['blogId']); ?>" class="read-more">Read More</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
function displayBlog($blogId) // Displays blog details from database
{
    $comments = getBlogComments($blogId);
    $blog = getBlog($blogId);

    if (!$blog) {
        echo "<p>Blog post not found</p>";
        return;
    }
    ?>
    <article class="blog">
        <?php if (!empty($blog['blogImage'])): ?>
            <img src="Uploads/<?php echo htmlspecialchars($blog['blogImage']); ?>" alt="Blog header image" class="blog-header-image">
        <?php endif; ?>
            <h1><?php echo htmlspecialchars($blog["blogTitle"]); ?></h1>
            <p>By: <?php echo htmlspecialchars($blog["blogAuthor"]); ?> | <?php echo $blog["blogPostDate"]; ?></p>
            <p><?php echo nl2br(htmlspecialchars($blog["blogContent"])); ?></p>
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

function displayBlogCategories() {
    $categories = getBlogCategories();
    echo "<ul class='category-bar-list'>";
    echo "<li><a href='blog.php'>All</a></li>";
    foreach ($categories as $category) {
        echo "<li><a href='?categoryId=" . htmlspecialchars($category['blogCategoryId']) . "'>" . htmlspecialchars($category['blogCategoryName']) . "</a></li>";
    }
    echo "</ul>";
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
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user']['username']) && isset($_POST['content']))
    {
        $author = trim($_SESSION['user']['username']);
        $comment = trim($_POST['content']);
        $blogId = intval($blogId);

        if (!empty($author) && !empty($comment)) {
            date_default_timezone_set('Europe/Amsterdam');
            $date = date('Y-m-d H:i:s'); // Format for DATETIME
            $conn = dbConnect();
            $stmt = $conn->prepare("INSERT INTO blogPosts (blogTitle, blogContent, blogAuthor, blogPostDate, blogCategoryId, blogImage) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $content, $author, $date, $categoryId, $imageName);
            $stmt->execute();

            // Redirect to avoid resubmission on refresh
            header("Location: blogpost.php?id=" . $blogId);
            exit();
        }
    }
}
function postBlog() // Insert blog into database
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" &&
        isset($_POST['title'], $_POST['content'], $_SESSION['user']['username'], $_POST['categoryId']))
    {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $author = trim($_SESSION['user']['username']);
        $categoryId = intval($_POST['categoryId']);
        
        $imageName = null;
        if (isset($_FILES['blogImage']) && $_FILES['blogImage']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['blogImage']['tmp_name'];
            $imageName = basename($_FILES['blogImage']['name']);
            $uploadDir = 'Uploads/';
            $targetFile = $uploadDir . $imageName;

            // Create uploads folder if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Move the uploaded file
            if (!move_uploaded_file($imageTmpPath, $targetFile)) {
                $imageName = null; // Set to null if upload fails
            }
        }

        if (!empty($title) && !empty($content) && !empty($author) && $categoryId > 0) {
            date_default_timezone_set('Europe/Amsterdam');
            $date = date('Y-m-d H:i:s');

            $conn = dbConnect();
            // Include blogImage in the INSERT query
            $stmt = $conn->prepare("INSERT INTO blogPosts (blogTitle, blogContent, blogAuthor, blogPostDate, blogCategoryId, blogImage) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssis", $title, $content, $author, $date, $categoryId, $imageName);
            
            if ($stmt->execute()) {
                header("Location: blog.php");
                exit();
            } else {
                echo "Error saving blog post: " . $conn->error;
            }
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