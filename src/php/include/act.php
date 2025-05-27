<?php


const ACT_UPLOADER = 'uploader';
const STATUS_ERROR = 'error';
const STATUS_OK = 'ok';

// Вызов из файла act.php
try {
  define('PROJECT_DIR', findProjectRootDir(__DIR__, 'assets'));
} catch (Exception $e) {
  // fallback — просто подняться на 3 уровня, если нет composer.json
  define('PROJECT_DIR', realpath(__DIR__ . '/../../..') . '/');
}
// define('PROJECT_DIR', realpath(__DIR__ . '../../') . '/');
define('UPLOAD_DIR', PROJECT_DIR . 'assets/uploads/posts/');
define('IMAGES_DIR', PROJECT_DIR . 'assets/images/posts');



const MESSAGE_INVALID_REQUEST_METHOD = 'invalid method';
const MESSAGE_INVALID_ID_ACT = 'invalid act';
const MESSAGE_INVALID_TITLE = 'invalid title';
const MESSAGE_INVALID_IMAGE = 'invalid image';
const MESSAGE_INVALID_SAVE_IMAGE = 'invalid save image';
const MESSAGE_INVALID_SAVE_DB_IMAGE = 'invalid save db image';
const MESSAGE_INVALID_IMAGE_HASH_GENERATE = 'invalid image hash';
const MESSAGE_FILES_COLLISION = 'File already exists';
const MESSAGE_USER_NOT_FOUND = 'user not found';
const MESSAGE_INVALID_SAVE_DB_POST = 'invalid save db post';

function uploadData(): string
{
  $title = isset($_POST['title']) ? $_POST['title'] : null;
  if (!$title) {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_TITLE);
  }

  if (!validateTitle($title)) {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_TITLE);
  }

  $image = $_FILES && $_FILES['image']['error'] === UPLOAD_ERR_OK ? $_FILES['image'] : null;
  if (!$image) {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_IMAGE);
  }

  if (!validateImage($image['type'], $image['size'])) {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_IMAGE);
  }

  $filename = generateImageName($title);

  $fileHash = generateImgHash($image['tmp_name']);
  if (!$fileHash) {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_IMAGE_HASH_GENERATE);
  }

  $isSuccess = move_uploaded_file($image['tmp_name'], UPLOAD_DIR . $filename);

  if (!$isSuccess) {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_SAVE_IMAGE . "Trying to move file to: " . UPLOAD_DIR . $filename);
  }

  $connection = connectToDatabase();

  $isDuplicate = checkImageHashDubleInDatabase($connection, $fileHash);

  if ($isDuplicate) {
    return getResponse(STATUS_ERROR, MESSAGE_FILES_COLLISION);
  }

  $isSuccess = saveImageToDatabase($connection, 'posts', $filename, $fileHash);

  if (!$isSuccess) {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_SAVE_DB_IMAGE);
  }

  $imageId = $connection->lastInsertId();

  $userId = 2; //заглушка

  if (!checkUserId($connection, $userId)) {
    return getResponse(STATUS_ERROR, MESSAGE_FILES_COLLISION);
  }

  $isSuccess = savePostToDatabase($connection, $title, $userId);

  if (!$isSuccess) {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_SAVE_DB_POST);
  }

  $postId = $connection->lastInsertId();

  $isSuccess = savePostImagesDb($connection, $postId, $imageId);

  if(!$isSuccess)
  {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_SAVE_DB_POST . ' Images DB');
  }

  migrateImageFiles(UPLOAD_DIR, IMAGES_DIR);

  $isSuccess = updatePostsJsonMultipleImages();

  if (!$isSuccess)
  {
    return getResponse(STATUS_ERROR, MESSAGE_INVALID_SAVE_DB_POST . 'updatePostsJsonMultipleImages');
  }

  return getResponse(STATUS_OK, '');
}

function getResponse(string $status, string $message): string
{
  $response = [
    'status' => $status,
    'message' => $message
  ];
  return (string) json_encode($response);
}

?>