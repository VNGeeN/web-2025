<?php
require_once 'include/database.php';

$connection = connectToDatabase();
$posts = getPostsFromDatabase($connection);
?>


<!DOCTYPE>
<htlm lang="ru">

    <head>
        <meta charset="UTF-8">
        <title>Создать пост</title>
    </head>

    <body>
        <div class="container">
            <?php foreach ($posts as $post) {
                include 'partials/post.php';
            }
            ?>
        </div>
    </body>
</htlm>