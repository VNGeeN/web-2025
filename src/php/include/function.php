<?php

const IMAGE_EXT = '.png';
const TITLE_MAX_LENGTH = 255;
const IMAGE_MAX_LENGTH = 50;
const IMAGE_MAX_RANDOM = 5;
const IMAGE_TYPE = 'image/png';
const IMAGE_SIZE = 1024 * 1024;

function validateTitle(string $title):bool{
    return preg_match('/^[A-Za-zА-Яа-я\s]+$/u', $title) && strlen($title) <= TITLE_MAX_LENGTH;
}


function validateImage(string $type, int $size):bool{
    return $type === IMAGE_TYPE && $size <= IMAGE_SIZE;
}


function generateImageName(string $title):string{

    $filename = substr($title, 0, IMAGE_MAX_LENGTH);
    $randomPart = substr(sha1($title . time()), 0, IMAGE_MAX_RANDOM);
    return $filename . '-' . $randomPart . IMAGE_EXT;
}

function generateImgHash(string $tmp_name) {
    $fileHash = md5_file($tmp_name);
    return $fileHash;
}

function migrateImageFiles(string $uploadDir, string $imagesDir) {

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    if (!is_dir($imagesDir)) {
        mkdir($imagesDir, 0755, true);
    }

    $files = glob($uploadDir . '*.{jpg,png,gif}', GLOB_BRACE);
    foreach ($files as $file) {
        $newPath = rtrim($imagesDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($file);
        if (!rename($file, $newPath)) {
            error_log("Failed to move: $file");
        }
    }
}

function findProjectRootDir(string $startDir, string $marker = 'composer.json'): string
{
    $dir = realpath($startDir);

    while ($dir !== false && !file_exists($dir . DIRECTORY_SEPARATOR . $marker)) {
        $parent = dirname($dir);
        if ($parent === $dir) {
            // Дошли до корня файловой системы, маркер не найден
            break;
        }
        $dir = $parent;
    }

    if ($dir === false) {
        throw new Exception("Не удалось найти корень проекта с маркером $marker");
    }

    return $dir . DIRECTORY_SEPARATOR;
}

function loadExistingPosts(string $filePath):array {
    if (!file_exists($filePath))
    {
        return [];
    }
    $json = file_get_contents($filePath);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function savePostsToJson(array $posts, string $filePath): bool {
    $jsonData = json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($jsonData === false) {
        return false;
    }
    return file_put_contents($filePath, $jsonData) !== false;
}

function updatePostsJsonMultipleImages(): bool {
    $connection = connectToDatabase();
    $posts = getPostsWithImages($connection);

    $filePath = __DIR__ . '/../../json/posts.json';
    $existing = loadExistingPosts($filePath);
    $index = [];
    foreach($existing as $p)
    {
        $index[$p['id']] = $p;
    }

    foreach($posts as $np)
    {
        $index[$np['id']] = $np;
    }
    $merged = array_values($index);
    return savePostsToJson($merged, $filePath);
}

?>