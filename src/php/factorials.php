<?php
function factorial($n) {
    if ($n < 0) return 'Ошибка: отрицательное число';
    if ($n <= 1) return 1;
    return $n * factorial($n - 1);
}

$result = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['number'])) {
    $input = $_POST['number'];
    
    if (!ctype_digit($input) || $input === '') {
        $error = 'Введите целое неотрицательное число';
    } else {
        $number = (int)$input;
        $result = "{$number}! = " . factorial($number);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Калькулятор факториала</title>
    <link rel="stylesheet" href="../css/factorials.css">
</head>
<body>
    <div class="factorials">
        <p class="exercise">
            Задание №6
        </p>
        <form class="factorials__input" method="post">
            Введите число: 
            <input class="factorials__input-box"type="text" name="number" 
                   placeholder="Пример: 5"
                   required>
            <button type="submit">Вычислить</button>
        </form>

        <?php if ($error !== ''): ?>
            <div class="error"><?= $error ?></div>
        <?php elseif ($result !== ''): ?>
            <div class="result"><?= $result ?></div>
        <?php endif; ?>

        <div style="margin-top:15px; color: #666;">
            Примечание: для чисел > 20 результат может быть некорректен из-за переполнения
        </div>
    </div>
</body>
</html>