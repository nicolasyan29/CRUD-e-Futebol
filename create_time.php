<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se campos obrigatórios foram enviados
    if (isset($_POST["nome"], $_POST["cidade"])) {
        $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
        $cidade = mysqli_real_escape_string($conn, $_POST["cidade"]);

        // Validar data de fundação
        
            // Inserir novo time
            $sql = "INSERT INTO times (nome,cidade) VALUES ('$nome', '$cidade')";

            if ($conn->query($sql) === TRUE) {
                echo "<div class='success'>Time cadastrado com sucesso! <a href='read_time.php'>Ver times</a></div>";
            } else {
                echo "<div class='error'>Erro: " . $conn->error . "</div>";
            }
        }
    } else {
        echo "<div class='error'>Erro: Todos os campos obrigatórios devem ser preenchidos.</div>";
    }
    $conn->close();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>➕ Cadastrar Novo time</h1>
        <nav>
            <a href="read_time.php">👥 Ver times</a>
         <a href="create_time.php">📋 Painel Administrativo</a>
        </nav>
    </header>

    <main>
        <form method="POST" action="read_time.php">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br><br>

             <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" required><br><br>


            <input type="submit" value="Cadastrar time:">
        </form>
    </main>
</body>
</html>
    