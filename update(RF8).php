<?php
include("../config/db.php");

$id = $_GET['id'];
$jogador = $conn->query("SELECT * FROM jogadores WHERE id=$id")->fetch_assoc();
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
        $sql = "UPDATE jogadores SET nome=?, posicao=?, numero_camisa=?, time_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $nome, $posicao, $camisa, $time_id, $id);
        if ($stmt->execute()) {
            header("Location: read.php?sucesso=1");
            exit;
        } else {
            $msg = "Erro ao atualizar jogador.";
        }
    }
}

$times = $conn->query("SELECT * FROM times");
$posicoes = ["Goleiro","Zagueiro","Lateral","Volante","Meia","Atacante"];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Jogador</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container">
  <h1>⚽ Editar Jogador</h1>
  <?php if ($msg) echo "<div class='msg-error'>$msg</div>"; ?>

  <form method="post">
    <input type="text" name="nome" value="<?= $jogador['nome'] ?>" required>
    <select name="posicao" required>
      <?php foreach($posicoes as $p): ?>
        <option value="<?= $p ?>" <?= $jogador['posicao']==$p?"selected":"" ?>><?= $p ?></option>
      <?php endforeach; ?>
    </select>
    <input type="number" name="camisa" min="1" max="99" value="<?= $jogador['numero_camisa'] ?>" required>
    <select name="time" required>
      <?php while($t = $times->fetch_assoc()): ?>
        <option value="<?= $t['id'] ?>" <?= $t['id']==$jogador['time_id']?"selected":"" ?>><?= $t['nome'] ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Salvar</button>
  </form>

  <a href="read.php" class="btn-add">← Voltar</a>
</div>
</body>
</html>
