<?php
function connectDb() {
    $conn = pg_connect("host=localhost port=5432 dbname=hrav_avaliacao user=postgres password=postgres");
    if (!$conn) {
        die("Erro ao conectar ao banco de dados: " . pg_last_error());
    }
    return $conn;
}
?>
