<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👥 Gerenciar Usuários</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>👥 Gerenciar Usuários</h1>
        <nav>
            <a href="painel_admin.php">📋 Voltar ao Painel</a>
            <a href="create_usuario.php">➕ Adicionar Novo Usuário</a>
        </nav>
    </header>

    <main>
        <section class="usuarios-panel">
            <h2>Lista de Times</h2>
            
            <?php
            $sql = "SELECT * FROM usuarios ORDER BY nome";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table border='1' cellpadding='8' cellspacing='0'>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        
                    </tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id_time']}</td>
                            <td>{$row['nome']}</td>
                            
                            <td>
                                <a href='update_time.php?id={$row['id_time']}'>Editar</a> | 
                                <a href='delete_time.php?id={$row['id_time']}' onclick='return confirm(\"Tem certeza que deseja excluir?\")'>Excluir</a>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Nenhum usuário cadastrado.</p>";
            }
            ?>
        </section>
    </main>
</body>
</html>