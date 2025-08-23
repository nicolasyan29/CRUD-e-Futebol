<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$id = $_POST['id'];
$nome = $_POST['nome'];
$cidade = $_POST['cidade'];

$sql = "UPDATE times SET nome='$nome', cidade='$cidade' WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    header("Location: read_time.php");
    exit();
} else {
    echo "Erro ao atualizar o time: " . $conn->error;
}
$conn->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM times WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nome = $row['nome'];
        $cidade = $row['cidade'];
    } else {
        echo "Time não encontrado.";
        exit();
    }
}
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Atualizar Time</h1>
    <form action="" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?php echo $nome; ?>" required>
        <br>
        <label for="cidade">Cidade:</label>
        <input type="text" name="cidade" value="<?php echo $cidade; ?>" required>
        <br>
        <input type="submit" value="Atualizar">
    </form>

</body>
</html>