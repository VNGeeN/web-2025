<?php
function numberToWord($num)
{
    $numbers = [
        0 => 'ноль',
        1 => 'один',
        2 => 'два',
        3 => 'три',
        4 => 'четыре',
        5 => 'пять',
        6 => 'шесть',
        7 => 'семь',
        8 => 'восемь',
        9 => 'девять'
    ];
    return $numbers[$num] ?? 'Неверный ввод';
}

$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['number'])) {
    $input = (int) $_POST['number'];
    $result = ($input >= 0 && $input <= 9)
        ? numberToWord($input)
        : 'Введите цифру от 0 до 9';
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Цифра в слово</title>
    <link rel="stylesheet" href="../css/int-to-string.css">
</head>

<body>
    <div class="int-to-string">
        <p class="exercise">
            Задание №2
        </p>
        <form class="int-to-string__input" method="post">
            Цифра (0-9):
            <input class="int-to-string__input-box" type="number" name="number" min="0" max="9" required
                title="Только одна цифра">
            <input type="submit" value="Преобразовать">
        </form>

        <?php if ($result !== ''): ?>
            <p>Результат: <?php echo $result; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>