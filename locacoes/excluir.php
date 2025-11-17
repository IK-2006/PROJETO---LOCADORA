<?php
include '../config.php';

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    header("Location: listar.php?erro=ID da locação não especificado");
    exit;
}

$id = $_GET['id'];

// Buscar dados da locação com informações relacionadas
$stmt = $pdo->prepare("
    SELECT L.*, C.NOME as CLIENTE_NOME, F.TITULO as FILME_TITULO 
    FROM LOCACAO L 
    INNER JOIN CLIENTE C ON L.ID_CLIENTE = C.ID_CLIENTE 
    INNER JOIN FILME F ON L.ID_FILME = F.ID_FILME 
    WHERE L.ID_LOCACAO = ?
");
$stmt->execute([$id]);
$locacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$locacao) {
    header("Location: listar.php?erro=Locação não encontrada");
    exit;
}

// Processar exclusão se confirmada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirmar'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM LOCACAO WHERE ID_LOCACAO = ?");
            $stmt->execute([$id]);
            header("Location: listar.php?sucesso=Locação excluída com sucesso!");
            exit;
        } catch(PDOException $e) {
            $erro = "Erro ao excluir locação: " . $e->getMessage();
        }
    } else {
        // Se cancelou, voltar para a listagem
        header("Location: listar.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Locação</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Excluir Locação</h1>
            <a href="listar.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (isset($erro)): ?>
            <div class="alert error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <div class="confirmation-box">
            <h2>Confirmar Exclusão</h2>
            
            <div class="locacao-info">
                <p><strong>ID da Locação:</strong> <?php echo $locacao['ID_LOCACAO']; ?></p>
                <p><strong>Cliente:</strong> <?php echo $locacao['CLIENTE_NOME']; ?></p>
                <p><strong>Filme:</strong> <?php echo $locacao['FILME_TITULO']; ?></p>
                <p><strong>Data de Locação:</strong> <?php echo date('d/m/Y', strtotime($locacao['DATA_LOCACAO'])); ?></p>
                <p><strong>Data de Devolução:</strong> <?php echo date('d/m/Y', strtotime($locacao['DATA_DEVOLUCAO'])); ?></p>
                <p><strong>Multa:</strong> R$ <?php echo number_format($locacao['MULTA'], 2, ',', '.'); ?></p>
            </div>

            <div class="alert error">
                <strong>Atenção!</strong> Esta ação não pode ser desfeita. Tem certeza que deseja excluir esta locação?
            </div>

            <form method="POST">
                <div class="form-actions">
                    <button type="submit" name="confirmar" value="1" class="btn btn-delete">Sim, Excluir Locação</button>
                    <button type="submit" name="cancelar" value="1" class="btn btn-primary">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>