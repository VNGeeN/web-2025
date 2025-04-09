<?php
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['year'])) {
    $year = (int) $_POST['year'];
    $isLeap = ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
    $result = $isLeap ? 'YES' : 'NO';
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Високосный год</title>
    <link rel="stylesheet" href="../css/leap-year.css">
</head>

<body>
    <div class="leap-year">
        <p class="exercise">
            Задание №1
        </p>
        <form class="leap-year__input" method="post">
            Год: <input class="leap-year__input-box" type="number" name="year" min="1" max="30000" required>
            <input class="leap-year__button" type="submit" value="Проверить">
        </form>
        <?php if ($result !== ''): ?>
            <p>Результат: <?php echo $result; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>