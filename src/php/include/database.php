<?php

const DB_HOST = 'mysql';
const DB_NAME = 'blog';
const DB_USER = 'root';
const DB_PASSWORD = '6a12';

function connectToDatabase():PDO {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    return new PDO($dsn, DB_USER, DB_PASSWORD, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
}


function getPostsFromDatabase(PDO $connection, int $limit = 100): array {
  $query = <<<SQL
    SELECT
      p.id,
      p.title,
      p.user_id,
      (
        SELECT i.filename
        FROM post_images pi
        JOIN images AS i ON pi.image_id = i.id
        WHERE pi.post_id = p.id
        ORDER BY i.id ASC
        LIMIT 1
      ) AS filename,
      p.created_at
    FROM
      posts AS p
    ORDER BY p.created_at DESC
    LIMIT {$limit}
  SQL;

  $statemant = $connection->prepare($query);
  $statemant->bindValue(':limit', $limit, PDO::PARAM_INT);
  $statemant->execute();
  return $statemant->fetchAll(PDO::FETCH_ASSOC);
}

function savePostToDatabase(PDO $connection, string $title, int $userId){
    $query = <<<SQL
      INSERT INTO
        posts (title, user_id)
      VALUES
        (:title, :user_id);
    SQL;

    $statemant = $connection->prepare($query);
    return $statemant->execute([
        ':title' => $title,
        ':user_id' => $userId,
    ]);
}

function getImagesFromDatabase(PDO $connection, int $limit = 100):array {
  $query = <<<SQL
    SELECT
      id, user_id
    FROM
      posts
    LIMIT {$limit}
  SQL;
  
  $statemant = $connection->query($query);
  return $statemant->fetchAll(PDO::FETCH_ASSOC);
}

function checkImageHashDubleInDatabase(PDO $connection, string $fileHash): bool {
  $query = "SELECT id FROM images WHERE file_hash = ?";
  $statement = $connection->prepare($query);
  $statement->execute([$fileHash]);
  $row = $statement->fetch(PDO::FETCH_ASSOC);
  return $row !== false;  // true - дубликат найден, false - нет
}

function saveImageToDatabase(PDO $connection, string $entityType, string $filename, string $fileHash) {
    $query = <<<SQL
      INSERT INTO
        images (entity_type, filename, file_hash)
      VALUES
        (:type, :filename, :hash)
    SQL;

    $statemant = $connection->prepare($query);
    return $statemant->execute([
        ':type' => $entityType,
        ':filename' => $filename,
        ':hash' => $fileHash
    ]);
}

function checkUserId(PDO $connection, int $userId): bool {
  $query = "SELECT id FROM users WHERE id = ?";
  $statement = $connection->prepare($query);
  $statement->execute([$userId]);
  $row = $statement->fetch(PDO::FETCH_ASSOC);
  return $row !== false;
}

function savePostImagesDb(PDO $connection, int $post_id, int $image_id):bool {
  $query = <<<SQL
      INSERT INTO
        post_images (post_id, image_id)
      VALUES
        (:post_id, :image_id)
    SQL;

    $statemant = $connection->prepare($query);
    return $statemant->execute([
        ':post_id' => $post_id,
        ':image_id' => $image_id
    ]);
}

function getPostsWithImages(PDO $connection, int $limit = 100): array {
  $query = <<<SQL
    SELECT
      posts.id AS post_id,
      posts.title,
      posts.user_id,
      posts.created_at,
      images.filename
    FROM posts
    LEFT JOIN post_images ON posts.id = post_images.post_id
    LEFT JOIN images ON post_images.image_id = images.id
    LIMIT {$limit}
  SQL;

  $statement = $connection->query($query);
  $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

  $posts = [];
  foreach ($rows as $row) {
      $postId = (int)$row['post_id'];
      $userId = (int)$row['user_id'];
      if (!isset($posts[$postId])) {
          $posts[$postId] = [
              'id' => $postId,
              'title' => $row['title'],
              'user_id' => $userId,
              'post_pictures' => [],
              'comment' => '', //заглушка
              'reactions' => ['like' => 0],
              'timestamp' => strtotime($row['created_at']),
          ];
      }
      if ($row['filename']) {
          $posts[$postId]['post_pictures'][] = "../../assets/images/posts/" . $row['filename'];  //передать констану для дирректории, чтобы было не так хардкорно
      }
  }
  return array_values($posts); // чтобы сбросить ключи, вернуть индексный массив
}

?>