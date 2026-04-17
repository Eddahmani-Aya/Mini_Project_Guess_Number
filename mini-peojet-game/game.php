<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['secret_number'])) {
    $_SESSION['secret_number'] = rand(1, 100);
    $_SESSION['attempts'] = 0;
    $_SESSION['guesses'] = [];
}

$secret_number = $_SESSION['secret_number'];
$message = '';
$messageType = 'info';
$gameOver = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guess'])) {
    $guess = intval($_POST['guess']);
    
    if ($guess < 1 || $guess > 100) {
        $message = "❌ Veuillez entrer un nombre entre 1 et 100.";
        $messageType = 'warning';
    } else {
        $_SESSION['attempts']++;
        $_SESSION['guesses'][] = $guess;
        
        if ($guess === $secret_number) {
            $message = "🎉 Bravo ! Nombre $secret_number trouvé en " . $_SESSION['attempts'] . " tentatives !";
            $messageType = 'success';
            $gameOver = true;
        } elseif ($guess < $secret_number) {
            $message = "📈 C'est plus grand ! Tentative #" . $_SESSION['attempts'];
        } else {
            $message = "📉 C'est plus petit ! Tentative #" . $_SESSION['attempts'];
        }
        
        if ($_SESSION['attempts'] >= 10 && $guess !== $secret_number) {
            $message .= " 😢 Game Over ! Le nombre était $secret_number.";
            $messageType = 'error';
            $gameOver = true;
        }
    }
}

// Réponse JSON
$response = [
    'message' => $message,
    'messageType' => $messageType,
    'attempts' => $_SESSION['attempts'],
    'guesses' => $_SESSION['guesses'],
    'gameOver' => $gameOver
];

echo json_encode($response);
