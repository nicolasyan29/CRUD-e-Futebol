<?php
include 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID não fornecido!");
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    die("ID inválido!");
}

$sql = "SELECT * FROM jogadores WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Jogador não encontrado!");
}

$sql = "DELETE FROM jogadores WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Jogador excluído com sucesso! 
        <a href='read_jogador.php'>Ver registros.</a>";
} else {
    echo "Erro ao excluir: " . $conn->error;
}
$conn->close();
exit();
?>
