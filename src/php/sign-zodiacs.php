<?php
function getZodiacSign($day, $month)
{
    $signs = [
        ['name' => 'Водолей', 'start' => [1, 20], 'end' => [2, 18]],
        ['name' => 'Рыбы', 'start' => [2, 19], 'end' => [3, 20]],
        ['name' => 'Овен', 'start' => [3, 21], 'end' => [4, 19]],
        ['name' => 'Телец', 'start' => [4, 20], 'end' => [5, 20]],
        ['name' => 'Близнецы', 'start' => [5, 21], 'end' => [6, 20]],
        ['name' => 'Рак', 'start' => [6, 21], 'end' => [7, 22]],
        ['name' => 'Лев', 'start' => [7, 23], 'end' => [8, 22]],
        ['name' => 'Дева', 'start' => [8, 23], 'end' => [9, 22]],
        ['name' => 'Весы', 'start' => [9, 23], 'end' => [10, 22]],
        ['name' => 'Скорпион', 'start' => [10, 23], 'end' => [11, 21]],
        ['name' => 'Стрелец', 'start' => [11, 22], 'end' => [12, 21]],
        ['name' => 'Козерог', 'start' => [12, 22], 'end' => [1, 19]]
    ];

    foreach ($signs as $sign) {
        if (
            ($month == $sign['start'][0] && $day >= $sign['start'][1]) ||
            ($month == $sign['end'][0] && $day <= $sign['end'][1])
        ) {
            // Особый случай для Козерога (переход через год)
            if ($sign['name'] == 'Козерог') {
                if ($month == 12 || $month == 1)
                    return $sign['name'];
            } else {
                return $sign['name'];
            }
        }
    }
    return 'Неизвестно';
}

$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
    $dateParts = explode('.', $_POST['date']);

    if (count($dateParts) === 3) {
        $day = (int) $dateParts[0];
        $month = (int) $dateParts[1];
        $year = (int) $dateParts[2];

        if (checkdate($month, $day, $year)) {
            $result = getZodiacSign($day, $month);
        } else {
            $result = 'Некорректная дата';
        }
    } else {
        $result = 'Неверный формат даты';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Знак зодиака</title>
    <link rel="stylesheet" href="../css/sign-zodiac.css">
</head>

<body>
    <div class="sign-zodiac">
        <p class="exercise">
            Задание №3
        </p>
        <form class="sign-zodiac__input" method="post">
            Введите дату (ДД.ММ.ГГГГ):
            <input class="sign-zodiac__input-box" type="text" name="date" placeholder="например: 15.04.1452"
                pattern="\d{2}\.\d{2}\.\d{4}" title="Формат: ДД.ММ.ГГГГ" required>
            <input type="submit" value="Узнать">
        </form>

        <?php if ($result !== ''): ?>
            <div class="<?php echo $result === 'Неизвестно' ? 'error' : ''; ?>">
                Результат: <?php echo $result; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>