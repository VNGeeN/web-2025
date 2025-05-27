<?php
require_once('validation.php');

$page = "home";

function validateUserId(string $input)
{

  if (trim($input) === '') {
    header("Location: /php/partials/feed.php");
    exit();
  }

  if (!ctype_digit($input)) {
    header("Location: /php/partials/feed.php");
    exit();
  }

  $userId = (int) $input;

  if ($userId < 1) {
    header("Location: /php/partials/feed.php");
    exit();
  }

  return $userId;
}

$input = $_GET['id'] ?? '';

$userId = validateUserId($input);

$users = json_decode(file_get_contents('../../json/users.json'), true);
$allPosts = json_decode(file_get_contents('../../json/posts.json'), true);

$errors = validateUserData($users);

if (!empty($errors)) {
  echo '<h2>Ошибки валидации пользователей:</h2>';
  echo '<pre>' . print_r($errors, true) . '</pre>';
  exit;
}

$userData = null;

foreach ($users as $user) {
  if ($user['id'] === $userId) {
    $userData = $user;
    break;
  }
}

$userPosts = array_filter($allPosts, function ($post) use ($userId) {
  return $post['user_id'] === $userId;
});

$totalPictures = 0;
foreach ($userPosts as $post) {
    $totalPictures += count($post['post_pictures']);
}

if (!$userData) {
  header("Location: /php/partials/feed.php");
  exit();
}

?>


<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Профиль</title>
  <link rel="stylesheet" href="../../css/fonts.css" />
  <link rel="stylesheet" href="../../css/profile.css">
</head>

<body>
  <div class="main-container">
    <?php require_once('sidebar.php'); ?>
    <div class="container">
      <div class="profile">
        <div class="profile__head">
          <div class="profile__author-info">
            <img class="profile__avatar" src="<?= $userData['profile_properties']['avatar']; ?>"
              alt="Фото аватара в профиле" />
            <span class="profile__name"><?= $userData['profile_properties']['profile_name']; ?></span>
            <span class="profile__descriprion"><?= $userData['profile_properties']['user_about']; ?></span>
          </div>
          <div class="profile__info">
            <img class="profile__image-icon" src="../../assets/icons/image-icon.svg" alt="Иконка картинок" />
            <span class="profile__count-posts"><?= $totalPictures; ?></span>
            <span class="profile__post-info-text">поста</span>
          </div>
        </div>
        <div class="profile__mosaic">
          <?php foreach ($userPosts as $post): ?>
            <?php foreach ($post['post_pictures'] as $picture): ?>
              <a href="/php/partials/feed.php?id=<?= $userId; ?>" class="profile__gallery-link">
                <img class="profile__mosaic-img" src="<?= $picture ?>"
                  alt="Фото в профиле <?= $userData['profile_properties']['profile_name']; ?>" />
              </a>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</body>

</html>