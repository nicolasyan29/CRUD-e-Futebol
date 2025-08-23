<?php
include 'db.php';

// Configuração de paginação
$itens_por_pagina = 10;
$pagina_atual = $_GET['pagina'] ?? 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// Filtros
$filtro_time = $_GET['time'] ?? '';
$filtro_data_inicio = $_GET['data_inicio'] ?? '';
$filtro_data_fim = $_GET['data_fim'] ?? '';
$filtro_resultado = $_GET['resultado'] ?? '';

// Construir query base
$sql_where = "WHERE 1=1";

if ($filtro_time) {
    $sql_where .= " AND (p.time_casa_id = $filtro_time OR p.time_fora_id = $filtro_time)";
}

if ($filtro_data_inicio) {
    $sql_where .= " AND p.data_jogo >= '$filtro_data_inicio'";
}

if ($filtro_data_fim) {
    $sql_where .= " AND p.data_jogo <= '$filtro_data_fim'";
}

if ($filtro_resultado) {
    if ($filtro_resultado == 'vitoria_casa') {
        $sql_where .= " AND p.gols_casa > p.gols_fora";
    } elseif ($filtro_resultado == 'vitoria_fora') {
        $sql_where .= " AND p.gols_casa < p.gols_fora";
    } elseif ($filtro_resultado == 'empate') {
        $sql_where .= " AND p.gols_casa = p.gols_fora";
    }
}

// Query para contar total de registros
$sql_count = "SELECT COUNT(*) as total FROM partidas p $sql_where";
$result_count = $conn->query($sql_count);
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $itens_por_pagina);

// Query principal com paginação
$sql = "SELECT p.*, 
               tc.nome as time_casa_nome, 
               tf.nome as time_fora_nome
        FROM partidas p
        LEFT JOIN times tc ON p.time_casa_id = tc.id
        LEFT JOIN times tf ON p.time_fora_id = tf.id
        $sql_where
        ORDER BY p.data_jogo DESC, p.id DESC
        LIMIT $offset, $itens_por_pagina";

$result = $conn->query($sql);

// Buscar times para o filtro
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
    <title>Lista de Partidas</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1>📋 Lista de Partidas</h1>
        <nav>
            <a href="index.php">🏠 Início</a>
            <a href="create_partida.php">➕ Nova Partida</a>
            <a href="read_time.php">👥 Ver Times</a>
        </nav>
    </header>

    <main>
        <!-- Filtros -->
        <div class="filtros">
            <h2>Filtros</h2>
            <form method="GET" action="read_partidas.php">
                <label for="time">Filtrar por Time:</label>
                <select id="time" name="time">
                    <option value="">Todos os times</option>
                    <?php foreach ($times as $time): ?>
                        <option value="<?php echo $time['id']; ?>" <?php echo $filtro_time == $time['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($time['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="data_inicio">Data Início:</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?php echo $filtro_data_inicio; ?>">

                <label for="data_fim">Data Fim:</label>
                <input type="date" id="data_fim" name="data_fim" value="<?php echo $filtro_data_fim; ?>">

                <label for="resultado">Resultado:</label>
                <select id="resultado" name="resultado">
                    <option value="">Todos</option>
                    <option value="vitoria_casa" <?php echo $filtro_resultado == 'vitoria_casa' ? 'selected' : ''; ?>>Vitória Mandante</option>
                    <option value="vitoria_fora" <?php echo $filtro_resultado == 'vitoria_fora' ? 'selected' : ''; ?>>Vitória Visitante</option>
                    <option value="empate" <?php echo $filtro_resultado == 'empate' ? 'selected' : ''; ?>>Empate</option>
                </select>

                <input type="submit" value="Aplicar Filtros">
                <a href="read_partidas.php" class="button">Limpar Filtros</a>
            </form>
        </div>

        <!-- Lista de Partidas -->
        <div class="lista-partidas">
            <h2>Partidas Encontradas: <?php echo $total_registros; ?></h2>
            
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Mandante</th>
                            <th>Placar</th>
                            <th>Visitante</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($partida = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($partida['data_jogo'])); ?></td>
                                <td><?php echo htmlspecialchars($partida['time_casa_nome']); ?></td>
                                <td><?php echo $partida['gols_casa'] . ' x ' . $partida['gols_fora']; ?></td>
                                <td><?php echo htmlspecialchars($partida['time_fora_nome']); ?></td>
                                <td>
                                    <a href="update_partida.php?id=<?php echo $partida['id']; ?>" class="button">Editar</a>
                                    <a href="delete_partida.php?id=<?php echo $partida['id']; ?>" class="button danger" onclick="return confirm('Tem certeza que deseja excluir esta partida?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Paginação -->
                <?php if ($total_paginas > 1): ?>
                    <div class="paginacao">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <a href="read_partidas.php?pagina=<?php echo $i; ?>&time=<?php echo $filtro_time; ?>&data_inicio=<?php echo $filtro_data_inicio; ?>&data_fim=<?php echo $filtro_data_fim; ?>&resultado=<?php echo $filtro_resultado; ?>" 
                               class="<?php echo $i == $pagina_atual ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <p>Nenhuma partida encontrada.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
