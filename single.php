<?php require "inc/header.inc.php";

if (!isset($_GET['id'])) {
    header("Location: blog.php");
} else {
    $blog_id = $_GET['id'];
}
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <?php
            // SQL to get a single blog post. Note the use of a JOIN
            $sql = "SELECT post.post_id, post.title, post.date, post.content, author.author_id, author.first_name, author.last_name 
                FROM post 
                JOIN author 
                ON post.author = author.author_id 
                WHERE post.post_id = :blog_id";

            // PDO Prepared Statements
            $stmt = $db->prepare($sql);
            $stmt->execute(["blog_id" => $blog_id]);

            // Fetch one row
            $row = $stmt->fetch();

            // var_dump($data);

            // Blog Title
            echo "<h2>{$row->title}</h2>";
            echo "<hr>";

            // Take the date and convert it to a PHP date object
            $date = date_create($row->date);
            // Show blog post author and format the date
            echo "<p class='fw-bold'>{$row->first_name} {$row->last_name} - " . $date->format('M d, Y')  . "</p>";

            // Now get the categories for this post with SQL JOIN
            $sql = "SELECT post_category.post_id, post_category.category_id, category.category 
                FROM post_category 
                JOIN category 
                ON post_category.category_id = category.category_id 
                WHERE post_category.post_id = :post_id";

            // PDO Prepared statements
            $stmt_category = $db->prepare($sql);
            $stmt_category->execute(["post_id" => $row->post_id]);
            $categories = $stmt_category->fetchAll();

            // Generate an unordered list with categories
            echo "<p>Category</p>";
            echo "<ul>";
            foreach ($categories as $category_row) {
                echo "<li>{$category_row->category}</li>";
            }
            echo "</ul>";

            // Now get the tags for this post with SQL JOIN
            $sql = "SELECT post_tag.post_id, post_tag.tag_id, tag.tag 
                FROM post_tag 
                JOIN tag 
                ON post_tag.tag_id = tag.id 
                WHERE post_tag.post_id = :post_id";

            // PDO Prepared statements
            $stmt_tag = $db->prepare($sql);
            $stmt_tag->execute(["post_id" => $row->post_id]);
            $tags = $stmt_tag->fetchAll();

            // Generate an unordered list with tags
            echo "<p>Tag(s)</p>";

            echo "<ul>";
            foreach ($tags as $tag_row) {
                echo "<li>{$tag_row->tag}</li>";
            }
            echo "</ul>";

            // Show the blog post content
            echo "<p>{$row->content}</p>";
            ?>
        </div>
    </div>
    <?php
    require "inc/footer.inc.php";
    ?>