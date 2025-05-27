<?php

?>

<div>
    <h2><?= htmlentities($post['title']);?></h2>
    <img src="/assets/images/posts/<?= htmlentities($post['filename']); ?>" alt="<?= htmlentities($post['title']); ?>">
</div>