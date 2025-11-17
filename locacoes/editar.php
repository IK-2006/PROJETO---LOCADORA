<?php
include '../config.php';

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = $_GET['id'];
$erro = '';

// Buscar dados da locação
$stmt = $pdo->prepare("SELECT * FROM LOCACAO WHERE ID_LOCACAO = ?");
$stmt->execute([$id]);
$locacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$locacao) {
    header("Location: listar.php?erro=Locação não encontrada");
    exit;
}

// Buscar clientes e filmes para os selects
$clientes = $pdo->query("SELECT ID_CLIENTE, NOME FROM CLIENTE ORDER BY NOME")->fetchAll(PDO::FETCH_ASSOC);
$filmes = $pdo->query("SELECT ID_FILME, TITULO FROM FILME ORDER BY TITULO")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_locacao = $_POST['data_locacao'];
    $data_devolucao = $_POST['data_devolucao'];
    $multa = $_POST['multa'] ? floatval($_POST['multa']) : 0.00;
    $id_filme = $_POST['id_filme'];
    $id_cliente = $_POST['id_cliente'];
    
    // Validações
    if (empty($data_locacao) || empty($data_devolucao) || empty($id_filme) || empty($id_cliente)) {
        $erro = "Todos os campos são obrigatórios!";
    } elseif ($data_devolucao < $data_locacao) {
        $erro = "Data de devolução não pode ser anterior à data de locação!";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE LOCACAO SET DATA_LOCACAO = ?, DATA_DEVOLUCAO = ?, MULTA = ?, ID_FILME = ?, ID_CLIENTE = ? WHERE ID_LOCACAO = ?");
            $stmt->execute([$data_locacao, $data_devolucao, $multa, $id_filme, $id_cliente, $id]);
            header("Location: listar.php?sucesso=Locação atualizada com sucesso!");
            exit;
        } catch(PDOException $e) {
            $erro = "Erro ao atualizar locação: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Locação</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Locação</h1>
            <a href="listar.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="data_locacao">Data Locação:</label>
                <input type="date" id="data_locacao" name="data_locacao" 
                       value="<?php echo isset($_POST['data_locacao']) ? htmlspecialchars($_POST['data_locacao']) : $locacao['DATA_LOCACAO']; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="data_devolucao">Data Devolução:</label>
                <input type="date" id="data_devolucao" name="data_devolucao" 
                       value="<?php echo isset($_POST['data_devolucao']) ? htmlspecialchars($_POST['data_devolucao']) : $locacao['DATA_DEVOLUCAO']; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="multa">Multa (R$):</label>
                <input type="number" id="multa" name="multa" step="0.01" min="0" 
                       value="<?php echo isset($_POST['multa']) ? htmlspecialchars($_POST['multa']) : $locacao['MULTA']; ?>">
            </div>
            
            <div class="form-group">
                <label for="id_cliente">Cliente:</label>
                <select id="id_cliente" name="id_cliente" required>
                    <option value="">Selecione um cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['ID_CLIENTE']; ?>" 
                            <?php echo (isset($_POST['id_cliente']) && $_POST['id_cliente'] == $cliente['ID_CLIENTE']) || $locacao['ID_CLIENTE'] == $cliente['ID_CLIENTE'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cliente['NOME']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_filme">Filme:</label>
                <select id="id_filme" name="id_filme" required>
                    <option value="">Selecione um filme</option>
                    <?php foreach ($filmes as $filme): ?>
                        <option value="<?php echo $filme['ID_FILME']; ?>" 
                            <?php echo (isset($_POST['id_filme']) && $_POST['id_filme'] == $filme['ID_FILME']) || $locacao['ID_FILME'] == $filme['ID_FILME'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($filme['TITULO']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>