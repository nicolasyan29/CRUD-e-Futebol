<?php
include("../config/db.php");
$id = $_GET['id'];

if ($conn->query("DELETE FROM partidas WHERE id=$id")) {
    header("Location: read.php?sucesso=1");
} else {
    echo "Erro ao excluir partida.";
}
?>
