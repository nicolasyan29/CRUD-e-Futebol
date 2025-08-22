<?php
include("../config/db.php");

$filtro_time = $_GET['time'] ?? "";
$filtro_ini = $_GET['inicio'] ?? "";
$filtro_fim = $_GET['fim'] ?? "";
$filtro_res = $_GET['resultado'] ?? "";

$pagina = $_GET['pagina'] ?? 1;
$limite = 5;
$offset = ($pagina - 1) * $limite;

$sql = "SELECT p.*, t1.nome AS mandante, t2.nome AS visitante
        FROM partidas p
        JOIN times t1 ON p.mandante_id = t1.id
        JOIN times t2 ON p.visitante_id = t2.id
        WHERE 1=1";

$params = [];
$types = "";

if ($filtro_time) {
    $sql .= " AND (t1.id = ? OR t2.id = ?)";
    $params[] = $filtro_time;
    $params[] = $filtro_time;
    $types .= "ii";
}
if ($filtro_ini && $filtro_fim) {
    $sql .= " AND p.data BETWEEN ? AND ?";
    $params[] = $filtro_ini;
    $params[] = $filtro_fim;
    $types .= "ss";
}
if ($filtro_res == "mandante") {
    $sql .= " AND placar_mandante > placar_visitante";
}
if ($filtro_res == "visitante") {
    $sql .= " AND placar_visitante > placar_mandante";
}
if ($filtro_res == "empate") {
    $sql .= " AND placar_mandante = placar_visitante";
}

$sql .= " LIMIT ? OFFSET ?";
$params[] = $limite;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$times = $conn->query("SELECT * FROM times");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Partidas</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container">
  <h1>⚽ Partidas</h1>
  <a href="create.php" class="btn-add">+ Cadastrar Partida</a>

  <form method="get">
    <select name="time">
      <option value="">-- Time --</option>
      <?php while($t = $times->fetch_assoc()): ?>
        <option value="<?= $t['id'] ?>" <?= $filtro_time==$t['id']?"selected":"" ?>><?= $t['nome'] ?></option>
      <?php endwhile; ?>
    </select>
    <input type="date" name="inicio" value="<?= $filtro_ini ?>">
    <input type="date" name="fim" value="<?= $filtro_fim ?>">
    <select name="resultado">
      <option value="">-- Resultado --</option>
      <option value="mandante" <?= $filtro_res=="mandante"?"selected":"" ?>>Vitória Mandante</option>
      <option value="visitante" <?= $filtro_res=="visitante"?"selected":"" ?>>Vitória Visitante</option>
      <option value="empate" <?= $filtro_res=="empate"?"selected":"" ?>>Empate</option>
    </select>
    <button type="submit">Filtrar</button>
  </form>

  <table class="table">
    <tr>
      <th>Data/Hora</th>
      <th>Mandante</th>
      <th>Placar</th>
      <th>Visitante</th>
      <th>Ações</th>
    </tr>
    <?php while($p = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $p['data'] ?></td>
        <td><?= $p['mandante'] ?></td>
        <td><?= $p['placar_mandante'] ?> x <?= $p['placar_visitante'] ?></td>
        <td><?= $p['visitante'] ?></td>
        <td>
          <a href="update.php?id=<?= $p['id'] ?>" class="btn btn-edit">Editar</a>
          <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-delete">Excluir</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
