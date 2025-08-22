<?php
include("../config/db.php");

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

echo "<h2>Times</h2>";
echo "<form method='get'>
        <input type='text' name='filtro' placeholder='Buscar time' value='$filtro'>
        <button type='submit'>Filtrar</button>
      </form>";

while ($row = $result->fetch_assoc()) {
    echo "<p>{$row['nome']} - {$row['cidade']}
          <a href='update.php?id={$row['id']}'>Editar</a>
          <a href='delete.php?id={$row['id']}'>Excluir</a></p>";
}


$res = $conn->query("SELECT COUNT(*) AS total FROM times WHERE nome LIKE '$busca'");
$total = $res->fetch_assoc()['total'];
$paginas = ceil($total / $limite);

for ($i = 1; $i <= $paginas; $i++) {
    echo "<a href='read.php?pagina=$i&filtro=$filtro'>$i</a> ";
}
?>
