<?php
function evaluateRPN($expression) {
    $nums = explode(' ', trim($expression));
    $stack = [];
    
    foreach ($nums as $num) {
        if ($num === '') continue;
        
        if (is_numeric($num)) {
            array_push($stack, (int)$num);
        } else {
            if (count($stack) < 2) {
                return ['error' => 'Недостаточно операндов для операции ' . $num];
            }
            $b = array_pop($stack);
            $a = array_pop($stack);
            
            switch ($num) {
                case '+': $result = $a + $b; break;
                case '-': $result = $a - $b; break;
                case '*': $result = $a * $b; break;
                default: return ['error' => 'Неизвестная операция: ' . $num];
            }
            array_push($stack, $result);
        }
    }
    
    if (count($stack) !== 1) {
        return ['error' => 'Неверное выражение'];
    }
    return ['result' => $stack[0]];
}

$output = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expression'])) {
    $input = trim($_POST['expression']);
    
    if (empty($input)) {
        $error = 'Введите выражение';
    } else {
        $evaluation = evaluateRPN($input);
        isset($evaluation['error']) ? 
            $error = $evaluation['error'] : 
            $output = $evaluation['result'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Калькулятор ОПЗ</title>
   <link rel="stylesheet" href="../css/RPN.css">
</head>
<body>
    <div class="rpn">
        <p class="exercise">
            Задание №7
        </p>
        <form class="rpn__input" method="post">
            <input class="rpn__input-box" type="text" name="expression" 
                   placeholder="Введите выражение (пример: 8 9 + 1 7 - *)"
                   value="<?= $_POST['expression'] ?? '' ?>">
            <button type="submit">Вычислить</button>
        </form>

        <?php if ($error): ?>
            <div class="error">Ошибка: <?= $error ?></div>
        <?php elseif ($output !== ''): ?>
            <div class="result">Результат: <?= $output ?></div>
        <?php endif; ?>
    </div>
</body>
</html>