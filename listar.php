<?php
require_once '../config/database.php';

// Processar exclusão
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: listar.php?msg=Cliente excluído com sucesso!");
    exit;
}

$conn = getConnection();
$result = $conn->query("SELECT * FROM clientes ORDER BY nome ASC");
$clientes = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Sistema de Locadora</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>👥 Clientes</h1>
        </header>
        
        <nav class="menu">
            <a href="../index.php" class="menu-item">Início</a>
            <a href="listar.php" class="menu-item active">Clientes</a>
            <a href="../locacoes/listar.php" class="menu-item">Locações</a>
            <a href="../locacoes/abertas.php" class="menu-item">Locações em Aberto</a>
        </nav>
        
        <main>
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="cadastrar.php" class="btn btn-primary">➕ Novo Cliente</a>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Data Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum cliente cadastrado</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?php echo $cliente['id']; ?></td>
                                    <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['cpf']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($cliente['data_cadastro'])); ?></td>
                                    <td class="actions">
                                        <a href="editar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-edit">✏️ Editar</a>
                                        <a href="?excluir=<?php echo $cliente['id']; ?>" 
                                           class="btn btn-sm btn-delete" 
                                           onclick="return confirm('Tem certeza que deseja excluir este cliente?');">🗑️ Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
