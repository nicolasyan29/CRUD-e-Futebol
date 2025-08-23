<?php

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = $_POST['nome'];
    $posicao = $_POST['posicao'];
    $numero_camisa = $_POST['numero_camisa'];
    $time_id = $_POST['time_id'];

    $sql = "INSERT INTO jogadores (nome,posicao,numero_camisa,time_id) VALUES ('$nome','$posicao','$numero_camisa','$time_id')";

    if ($conn->query($sql) === true) {
        header("Location: read_jogador.php");
        exit();
    } else {
        echo "Erro " . $sql . '<br>' . $conn->error;
    }
    $conn->close();


    
}

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Jogador</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <header>
        <h1>➕ Cadastrar Novo Jogador</h1>
        <nav>
            <a href="read_jogador.php">👥 Ver Jogadores</a>
            <a href="index.php">🏠 Página Inicial</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <form method="POST" action="create_jogador.php">

                <label for="nome">Nome:</label>
                <input type="text" name="nome" required>

                <label for="posicao">Posição:</label>
                <input type="text" name="posicao" required>

                <label for="numero_camisa">Número da Camisa:</label>
                <input type="number" name="numero_camisa" required>

                <label for="time_id">Time:</label>
                <select name="time_id" required>
                    <option value="">Selecione um time</option>
                    <?php
                    $sql = "SELECT * FROM times";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                    }
                    $conn->close();
                    ?>
                </select>

                <input type="submit" value="Cadastrar Jogador">
            </form>
        </div>
    </main>
</body>

</html>