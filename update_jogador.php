<?php
include 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID não fornecido!");
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    die("ID inválido!");
}

$sql = "SELECT * FROM jogadores WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Jogador não encontrado!");
}

$jogador = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $conn->real_escape_string($_POST['nome']);
    $posicao = $conn->real_escape_string($_POST['posicao']);
    $numero_camisa = (int)$_POST['numero_camisa'];
    $time_id = (int)$_POST['time_id'];

    $sql = "UPDATE jogadores SET nome='$nome', posicao='$posicao', numero_camisa=$numero_camisa, time_id=$time_id WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Jogador atualizado com sucesso! 
            <a href='read_jogador.php'>Ver registros.</a>";
        exit();
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}

$times_result = $conn->query("SELECT * FROM times ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Jogador</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>✏️ Editar Jogador</h1>
        <nav>
            <a href="read_jogador.php">👥 Ver Jogadores</a>
            <a href="create_jogador.php">➕ Adicionar Novo Jogador</a>
        </nav>
    </header>

    <main>
        <section class="form-panel">
            <h2>Editar Jogador</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($jogador['nome']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="posicao">Posição:</label>
                    <input type="text" id="posicao" name="posicao" value="<?php echo htmlspecialchars($jogador['posicao']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="numero_camisa">Número da Camisa:</label>
                    <input type="number" id="numero_camisa" name="numero_camisa" value="<?php echo $jogador['numero_camisa']; ?>" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="time_id">Time:</label>
                    <select id="time_id" name="time_id" required>
                        <option value="">Selecione um time</option>
                        <?php while ($time = $times_result->fetch_assoc()): ?>
                            <option value="<?php echo $time['id']; ?>" <?php echo $time['id'] == $jogador['time_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($time['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit">Atualizar Jogador</button>
            </form>
        </section>
    </main>
</body>
</html>
<?php $conn->close(); ?>
