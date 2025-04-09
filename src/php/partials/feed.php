<?php
require_once('validation.php');

$page = "feed";
$feedData = json_decode(file_get_contents('../../json/posts.json'), true);
$userData = json_decode(file_get_contents('../../json/users.json'), true);

$errors = validatePostsData($feedData);

if (!empty($errors)) {
    echo '<h2>Ошибки валидации постов:</h2>';
    echo '<pre>' . print_r($errors, true) . '</pre>';
    exit;
}

$errors = validateUserData($userData);

if (!empty($errors)) {
    echo '<h2>Ошибки валидации пользователей:</h2>';
    echo '<pre>' . print_r($errors, true) . '</pre>';
    exit;
}

$posts = [];
$i = 0;
foreach ($feedData as $feed) {
    foreach ($userData as $user) {
        if ($feed['user_id'] === $user['id']) {
            $posts[$feed['id']] = [
                'profile_name' => $user['profile_properties']['profile_name'],
                'avatar' => $user['profile_properties']['avatar'],
                'post_picture' => $feed['post_picture'],
                'comment' => $feed['comment'],
                'reactions' => $feed['reactions']
            ];

        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Лента</title>
    <link rel="stylesheet" href="../../css/fonts.css" />
    <link rel="stylesheet" href="../../css/feed.css">
</head>

<body>
    <div class="main-container">
        <?php require_once('sidebar.php'); ?>
        <div class="container">
            <?php foreach ($posts as $post): ?>
                <div class="card">
                    <div class="card__head">
                        <div class="card__author-info">
                            <img class="card__avatar" src="<?= $post['avatar'] ?>"
                                alt="Картинка аватара <?= $post['profile_name']; ?>" />
                            <span class="card__name"><?= $post['profile_name']; ?></span>
                        </div>
                        <div class="card__redactor-button">
                            <img class="card__button-icon" src="../../assets/icons/redactor-icon.svg"
                                alt="Картинка редактирования" />
                        </div>
                    </div>
                    <div class="card__media">
                        <img class="card__image" src="<?= $post['post_picture'] ?>" alt="Фото в ленте" />
                        <div class="card__note">1/3</div>
                    </div>
                    <div class="card__reaction">
                        <div class="card__like">
                            <img class="card__reaction-icon" src="../../assets/icons/like-icon.png" alt="" />
                            <span class="card__reaction-count"><?= $post['reactions']['like']; ?></span>
                        </div>
                    </div>
                    <div class="card__description">
                        <p class="card__text">
                            <?= $post['comment']; ?>
                        </p>
                        <button class="card__button-more">ещё</button>
                    </div>
                    <div class="card__post-time">2 часа назад</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>