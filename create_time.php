<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se campos obrigatórios foram enviados
    if (isset($_POST["nome"], $_POST["email"], $_POST["senha"], $_POST["data_contratacao"])) {
        $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); // Hash seguro da senha
        $telefone = mysqli_real_escape_string($conn, $_POST["telefone"] ?? '');
        $endereco = mysqli_real_escape_string($conn, $_POST["endereco"] ?? '');
        $data_contratacao = mysqli_real_escape_string($conn, $_POST["data_contratacao"]);

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
                $sql = "INSERT INTO usuarios (nome, email, senha_hash, telefone, endereco, data_contratacao) 
                        VALUES ('$nome', '$email', '$senha', '$telefone', '$endereco', '$data_contratacao')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "<div class='success'>Usuário cadastrado com sucesso! <a href='read_usuarios.php'>Ver usuários</a></div>";
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
            <a href="painel_admin.php">📋 Painel Administrativo</a>
        </nav>
    </header>

    <main>
        <form method="POST" action="create_usuarios.php">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required><br><br>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone"><br><br>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco"><br><br>

            <label for="data_contratacao">Data de Contratação:</label>
            <input type="date" id="data_contratacao" name="data_contratacao" required><br><br>

            <input type="submit" value="Cadastrar Usuário">
        </form>
    </main>
</body>
</html>
    