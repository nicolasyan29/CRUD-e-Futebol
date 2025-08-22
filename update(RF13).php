<?php
include("../config/db.php");

$id = $_GET['id'];
$partida = $conn->query("SELECT * FROM partidas WHERE id=$id")->fetch_assoc();
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mandante = $_POST['mandante'];
    $visitante = $_POST['visitante'];
    $datahora = $_POST['datahora'];
    $placar_mandante = $_POST['placar_mandante'];
    $placar_visitante = $_POST['placar_visitante'];

    if ($mandante == $visitante) {
        $msg = "❌ Mandante e visitante devem ser diferentes!";
    } else {
        $sql = "UPDATE partidas SET mandante_id=?, visitante_id=?, data=?, placar_mandante=?, placar_visitante=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issiii", $mandante, $visitante, $datahora, $placar_mandante, $placar_visitante, $id);
        if ($stmt->execute()) {
            header("Location: read.php?sucesso=1");
            exit;
        } else {
            $msg = "Erro ao atualizar partida.";
        }
    }
}
$times = $conn->query("SELECT * FROM times");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Partida</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container">
  <h1>⚽ Editar Partida</h1>
  <?php if ($msg) echo "<p style='color:red'>$msg</p>"; ?>
  <form method="post">
    <select name="mandante" required>
      <?php while($t = $times->fetch_assoc()): ?>
        <option value="<?= $t['id'] ?>" <?= $t['id']==$partida['mandante_id']?"selected":"" ?>><?= $t['nome'] ?></option>
      <?php endwhile; ?>
    </select>
    <select name="visitante" required>
      <?php
      $times->data_seek(0);
      while($t = $times->fetch_assoc()): ?>
        <option value="<?= $t['id'] ?>" <?= $t['id']==$partida['visitante_id']?"selected":"" ?>><?= $t['nome'] ?></option>
      <?php endwhile; ?>
    </select>
    <input type="datetime-local" name="datahora" value="<?= date('Y-m-d\TH:i', strtotime($partida['data'])) ?>" required>
    <input type="number" name="placar_mandante" min="0" value="<?= $partida['placar_mandante'] ?>" required>
    <input type="number" name="placar_visitante" min="0" value="<?= $partida['placar_visitante'] ?>" required>
    <button type="submit">Salvar</button>
  </form>
  <a href="read.php" class="btn-add">← Voltar</a>
</div>
</body>
</html>
