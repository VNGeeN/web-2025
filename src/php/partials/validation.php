<?php
declare(strict_types=1);

const MAX_POSTS = 100;
const ALLOWED_IMAGE_EXT = ['png', 'jpg', 'jpeg', 'gif', 'svg'];
const MAX_COMMENT_LENGTH = 1000;
const MIN_TIMESTAMP = 0;


/**
 * Валидация данных пользователя
 * @param array $value
 * @return array
 */
function validateUserData(array $users): array
{
    $errors = [];
    $ids = [];

    foreach ($users as $index => $user) {
        $userErrors = [];

        // Проверка ID
        if (!validateType($user['id'], 'integer') || $user['id'] < 1) {
            $userErrors['id'] = 'ID должен быть положительным целым числом';
        } elseif (in_array($user['id'], $ids)) {
            $userErrors['id'] = 'Дублирующийся ID';
        }
        $ids[] = $user['id'];

        // Поля имени и фамилии
        $userErrors += validateField($user, 'name', 2, 50);
        $userErrors += validateField($user, 'surname', 2, 50);

        // Профиль
        if (!isset($user['profile_properties'])) {
            $userErrors['profile_properties'] = 'Отсутствует раздел профиля';
        } else {
            $profileErrors = [];
            $profile = $user['profile_properties'];

            $profileErrors += validateImagePath($profile, 'avatar');
            $profileErrors += validateField($profile, 'profile_name', 2, 100);

            if (isset($profile['user_about'])) {
                $profileErrors += validateField($profile, 'user_about', 0, 500);
            }

            if ($profileErrors) {
                $userErrors['profile_properties'] = $profileErrors;
            }
        }

        // Посты
        if (!isset($user['user_posts']['posts'])) {
            $userErrors['user_posts'] = 'Отсутствуют посты';
        } else {
            $postErrors = validateUserPosts($user['user_posts']['posts']);
            if ($postErrors) {
                $userErrors['user_posts'] = $postErrors;
            }
        }

        if ($userErrors) {
            $errors["user_{$index}"] = $userErrors;
        }
    }

    return $errors;
}

/**
 * Валидация данных постов в feed
 * @param array $value
 * @return array
 */
function validatePostsData(array $posts): array
{
    $errors = [];
    $postsIds = [];
    $userIds = [];

    foreach ($posts as $index => $post) {
        $postErrors = [];

        // Проверка ID поста
        if (!validateType($post['id'], 'integer') || $post['id'] < 1) {
            $postErrors['id'] = "ID должен быть положительным целым числом {$post}";
        } elseif (in_array($post['id'], $postsIds)) {
            $postErrors['id'] = 'Дублирующийся ID';
        }
        $postsIds[] = $post['id'];

        //Обязательные поля
        //Проверка id пользователя
        if (!validateType($post['user_id'] ?? null, 'integer') || $post['user_id'] < 1) {
            $postErrors['user_id'] = 'Некорректный ID пользователя';
        } else {
            $userIds[] = $post['user_id'];
        }

        //Проверка пути до картинки
        $postErrors += validateImagePath($post, 'post_picture');

        // Проверка комментария
        if (isset($post['comment'])) {
            if (!validateType($post['comment'], 'string')) {
                $postErrors['comment'] = 'Комментарий должен быть строкой';
            } elseif (mb_strlen($post['comment']) > MAX_COMMENT_LENGTH) {
                $postErrors['comment'] = "Комментарий слишком длинный (макс. " . MAX_COMMENT_LENGTH . " символов)";
            }
        }

        // Проверка временной метки
        if (!validateTimestamp($post['timestamp'] ?? null)) {
            $postErrors['timestamp'] = 'Некорректная временная метка';
        }

        if ($postErrors) {
            $errors["post_{$index}"] = $postErrors;
        }
    }
    return $errors;
}

/**
 * Валидация основных полей
 * @param array $data
 * @param string $field
 * @param int $min
 * @param int $max
 * @return array
 */
function validateField(array $data, string $field, int $min, int $max): array
{
    $errors = [];

    if (!isset($data[$field])) {
        $errors[$field] = 'Обязательное поле';
        return $errors;
    }

    if (!validateType($data[$field], 'string')) {
        $errors[$field] = 'Некорректный тип данных';
    } elseif (!validateLength($data[$field], $min, $max)) {
        $errors[$field] = "Допустимая длина: $min-$max символов";
    }

    return $errors;
}

/**
 * Валидация картинок
 * @param array $data
 * @param string $field
 * @return array
 */
function validateImagePath(array $data, string $field): array
{
    $errors = [];

    if (!isset($data[$field])) {
        $errors[$field] = 'Обязательное поле';
        return $errors;
    }

    if (!validateType($data[$field], 'string')) {
        $errors[$field] = 'Некорректный тип данных';
        return $errors;
    }

    //Обработка пути
    $relativePath = ltrim($data[$field], '\.\./');
    $fullPath = realpath($_SERVER['DOCUMENT_ROOT'] . '/' . $relativePath);
    
    if (!$fullPath) {
        $errors[$field] = 'Файл не существует';
    }

    $ext = pathinfo($fullPath, PATHINFO_EXTENSION);

    if (!in_array(strtolower($ext), ALLOWED_IMAGE_EXT)) {
        $errors[$field] = 'Недопустимый формат изображения';
    }

    return $errors;
}

/**
 * Валидация постов
 * @param array $posts
 * @return array
 */
function validateUserPosts(array $posts): array
{
    $errors = [];

    if (count($posts) > MAX_POSTS) {
        $errors[] = "Превышено максимальное количество постов: " . MAX_POSTS;
    }

    foreach ($posts as $i => $post) {
        if (!validateType($post, 'string')) {
            $errors["post_$i"] = 'Некорректный тип данных';
            continue;
        }

        $ext = pathinfo($post, PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), ALLOWED_IMAGE_EXT)) {
            $errors["post_$i"] = 'Недопустимый формат изображения';
        }
    }

    return $errors;
}

/**
 * Валидация длины строки
 * @param mixed $value
 * @param int $min
 * @param int $max
 * @return bool
 */
function validateLength($value, int $min, int $max): bool
{
    if (!is_string($value))
        return false;
    $length = mb_strlen($value);
    return $length >= $min && $length <= $max;
}

/**
 * Валидация timestamp
 * @param mixed $timestamp
 * @param int|null $min (если null - автоопределение)
 * @return bool
 */
function validateTimestamp($timestamp, ?int $min = null): bool
{
    if (!is_int($timestamp))
        return false;

    $min = $min ?? MIN_TIMESTAMP;

    return $timestamp >= $min;
}

/**
 * Валидация типа значения
 * @param mixed $value
 * @param string $type
 * @return bool
 */
function validateType($value, string $type): bool
{
    switch ($type) {
        case 'string':
            return is_string($value);
        case 'integer':
            return is_int($value);
        case 'array':
            return is_array($value);
        case 'boolean':
            return is_bool($value);
        default:
            return false;
    }
}