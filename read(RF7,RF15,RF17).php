<?php
include("../config/db.php");

$sucesso = $_GET['sucesso'] ?? "";
$erro = $_GET['erro'] ?? "";

$filtro_nome = $_GET['nome'] ?? "";
$filtro_posicao = $_GET['posicao'] ?? "";
$filtro_time = $_GET['time'] ?? "";

$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$limite = 10;
$offset = ($pagina - 1) * $limite;

$sql = "SELECT SQL_CALC_FOUND_ROWS j.*, t.nome AS time_nome
        FROM jogadores j
        JOIN times t ON j.time_id = t.id
        WHERE 1=1";
$params = [];
$types = "";

if ($filtro_nome) {
    $sql .= " AND j.nome LIKE ?";
    $params[] = "%$filtro_nome%"; $types .= "s";
}
if ($filtro_posicao) {
    $sql .= " AND j.posicao = ?";
    $params[] = $filtro_posicao; $types .= "s";
}
if ($filtro_time) {
    $sql .= " AND j.time_id = ?";
    $params[] = $filtro_time; $types .= "i";
}

$sql .= " LIMIT ? OFFSET ?";
$params[] = $limite; $params[] = $offset; $types .= "ii";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<div class='msg-error'>❌ Erro SQL: verifique a consulta.<br><small>{$conn->error}</small></div>";
    exit;
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$total_res = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc()['total'];
$total_paginas = ceil($total_res / $limite);

$times = $conn->query("SELECT * FROM times");
$posicoes = ["Goleiro","Zagueiro","Lateral","Volante","Meia","Atacante"];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Jogadores</title>
  <link rel="stylesheet" href="../style.css">
  <script>
    function confirmarExclusao(url) {
      if (confirm("⚠ Tem certeza que deseja excluir este jogador?")) {
        window.location.href = url;
      }
    }
  </script>
</head>
<body>
<div class="container">
  <h1>⚽ Jogadores</h1>

  <?php if ($sucesso): ?><div class="msg-success">✅ Ação realizada com sucesso!</div><?php endif; ?>
  <?php if ($erro): ?><div class="msg-error">❌ Ocorreu um erro: <?= htmlspecialchars($erro) ?></div><?php endif; ?>

  <a href="create.php" class="btn-add">+ Cadastrar Jogador</a>

 
  <form method="get">
    <input type="text" name="nome" placeholder="Nome" value="<?= $filtro_nome ?>">
    <select name="posicao">
      <option value="">-- Posição --</option>
      <?php foreach($posicoes as $p): ?>
        <option value="<?= $p ?>" <?= $filtro_posicao==$p?"selected":"" ?>><?= $p ?></option>
      <?php endforeach; ?>
    </select>
    <select name="time">
      <option value="">-- Time --</option>
      <?php while($t = $times->fetch_assoc()): ?>
        <option value="<?= $t['id'] ?>" <?= $filtro_time==$t['id']?"selected":"" ?>><?= $t['nome'] ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Filtrar</button>
  </form>

  <table class="table">
    <tr>
      <th>Nome</th>
      <th>Posição</th>
      <th>Nº Camisa</th>
      <th>Time</th>
      <th>Ações</th>
    </tr>
    <?php while($j = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $j['nome'] ?></td>
        <td><?= $j['posicao'] ?></td>
        <td><?= $j['numero_camisa'] ?></td>
        <td><?= $j['time_nome'] ?></td>
        <td>
          <a href="update.php?id=<?= $j['id'] ?>" class="btn btn-edit">Editar</a>
          <a href="javascript:void(0)" onclick="confirmarExclusao('delete.php?id=<?= $j['id'] ?>')" class="btn btn-delete">Excluir</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <div class="pagination">
    <?php for ($i=1; $i <= $total_paginas; $i++): ?>
      <a href="?pagina=<?= $i ?>&nome=<?= $filtro_nome ?>&posicao=<?= $filtro_posicao ?>&time=<?= $filtro_time ?>"
         style="<?= $i==$pagina?'background:#1a73e8;color:white':'' ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>
  </div>
</div>
</body>
</html>
