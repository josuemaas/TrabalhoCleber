<?php
function getQuestions() {
    $conn = pg_connect("host=localhost port=5432 dbname=hrav_avaliacao user=postgres password=postgres");

    if (!$conn) {
        die("Erro ao conectar ao banco de dados: " . pg_last_error());
    }

    $query = "SELECT id_pergunta AS id, texto FROM pergunta WHERE status = 'ativa'";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Erro ao buscar perguntas: " . pg_last_error($conn));
    }

    $questions = pg_fetch_all($result);
    pg_close($conn);

    return $questions;
}
?>