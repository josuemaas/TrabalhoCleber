<?php
session_start();
require_once 'conexao.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Conectar ao banco de dados
    $conn = connectDb();

    if (!$conn) {
        die("Erro ao conectar ao banco de dados.");
    }

    // Alterar a query para especificar o schema, se necessário
    $query = "SELECT id, password, role FROM public.usuario WHERE username = $1";  // Usando 'public' como exemplo

    // Executar a consulta
    $result = pg_query_params($conn, $query, [$username]);

    if (!$result) {
        die("Erro ao buscar usuário: " . pg_last_error($conn));
    }

    // Verifica se o usuário existe
    if (pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);

        // Verifica a senha
        if (password_verify($password, $user['password'])) {
            // Salva dados do usuário na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role']; // Salva o papel do usuário na sessão

            header("Location: index.php");
            exit;
        } else {
            $error = "Usuário ou senha inválidos.";
        }
    } else {
        $error = "Usuário ou senha inválidos.";
    }

    pg_close($conn);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Imagem Centralizada -->
        <div class="image-container">
            <img src="logohosp.jpg" alt="Imagem do Login">
        </div>
        
        <h1>Login</h1>
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <div>
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>