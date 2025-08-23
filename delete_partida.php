<?php
include 'db.php';

$mensagem = '';
$erro = '';

// Buscar dados da partida para confirmar exclusão
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

// Processar exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST" && $partida) {
    $confirmacao = $_POST["confirmacao"] ?? '';
    
    if ($confirmacao === 'sim') {
        $sql = "DELETE FROM partidas WHERE id = $partida_id";
        
        if ($conn->query($sql) === TRUE) {
            $mensagem = "Partida excluída com sucesso! <a href='read_partidas.php'>Ver partidas</a>";
            $partida = null; // Limpar dados após exclusão
        } else {
            $erro = "Erro ao excluir partida: " . $conn->error;
        }
    } else {
        $mensagem = "Exclusão cancelada. <a href='read_partidas.php'>Voltar para a lista de partidas</a>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Partida</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>🗑️ Excluir Partida</h1>
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
        <?php elseif ($mensagem): ?>
            <div class="success"><?php echo $mensagem; ?></div>
            <?php if (strpos($mensagem, 'cancelada') === false): ?>
                <p><a href="read_partidas.php">← Voltar para a lista de partidas</a></p>
            <?php endif; ?>
        <?php elseif ($partida): ?>
            <div class="confirmacao-exclusao">
                <h2>Confirmar Exclusão</h2>
                <div class="info-partida">
                    <h3>Partida a ser excluída:</h3>
                    <p><strong>ID:</strong> <?php echo $partida['id']; ?></p>
                    <p><strong>Mandante:</strong> <?php echo htmlspecialchars($partida['time_casa_nome']); ?></p>
                    <p><strong>Visitante:</strong> <?php echo htmlspecialchars($partida['time_fora_nome']); ?></p>
                    <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($partida['data_jogo'])); ?></p>
                    <p><strong>Placar:</strong> <?php echo $partida['gols_casa'] . ' x ' . $partida['gols_fora']; ?></p>
                </div>

                <p class="aviso">⚠️ <strong>Atenção:</strong> Esta ação não pode ser desfeita. Tem certeza que deseja excluir esta partida?</p>

                <form method="POST" action="delete_partida.php?id=<?php echo $partida_id; ?>">
                    <input type="hidden" name="confirmacao" value="sim">
                    <input type="submit" value="Sim, Excluir Partida" class="danger">
                    <a href="read_partidas.php" class="button">Não, Cancelar</a>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
