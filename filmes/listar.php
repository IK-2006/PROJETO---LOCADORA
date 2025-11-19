<?php
include '../config.php';

// Excluir filme
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    
    // Verificar se o filme tem locações vinculadas
    $stmt_check = $pdo->prepare("SELECT COUNT(*) as total FROM " . TABLE_LOCACAO . " WHERE ID_FILME = ?");
    $stmt_check->execute([$id]);
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($result['total'] > 0) {
        header("Location: listar.php?erro=Não é possível excluir filme com locações vinculadas");
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM " . TABLE_FILME . " WHERE ID_FILME = ?");
    if ($stmt->execute([$id])) {
        header("Location: listar.php?sucesso=Filme excluído com sucesso!");
        exit;
    } else {
        header("Location: listar.php?erro=Erro ao excluir filme");
        exit;
    }
}

// Buscar filmes com informações da categoria
$stmt = $pdo->query("
    SELECT F.*, C.NOME_CATEGORIA 
    FROM " . TABLE_FILME . " F 
    LEFT JOIN " . TABLE_CATEGORIA . " C ON F.ID_CATEGORIA = C.ID_CATEGORIA 
    ORDER BY F.TITULO
");
$filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Filmes</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gerenciar Filmes</h1>
            <a href="../index.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_GET['erro']); ?></div>
        <?php endif; ?>

        <div class="actions">
            <a href="cadastrar.php" class="btn btn-primary">Novo Filme</a>
        </div>

        <?php if (count($filmes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Ano</th>
                    <th>Categoria</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filmes as $filme): ?>
                <tr>
                    <td><?php echo htmlspecialchars($filme['ID_FILME']); ?></td>
                    <td><?php echo htmlspecialchars($filme['TITULO']); ?></td>
                    <td><?php echo htmlspecialchars($filme['ANO']); ?></td>
                    <td><?php echo htmlspecialchars($filme['NOME_CATEGORIA'] ?? 'Sem categoria'); ?></td>
                    <td class="actions">
                        <a href="editar.php?id=<?php echo $filme['ID_FILME']; ?>" class="btn btn-edit">Editar</a>
                        <a href="excluir.php?id=<?php echo $filme['ID_FILME']; ?>" class="btn btn-delete">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert info">Nenhum filme cadastrado.</div>
        <?php endif; ?>
    </div>
</body>
</html>