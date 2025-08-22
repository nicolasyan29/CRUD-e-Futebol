<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se campos obrigatórios foram enviados
    if (isset($_POST["nome"], $_POST["email"], $_POST["senha"], $_POST["data_contratacao"])) {
        $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
        
       
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='error'>Erro: Email inválido.</div>";
        } else {
            // Verificar se email já existe
            $check_email = "SELECT email FROM usuarios WHERE email = '$email'";
            $result = $conn->query($check_email);
            
            if ($result->num_rows > 0) {
                echo "<div class='error'>Erro: Este email já está cadastrado.</div>";
            } else {
                // Inserir novo usuário
                $sql = "INSERT INTO times (nome) VALUES ('$nome')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "<div class='success'>Usuário cadastrado com sucesso! <a href='read_time.php'>Ver times</a></div>";
                } else {
                    echo "<div class='error'>Erro: " . $conn->error . "</div>";
                }
            }
        }
    } else {
        echo "<div class='error'>Erro: Todos os campos obrigatórios devem ser preenchidos.</div>";
    }
    $conn->close();
}
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
        <h1>➕ Cadastrar Novo Usuário</h1>
        <nav>
            <a href="read_usuarios.php">👥 Ver usuários</a>
         <a href="create_time.php">📋 Painel Administrativo</a>
        </nav>
    </header>

    <main>
        <form method="POST" action="create_usuarios.php">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br><br>

            <input type="submit" value="Cadastrar time:">
        </form>
    </main>
</body>
</html>
    