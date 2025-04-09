<?php
function isLuckyTicket($number)
{
    $str = str_pad($number, 6, '0', STR_PAD_LEFT);
    $sum1 = intval($str[0]) + intval($str[1]) + intval($str[2]);
    $sum2 = intval($str[3]) + intval($str[4]) + intval($str[5]);
    return $sum1 === $sum2;
}

function validateTicket($num)
{
    return strlen($num) <= 6 && ctype_digit($num);
}

$result = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = preg_replace('/\D/', '', $_POST['start'] ?? '');
    $end = preg_replace('/\D/', '', $_POST['end'] ?? '');

    if (!validateTicket($start) || !validateTicket($end)) {
        $error = 'Некорректный формат номера';
    } else {
        $startNum = intval(str_pad($start, 6, '0', STR_PAD_LEFT));
        $endNum = intval(str_pad($end, 6, '0', STR_PAD_LEFT));

        if ($startNum > $endNum) {
            $error = 'Начальный номер должен быть меньше конечного';
        } else {
            for ($num = $startNum; $num <= $endNum; $num++) {
                if (isLuckyTicket($num)) {
                    $result[] = str_pad($num, 6, '0', STR_PAD_LEFT);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Поиск счастливых билетов</title>
    <link rel="stylesheet" href="../css/lucky-ticket.css">
</head>

<body>
    <div class="lucky-ticket">
        <p class="exercise">
            Задание №5
        </p>
        <form class="lucky-ticket__input" method="post">
            Начальный номер: <br>
            <input class="lucky-ticket__input-box" type="text" name="start" placeholder="Пример: 111111" required><br>
            Конечный номер: <br>
            <input class="lucky-ticket__input-box" type="text" name="end" placeholder="Пример: 123321" required><br>
            <button type="submit">Найти</button>
            </formс>

            <?php if ($error !== ''): ?>
                <div class="error">Ошибка: <?= $error ?></div>
            <?php elseif (!empty($result)): ?>
                <div class="result">
                    <h3>Найдено счастливых билетов: <?= count($result) ?></h3>
                    <?= implode('<br>', array_map(null, $result)) ?>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div>Счастливых билетов не найдено</div>
            <?php endif; ?>
    </div>
</body>

</html>