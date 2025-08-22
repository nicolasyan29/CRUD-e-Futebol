<?php
include("../config/db.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Times de Futebol</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <div class="container">
    <h1>⚽ Gestão de Times</h1>

    <a href="create.php" class="btn-add">+ Adicionar Time</a>

    <form method="get">
      <input type="text" name="filtro" placeholder="Buscar time..." value="<?= $_GET['filtro'] ?? '' ?>">
      <button type="submit">Pesquisar</button>
    </form>

    <table class="table">
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Cidade</th>
        <th>Ações</th>
      </tr>
      <?php
      $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : "";
      $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
      $limite = 5;
      $offset = ($pagina - 1) * $limite;

      $sql = "SELECT * FROM times WHERE nome LIKE ? LIMIT ? OFFSET ?";
      $stmt = $conn->prepare($sql);
      $busca = "%$filtro%";
      $stmt->bind_param("sii", $busca, $limite, $offset);
      $stmt->execute();
      $result = $stmt->get_result();

      while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['nome']}</td>
                  <td>{$row['cidade']}</td>
                  <td>
                    <a href='update.php?id={$row['id']}' class='btn btn-edit'>Editar</a>
                    <a href='delete.php?id={$row['id']}' class='btn btn-delete'>Excluir</a>
                  </td>
                </tr>";
      }

      $res = $conn->query("SELECT COUNT(*) AS total FROM times WHERE nome LIKE '$busca'");
      $total = $res->fetch_assoc()['total'];
      $paginas = ceil($total / $limite);
      ?>
    </table>

    <div class="pagination">
      <?php for ($i = 1; $i <= $paginas; $i++): ?>
        <a href="?pagina=<?= $i ?>&filtro=<?= $filtro ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
</body>
</html>
