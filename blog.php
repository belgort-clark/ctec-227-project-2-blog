<?php
$pageTitle = "Blog Home";
require "inc/header.inc.php";
?>

<div class="container mt-3">
    <div class="row">
        <h1 class="mb-5">Bruce's Technology Blog</h1>
        <?php
        // SQL to get all blog posts. Note the use of a JOIN
        $sql = "SELECT post.post_id, post.title, post.date, post.content, author.author_id, author.first_name, author.last_name 
                    FROM post 
                    JOIN author 
                    ON post.author = author.author_id";

        // PDO Prepared Statements
        // You must change this to PDO with placeholders
        $stmt = $db->prepare($sql);
        $stmt->execute();

        // Fetch all of the row(s)
        $data = $stmt->fetchAll();

        // Iterate through each of the rows
        foreach ($data as $row) {
            // Create HTML for each blog entry
            echo "<div class='col-12 mb-5'>";
            // Blog Title
            echo "<h2>{$row->title}</h2>";
            echo "<hr>";
            // Take the date and convert it to a PHP date object
            $date = date_create($row->date);
            // Show blog post author and format the date
            echo "<p class='fw-bold'>{$row->first_name} {$row->last_name} - " . $date->format('M d, Y')  . "</p>";

            // Now get the categories for this post with SQL JOIN
             // You must change this to PDO with placeholders
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
             // You must change this to PDO with placeholders
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
            $content = limit_text($row->content, 15);
            echo "<p>{$content}</p>";
            echo "<a href='single.php?id={$row->post_id}' title='Read the post'>Read more ></a>";
            echo "</div>"; // closing .col-12
        } // end of loop for Posts
        ?>
    </div> <!-- Closing for .row -->
    <?php
    require "inc/footer.inc.php";
    ?>