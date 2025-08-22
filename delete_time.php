<?php

include 'db.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID não fornecido!");
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    die("ID inválido!");
}

// Verificar se o usuário existe antes de excluir
$sql = "SELECT * FROM usuarios WHERE id_usuario = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Usuário não encontrado!");
}

// Verificar se o usuário tem produtos associados
$sql_check = "SELECT COUNT(*) as total FROM times WHERE id_time = $id";
$result_check = $conn->query($sql_check);
$row_check = $result_check->fetch_assoc();

if ($row_check['total'] > 0) {
    die("Não é possível excluir este usuário porque ele possui produtos cadastrados. 
        <br><a href='read_time.php'>Voltar</a>");
}

// Excluir o usuário
$sql = "DELETE FROM usuarios WHERE id_usuario = $id";

if ($conn->query($sql) === TRUE) {
    echo "Usuário excluído com sucesso! 
        <a href='read_time.php'>Ver registros.</a>";
} else {
    echo "Erro ao excluir: " . $conn->error;
}
$conn->close();
exit();