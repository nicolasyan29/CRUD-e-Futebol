<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Futebol</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>⚽ Sistema de Gerenciamento de Futebol</h1>
        <nav>
            <a href="read_time.php">👥 Ver Times</a>
            <a href="create_time.php">➕ Adicionar Time</a>
        </nav>
    </header>

    <main>
        <section class="container">
            <h2>🏠 Página Inicial</h2>
            <p>Bem-vindo ao sistema de gerenciamento de times de futebol!</p>
            
            <div style="margin: 20px 0; padding: 15px; background: #fffbe6; border-radius: 10px; border: 1px solid #f5deb3;">
                <h3>📊 Resumo dos Times</h3>
                <?php
                $sql = "SELECT COUNT(*) as total FROM times";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                echo "<p>Total de times cadastrados: <strong>{$row['total']}</strong></p>";
                
                $sql_recent = "SELECT nome, cidade FROM times ORDER BY id DESC LIMIT 3";
                $result_recent = $conn->query($sql_recent);
                
                if ($result_recent->num_rows > 0) {
                    echo "<p>Times mais recentes:</p>";
                    echo "<ul>";
                    while ($row_recent = $result_recent->fetch_assoc()) {
                        echo "<li>{$row_recent['nome']} - {$row_recent['cidade']}</li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>

            <div style="margin: 20px 0; text-align: center;">
                <h3>🚀 Ações Rápidas</h3>
                <div style="display: flex; justify-content: center; gap: 15px; margin-top: 15px;">
                    <a href="create_time.php" style="background: #d2691e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                        ➕ Adicionar Time
                    </a>
                    <a href="read_time.php" style="background: #b8860b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                        👥 Ver Todos os Times
                    </a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
<?php
$conn->close();
?>
