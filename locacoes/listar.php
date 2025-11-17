<?php
include '../config.php';

// Excluir locação
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    
    $stmt = $pdo->prepare("DELETE FROM LOCACAO WHERE ID_LOCACAO = ?");
    if ($stmt->execute([$id])) {
        header("Location: listar.php?sucesso=Locação excluída com sucesso!");
        exit;
    } else {
        header("Location: listar.php?erro=Erro ao excluir locação");
        exit;
    }
}

// Buscar locações com dados relacionados
$stmt = $pdo->query("
    SELECT L.*, C.NOME as CLIENTE_NOME, F.TITULO as FILME_TITULO 
    FROM LOCACAO L 
    INNER JOIN CLIENTE C ON L.ID_CLIENTE = C.ID_CLIENTE 
    INNER JOIN FILME F ON L.ID_FILME = F.ID_FILME 
    ORDER BY L.ID_LOCACAO DESC
");
$locacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Locações</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gerenciar Locações</h1>
            <a href="../index.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_GET['erro']); ?></div>
        <?php endif; ?>

        <div class="actions">
            <a href="cadastrar.php" class="btn btn-primary">Nova Locação</a>
        </div>

        <?php if (count($locacoes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data Locação</th>
                    <th>Data Devolução</th>
                    <th>Multa (R$)</th>
                    <th>Cliente</th>
                    <th>Filme</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locacoes as $locacao): ?>
                <tr>
                    <td><?php echo htmlspecialchars($locacao['ID_LOCACAO']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($locacao['DATA_LOCACAO'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($locacao['DATA_DEVOLUCAO'])); ?></td>
                    <td>R$ <?php echo number_format($locacao['MULTA'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($locacao['CLIENTE_NOME']); ?></td>
                    <td><?php echo htmlspecialchars($locacao['FILME_TITULO']); ?></td>
                    <td class="actions">
                        <a href="editar.php?id=<?php echo $locacao['ID_LOCACAO']; ?>" class="btn btn-edit">Editar</a>
                        <a href="listar.php?excluir=<?php echo $locacao['ID_LOCACAO']; ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('Tem certeza que deseja excluir esta locação?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert info">Nenhuma locação cadastrada.</div>
        <?php endif; ?>
    </div>
</body>
</html>