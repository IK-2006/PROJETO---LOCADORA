<?php
include '../config.php';

// Excluir cliente
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    
    // Verificar se o cliente tem locações vinculadas
    $stmt_check = $pdo->prepare("SELECT COUNT(*) as total FROM LOCACAO WHERE ID_CLIENTE = ?");
    $stmt_check->execute([$id]);
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($result['total'] > 0) {
        header("Location: listar.php?erro=Não é possível excluir cliente com locações vinculadas");
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM CLIENTE WHERE ID_CLIENTE = ?");
    if ($stmt->execute([$id])) {
        header("Location: listar.php?sucesso=Cliente excluído com sucesso!");
        exit;
    } else {
        header("Location: listar.php?erro=Erro ao excluir cliente");
        exit;
    }
}

// Buscar clientes
$stmt = $pdo->query("SELECT * FROM CLIENTE ORDER BY ID_CLIENTE");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Clientes</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gerenciar Clientes</h1>
            <a href="../index.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_GET['sucesso']); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_GET['erro']); ?></div>
        <?php endif; ?>

        <div class="actions">
            <a href="cadastrar.php" class="btn btn-primary">Novo Cliente</a>
        </div>

        <?php if (count($clientes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cliente['ID_CLIENTE']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['NOME']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['CPF']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['TELEFONE']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['EMAIL']); ?></td>
                    <td class="actions">
                        <a href="editar.php?id=<?php echo $cliente['ID_CLIENTE']; ?>" class="btn btn-edit">Editar</a>
                        <a href="listar.php?excluir=<?php echo $cliente['ID_CLIENTE']; ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('Tem certeza que deseja excluir o cliente <?php echo addslashes($cliente['NOME']); ?>?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert info">Nenhum cliente cadastrado.</div>
        <?php endif; ?>
    </div>
</body>
</html>