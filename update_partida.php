<?php
include 'db.php';

$mensagem = '';
$erro = '';

// Buscar dados da partida para edição
$partida_id = $_GET['id'] ?? '';
$partida = null;

if ($partida_id) {
    $sql_partida = "SELECT p.*, tc.nome as time_casa_nome, tf.nome as time_fora_nome 
                    FROM partidas p
                    LEFT JOIN times tc ON p.time_casa_id = tc.id
                    LEFT JOIN times tf ON p.time_fora_id = tf.id
                    WHERE p.id = $partida_id";
    $result_partida = $conn->query($sql_partida);
    
    if ($result_partida->num_rows > 0) {
        $partida = $result_partida->fetch_assoc();
    } else {
        $erro = "Partida não encontrada.";
    }
} else {
    $erro = "ID da partida não especificado.";
}

// Processar atualização
if ($_SERVER["REQUEST_METHOD"] == "POST" && $partida) {
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
        $sql = "UPDATE partidas 
                SET time_casa_id = $time_casa_id, 
                    time_fora_id = $time_fora_id, 
                    data_jogo = '$data_jogo', 
                    gols_casa = $gols_casa, 
                    gols_fora = $gols_fora 
                WHERE id = $partida_id";

        if ($conn->query($sql) === TRUE) {
            $mensagem = "Partida atualizada com sucesso! <a href='read_partidas.php'>Ver partidas</a>";
            // Atualizar dados locais
            $partida['time_casa_id'] = $time_casa_id;
            $partida['time_fora_id'] = $time_fora_id;
            $partida['data_jogo'] = $data_jogo;
            $partida['gols_casa'] = $gols_casa;
            $partida['gols_fora'] = $gols_fora;
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
    <title>Editar Partida</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>✏️ Editar Partida</h1>
        <nav>
            <a href="index.php">🏠 Início</a>
            <a href="read_partidas.php">📋 Ver Partidas</a>
            <a href="create_partida.php">➕ Nova Partida</a>
        </nav>
    </header>

    <main>
        <?php if ($erro && !$partida): ?>
            <div class="error"><?php echo $erro; ?></div>
            <p><a href="read_partidas.php">← Voltar para a lista de partidas</a></p>
        <?php else: ?>
            <?php if ($mensagem): ?>
                <div class="success"><?php echo $mensagem; ?></div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div class="error"><?php echo $erro; ?></div>
            <?php endif; ?>

            <?php if ($partida): ?>
                <form method="POST" action="update_partida.php?id=<?php echo $partida_id; ?>">
                    <label for="time_casa_id">Time Mandante:</label>
                    <select id="time_casa_id" name="time_casa_id" required>
                        <option value="">Selecione o time mandante</option>
                        <?php foreach ($times as $time): ?>
                            <option value="<?php echo $time['id']; ?>" <?php echo $partida['time_casa_id'] == $time['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($time['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br><br>

                    <label for="time_fora_id">Time Visitante:</label>
                    <select id="time_fora_id" name="time_fora_id" required>
                        <option value="">Selecione o time visitante</option>
                        <?php foreach ($times as $time): ?>
                            <option value="<?php echo $time['id']; ?>" <?php echo $partida['time_fora_id'] == $time['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($time['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br><br>

                    <label for="data_jogo">Data do Jogo:</label>
                    <input type="date" id="data_jogo" name="data_jogo" value="<?php echo $partida['data_jogo']; ?>" required><br><br>

                    <label for="gols_casa">Gols do Mandante:</label>
                    <input type="number" id="gols_casa" name="gols_casa" min="0" value="<?php echo $partida['gols_casa']; ?>"><br><br>

                    <label for="gols_fora">Gols do Visitante:</label>
                    <input type="number" id="gols_fora" name="gols_fora" min="0" value="<?php echo $partida['gols_fora']; ?>"><br><br>

                    <input type="submit" value="Atualizar Partida">
                    <a href="read_partidas.php" class="button">Cancelar</a>
                </form>

                <div class="info-partida">
                    <h3>Informações da Partida:</h3>
                    <p><strong>ID:</strong> <?php echo $partida['id']; ?></p>
                    <p><strong>Mandante:</strong> <?php echo htmlspecialchars($partida['time_casa_nome']); ?></p>
                    <p><strong>Visitante:</strong> <?php echo htmlspecialchars($partida['time_fora_nome']); ?></p>
                    <p><strong>Data Original:</strong> <?php echo date('d/m/Y', strtotime($partida['data_jogo'])); ?></p>
                    <p><strong>Placar Original:</strong> <?php echo $partida['gols_casa'] . ' x ' . $partida['gols_fora']; ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>
