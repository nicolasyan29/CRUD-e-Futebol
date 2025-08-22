<?php
include("../config/db.php");

$id = $_GET['id'];

if ($conn->query("DELETE FROM jogadores WHERE id=$id")) {
    header("Location: read.php?sucesso=1");
} else {
    header("Location: read.php?erro=Não foi possível excluir o jogador.");
}
?>
