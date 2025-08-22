<?php
include("../config/db.php");

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mandante = $_POST['mandante'];
    $visitante = $_POST['visitante'];
    $datahora = $_POST['datahora'];
    $placar_mandante = $_POST['placar_mandante'];
    $placar_visitante = $_POST['placar_visitante'];

    if ($mandante == $visitante) {
        $msg = "❌ Erro: O mandante e o visitante devem ser diferentes!";
    } else {
        $sql = "INSERT INTO partidas (mandante_id, visitante_id, data, placar_mandante, placar_visitante) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i ssii", $mandante, $visitante, $datahora, $placar_mandante, $placar_visitante);
        if ($stmt->execute()) {
            header("Location: read.php?sucesso=1");
            exit;
        } else {
            $msg = "Erro ao cadastrar partida.";
        }
    }
}

$times = $conn->query("SELECT * FROM times ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Partida</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container">
    <h1>⚽ Cadastrar Partida</h1>
    <?php if ($msg) echo "<p style='color:red'>$msg</p>"; ?>

    <form method="post">
        <select name="mandante" required>
            <option value="">-- Mandante --</option>
            <?php while($t = $times->fetch_assoc()) echo "<option value='{$t['id']}'>{$t['nome']}</option>"; ?>
        </select>

        <select name="visitante" required>
            <option value="">-- Visitante --</option>
            <?php
            $times->data_seek(0);
            while($t = $times->fetch_assoc()) echo "<option value='{$t['id']}'>{$t['nome']}</option>";
            ?>
        </select>

        <input type="datetime-local" name="datahora" required>
        <input type="number" name="placar_mandante" min="0" value="0" required>
        <input type="number" name="placar_visitante" min="0" value="0" required>

        <button type="submit">Cadastrar</button>
    </form>
    <a href="read.php" class="btn-add">← Voltar</a>
</div>
</body>
</html>
