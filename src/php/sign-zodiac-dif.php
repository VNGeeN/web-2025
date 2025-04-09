<?php
// Функция извлечения всех чисел из строки
function extractNumbers($dateStr) {
    $numbers = [];
    $currentNumber = '';
    
    for ($i = 0; $i < strlen($dateStr); $i++) {
        $char = $dateStr[$i];
        if (ctype_digit($char)) {
            $currentNumber .= $char;
        } else {
            if ($currentNumber !== '') {
                $numbers[] = (int)$currentNumber;
                $currentNumber = '';
            }
        }
    }
    
    if ($currentNumber !== '') {
        $numbers[] = (int)$currentNumber;
    }
    
    return $numbers;
}

// Функция определения формата даты
function parseDate($numbers) {
    $day = $month = $year = null;
    
    foreach ($numbers as $num) {
        if ($num >= 1 && $num <= 31) {
            if ($day === null) {
                $day = $num;
            } elseif ($month === null) {
                $month = $num;
            }
        } elseif ($num >= 1000) {
            $year = $num;
        } elseif ($num >= 1 && $num <= 12) {
            if ($month === null) {
                $month = $num;
            } elseif ($day === null) {
                $day = $num;
            }
        }
    }
    
    // Корректировка порядка для неоднозначных случаев
    if ($day > 12 && $month === null) {
        $month = $day;
        $day = $numbers[1] ?? 1;
    }
    
    return [$day ?? 1, $month ?? 1, $year ?? 2000];
}

// Проверка високосного года (без date())
function isLeapYear($year) {
    return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
}

// Проверка существования даты
function isValidDate($day, $month, $year) {
    if ($month < 1 || $month > 12) return false;
    
    $daysInMonth = [
        31, 28 + (isLeapYear($year) ? 1 : 0), 31, 30, 
        31, 30, 31, 31, 30, 31, 30, 31
    ];
    
    return $day >= 1 && $day <= $daysInMonth[$month - 1];
}

// Функция определения знака зодиака
function getZodiacSign($day, $month) {
    $signs = [
        ['name' => 'Козерог', 'start' => [1, 1], 'end' => [1, 19]],
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
        ['name' => 'Козерог', 'start' => [12, 22], 'end' => [12, 31]]
    ];

    foreach ($signs as $sign) {
        if (($month == $sign['start'][0] && $day >= $sign['start'][1]) || 
            ($month == $sign['end'][0] && $day <= $sign['end'][1])) {
            return $sign['name'];
        }
    }
    return 'Неизвестно';
}

// Обработка формы
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['date'])) {
    $rawDate = trim($_POST['date']);
    $numbers = extractNumbers($rawDate);
    
    if (count($numbers) < 2) {
        $result = 'Недостаточно данных';
    } else {
        list($day, $month, $year) = parseDate($numbers);
        
        if (isValidDate($day, $month, $year)) {
            $result = getZodiacSign($day, $month);
        } else {
            $result = 'Некорректная дата';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Определение знака зодиака</title>
    <style>
        /* .container { margin: 20px; padding: 15px; border: 1px solid #ccc; }
        input[type="text"] { width: 250px; padding: 5px; }
        .error { color: red; } */
    </style>
</head>
<body>
    <div class="container">
        <form method="post">
            Введите дату в любом формате: <br>
            <input type="text" name="date" 
                   placeholder="Примеры: 15.04.2023, 3/7/99, 2022-12-31"
                   required>
            <button type="submit">Определить</button>
        </form>

        <?php if ($result !== ''): ?>
            <div class="<?= strpos($result, 'Не') === 0 ? 'error' : '' ?>">
                <?= $result ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>