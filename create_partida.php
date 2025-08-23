<?php
include 'db.php';

$mensagem = '';
$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Accessing POST variables directly
    $time_casa_id = $_POST["time_casa_id"];
    $time_fora_id = $_POST["time_fora_id"];
    $data_jogo = mysqli_real_escape_string($conn, $_POST["data_jogo"]);
    $gols_casa = $_POST["gols_casa"] ?? 0;
    $gols_fora = $_POST["gols_fora"] ?? 0;

    // RF11: Validar se times são diferentes
    if ($time_casa_id == $time_fora_id) {
        $erro = "Erro: O time mandante não pode ser igual ao time visitante.";
    } elseif ($gols_casa < 0 || $gols_fora < 0) {
        $erro = "Erro: Os gols devem ser números inteiros maiores ou iguais a zero.";
    } else {
        $sql = "INSERT INTO partidas (time_casa_id, time_fora_id, data_jogo, gols_casa, gols_fora) 
                VALUES ($time_casa_id, $time_fora_id, '$data_jogo', $gols_casa, $gols_fora)";

        if ($conn->query($sql) === TRUE) {
            $mensagem = "Partida cadastrada com sucesso! <a href='read_partidas.php'>Ver partidas</a>";
        } else {
            $erro = "Erro: " . $conn->error;
        }
    }
}

// Buscar times para o dropdown
$times = [];
$sql_times = "SELECT id, nome FROM times ORDER BY nome";
$result_times = $conn->query($sql_times);
if ($result_times->num_rows > 0) {
    while($row = $result_times->fetch_assoc()) {
        $times[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Partida</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>➕ Cadastrar Nova Partida</h1>
        <nav>
            <a href="index.php">🏠 Início</a>
            <a href="read_partidas.php">📋 Ver Partidas</a>
            <a href="read_time.php">👥 Ver Times</a>
        </nav>
    </header>

    <main>
        <?php if ($mensagem): ?>
            <div class="success"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="create_partida.php">
            <label for="time_casa_id">Time Mandante:</label>
            <select id="time_casa_id" name="time_casa_id" required>
                <option value="">Selecione o time mandante</option>
                <?php foreach ($times as $time): ?>
                    <option value="<?php echo $time['id']; ?>"><?php echo htmlspecialchars($time['nome']); ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="time_fora_id">Time Visitante:</label>
            <select id="time_fora_id" name="time_fora_id" required>
                <option value="">Selecione o time visitante</option>
                <?php foreach ($times as $time): ?>
                    <option value="<?php echo $time['id']; ?>"><?php echo htmlspecialchars($time['nome']); ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="data_jogo">Data do Jogo:</label>
            <input type="date" id="data_jogo" name="data_jogo" required><br><br>

            <label for="gols_casa">Gols do Mandante:</label>
            <input type="number" id="gols_casa" name="gols_casa" min="0" value="0"><br><br>

            <label for="gols_fora">Gols do Visitante:</label>
            <input type="number" id="gols_fora" name="gols_fora" min="0" value="0"><br><br>

            <input type="submit" value="Cadastrar Partida">
        </form>
    </main>
</body>
</html>
