<?php
session_start();
require_once 'conexao.php';

// Função para salvar avaliações no banco
function saveEvaluation($responses, $sectorId, $deviceId) {
    $conn = connectDb();

    // Verificar se o dispositivo existe
    $queryDevice = "SELECT COUNT(*) FROM dispositivo WHERE id_dispositivo = $1";
    $resultDevice = pg_query_params($conn, $queryDevice, [$deviceId]);

    if (!$resultDevice || pg_fetch_result($resultDevice, 0, 0) == 0) {
        echo "Erro: Dispositivo inválido ou não encontrado (ID: $deviceId).";
        pg_close($conn);
        return false;
    }

    foreach ($responses as $questionId => $response) {
        $score = $response['score'];
        $feedback = $response['feedback'] ?? null;

        // Inserir avaliação
        $query = "INSERT INTO avaliacao (id_setor, id_dispositivo, id_pergunta, resposta, feedback)
                  VALUES ($1, $2, $3, $4, $5)";
        $params = [$sectorId, $deviceId, $questionId, $score, $feedback];
        $result = pg_query_params($conn, $query, $params);

        if (!$result) {
            echo "Erro ao salvar avaliação para a pergunta ID: $questionId<br>";
            echo pg_last_error($conn);
        }
    }

    pg_close($conn);
    return true;
}

// Processar submissão
if (isset($_SESSION['responses']) && !empty($_SESSION['responses'])) {
    $sectorId = 5; // ID do setor (fixo ou recebido do formulário)
    $deviceId = 1; // ID do dispositivo (fixo ou recebido do formulário)

    if (saveEvaluation($_SESSION['responses'], $sectorId, $deviceId)) {
        session_destroy();
        echo "<h1>Avaliação enviada com sucesso!</h1>";
        echo "<p>O Hospital Regional Alto Vale  agradece sua resposta. Ela é muito importante para nós.</p>";
    } else {
        echo "<h1>Erro ao salvar a avaliação. Tente novamente mais tarde.</h1>";
    }
} else {
    echo "<h1>Nenhuma resposta foi encontrada.</h1>";
}
?>
