<?php
include("../config/db.php");

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $posicao = $_POST['posicao'];
    $camisa = $_POST['camisa'];
    $time_id = $_POST['time'];

    if ($camisa < 1 || $camisa > 99) {
        $msg = "❌ Número da camisa deve estar entre 1 e 99.";
    } elseif (!$time_id) {
        $msg = "❌ O jogador deve estar vinculado a um time.";
    } else {
        $sql = "INSERT INTO jogadores (nome, posicao, numero_camisa, time_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $nome, $posicao, $camisa, $time_id);
        if ($stmt->execute()) {
            header("Location: read.php?sucesso=1");
            exit;
        } else {
            $msg = "Erro ao cadastrar jogador.";
        }
    }
}

$times = $conn->query("SELECT * FROM times ORDER BY nome");
$posicoes = ["Goleiro","Zagueiro","Lateral","Volante","Meia","Atacante"];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Jogador</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container">
  <h1>⚽ Cadastrar Jogador</h1>
  <?php if ($msg) echo "<div class='msg-error'>$msg</div>"; ?>

  <form method="post">
    <input type="text" name="nome" placeholder="Nome" required>
    <select name="posicao" required>
      <option value="">-- Posição --</option>
      <?php foreach($posicoes as $p): ?>
        <option value="<?= $p ?>"><?= $p ?></option>
      <?php endforeach; ?>
    </select>
    <input type="number" name="camisa" min="1" max="99" placeholder="Nº Camisa" required>
    <select name="time" required>
      <option value="">-- Time --</option>
      <?php while($t = $times->fetch_assoc()): ?>
        <option value="<?= $t['id'] ?>"><?= $t['nome'] ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Cadastrar</button>
  </form>

  <a href="read.php" class="btn-add">← Voltar</a>
</div>
</body>
</html>
