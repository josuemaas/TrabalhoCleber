<?php
session_start();
require_once 'questions.php';

//Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
  exit;
}

// Verifica se o usuário é admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    
// Obter as perguntas
$questions = getQuestions();

// Verifica se há perguntas disponíveis
if (empty($questions)) {
    echo "
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Erro - Nenhuma Pergunta Encontrada</title>
        <style>
            /* Estilos personalizados */
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Ainda não há perguntas cadastradas</h1>
            <a href='index.php' class='button'>Voltar à Página Principal</a>
        </div>
    </body>
    </html>
    ";
    exit();
}

// Inicializa o índice da pergunta e respostas
if (!isset($_SESSION['current_index'])) {
    $_SESSION['current_index'] = 0;
    $_SESSION['responses'] = [];
}

$currentIndex = $_SESSION['current_index'];
$currentQuestion = $questions[$currentIndex];

// Processa o formulário de avaliação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionId = $_POST['question_id'];
    $score = $_POST['score'];
    $feedback = $_POST['feedback'] ?? '';

    $_SESSION['responses'][$questionId] = [
        'score' => $score,
        'feedback' => $feedback
    ];

    // Avança para a próxima pergunta ou finaliza
    if ($currentIndex + 1 < count($questions)) {
        $_SESSION['current_index']++;
        header("Location: index.php");
        exit;
    } else {
        header("Location: enviar.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Avaliação</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>
            Bem-vindo(a), 
            <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'username' ?>!
        </h1>

        
        <?php if ($isAdmin): ?>
            <p><a href="admin.php" class="admin-link">Acessar Painel de Administração</a></p>
        <?php endif; ?>

        
        <h2>Avaliação de Satisfação</h2>
        <form method="POST">
            <h2><?= htmlspecialchars($currentQuestion['texto']) ?></h2>
            <input type="hidden" name="question_id" value="<?= htmlspecialchars($currentQuestion['id']) ?>">
            <div class="scale">
                <?php for ($i = 0; $i <= 10; $i++): ?>
                    <label class="scale-item">
                        <input type="radio" name="score" value="<?= $i ?>" required>
                        <span class="square"><?= $i ?></span>
                    </label>
                <?php endfor; ?>
            </div>
            <textarea name="feedback" placeholder="Deixe seu feedback (opcional)" rows="4"></textarea>
            <button type="submit">Próxima</button>
        </form>

      
        <p><a href="logout.php">Sair</a></p>
    </div>
</body>
</html>



