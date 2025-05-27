<?php
require_once('validation.php');

$id = null;

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
}

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

if ($id === false || $id === null) {
    $posts = postsFormalize($feedData, $userData);
} else if ($id !== null) {
    $feedFormolizeFromId = [];
    $i = 1;
    foreach ($feedData as $feed) {
        if ($feed['user_id'] === $id) {
            $feedFormolizeFromId[$i] = $feed;
        }
        $i++;
    }
    $posts = postsFormalize($feedFormolizeFromId, $userData);
}

function postsFormalize(array $feedData, array $userData): array
{
    $postsF = [];

    foreach ($feedData as $feed) {
        foreach ($userData as $user) {
            if ($feed['user_id'] === $user['id']) {
                $postsF[$feed['id']] = [
                    'profile_name' => $user['profile_properties']['profile_name'],
                    'avatar' => $user['profile_properties']['avatar'],
                    'comment' => $feed['comment'],
                    'post_pictures' => $feed['post_pictures'],
                    'reactions' => $feed['reactions']
                ];
            }
        }
    }
    return $postsF;
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
                        <?php
                        $total = count($post['post_pictures']);
                        $profileName = $post['profile_name'];
                        foreach ($post['post_pictures'] as $index => $picture):
                            $zIndex = $total - $index;
                            ?>
                            <img class="card__image" src="<?= $picture ?>" alt="Фото в профиле <?= $profileName; ?>"
                            style="z-index: <?= $zIndex ?>;"/>
                        <?php endforeach; ?>

                        <div class="card__note" style="z-index: <?= $total + 1 ?>">1/3</div>
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