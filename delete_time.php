<?php

include 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID não fornecido!");
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    die("ID inválido!");
}

$sql = "SELECT * FROM times WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Time não encontrado!");
}


$sql_check = "SELECT COUNT(*) as total FROM jogadores WHERE time_id = $id";
$result_check = $conn->query($sql_check);
$row_check = $result_check->fetch_assoc();

if ($row_check['total'] > 0) {
    die("Não é possível excluir este time porque ele possui jogadores cadastrados. 
        <br><a href='read_time.php'>Voltar</a>");
}


$sql = "DELETE FROM times WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Time excluído com sucesso! 
        <a href='read_time.php'>Ver registros.</a>";
} else {
    echo "Erro ao excluir: " . $conn->error;
}
$conn->close();
exit();
