<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mensagens de feedback
$success = $error = "";

// Obter setores disponíveis
$conn = connectDb();
$querySetores = "SELECT id_setor, nome FROM setor ORDER BY nome ASC";
$resultSetores = pg_query($conn, $querySetores);
$setores = pg_fetch_all($resultSetores);

// Adicionar pergunta com setor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $questionText = trim($_POST['question_text']);
    $idSetor = $_POST['id_setor'];

    if (!empty($questionText) && !empty($idSetor)) {
        $query = "INSERT INTO pergunta (texto, status, id_setor) VALUES ($1, 'ativa', $2)";
        $result = pg_query_params($conn, $query, [$questionText, $idSetor]);

        if ($result) {
            $success = "Pergunta adicionada com sucesso!";
        } else {
            $error = "Erro ao adicionar pergunta: " . pg_last_error($conn);
        }
    } else {
        $error = "O texto da pergunta e o setor devem ser informados.";
    }
}

// Excluir pergunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {
    $questionId = $_POST['question_id'];

    if (!empty($questionId)) {
        $query = "DELETE FROM pergunta WHERE id_pergunta = $1";
        $result = pg_query_params($conn, $query, [$questionId]);

        if ($result) {
            $success = "Pergunta excluída com sucesso!";
        } else {
            $error = "Erro ao excluir pergunta: " . pg_last_error($conn);
        }
    } else {
        $error = "Selecione uma pergunta para excluir.";
    }
}

// Listar perguntas
$queryQuestions = "SELECT id_pergunta, texto FROM pergunta";
$resultQuestions = pg_query($conn, $queryQuestions);
$questions = pg_fetch_all($resultQuestions);

pg_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Painel Administrativo</h1>
        <p> Administrador -  <?= htmlspecialchars($_SESSION['username']) ?>!</p>

        <!-- Mensagens de feedback -->
        <?php if (!empty($success)): ?>
            <p style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- Formulário para adicionar perguntas com setor -->
        <h2>Adicionar Pergunta</h2>
        <form method="POST" action="admin.php">
            <textarea name="question_text" placeholder="Digite a nova pergunta" rows="4" required></textarea>
            <select name="id_setor" required>
                <option value="" disabled selected>Selecione um setor</option>
                <?php if ($setores): ?>
                    <?php foreach ($setores as $setor): ?>
                        <option value="<?= $setor['id_setor'] ?>"><?= htmlspecialchars($setor['nome']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <button type="submit" name="add_question">Adicionar</button>
        </form>

        <!-- Lista de perguntas para exclusão -->
        <h2>Excluir Pergunta</h2>
        <form method="POST" action="admin.php">
            <select name="question_id" required>
                <option value="">Selecione uma pergunta</option>
                <?php if ($questions): ?>
                    <?php foreach ($questions as $question): ?>
                        <option value="<?= $question['id_pergunta'] ?>">
                            <?= htmlspecialchars($question['texto']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <button type="submit" name="delete_question">Excluir</button>
        </form>

        <a href="logout.php">Sair</a>
        <p><a href="index.php">Voltar à página anterior</a></p>
    </div>
</body>
</html>
