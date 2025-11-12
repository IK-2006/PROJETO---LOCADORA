<?php
require_once '../config/database.php';

// Processar exclusão
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM locacoes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: listar.php?msg=Locação excluída com sucesso!");
    exit;
}

$conn = getConnection();
$query = "SELECT l.*, c.nome as cliente_nome, f.titulo as filme_titulo 
          FROM locacoes l
          INNER JOIN clientes c ON l.cliente_id = c.id
          INNER JOIN filmes f ON l.filme_id = f.id
          ORDER BY l.data_locacao DESC";
$result = $conn->query($query);
$locacoes = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locações - Sistema de Locadora</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🎬 Locações</h1>
        </header>
        
        <nav class="menu">
            <a href="../index.php" class="menu-item">Início</a>
            <a href="../clientes/listar.php" class="menu-item">Clientes</a>
            <a href="listar.php" class="menu-item active">Locações</a>
            <a href="abertas.php" class="menu-item">Locações em Aberto</a>
        </nav>
        
        <main>
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="cadastrar.php" class="btn btn-primary">➕ Nova Locação</a>
            </div>
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Filme</th>
                            <th>Data Locação</th>
                            <th>Devolução Prevista</th>
                            <th>Devolução Real</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($locacoes)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Nenhuma locação cadastrada</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($locacoes as $locacao): ?>
                                <tr>
                                    <td><?php echo $locacao['id']; ?></td>
                                    <td><?php echo htmlspecialchars($locacao['cliente_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($locacao['filme_titulo']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($locacao['data_locacao'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($locacao['data_devolucao_prevista'])); ?></td>
                                    <td><?php echo $locacao['data_devolucao_real'] ? date('d/m/Y', strtotime($locacao['data_devolucao_real'])) : '-'; ?></td>
                                    <td>R$ <?php echo number_format($locacao['valor'], 2, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $locacao['status'] == 'aberta' ? 'warning' : 'success'; ?>">
                                            <?php echo $locacao['status'] == 'aberta' ? 'Aberta' : 'Devolvida'; ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="editar.php?id=<?php echo $locacao['id']; ?>" class="btn btn-sm btn-edit">✏️ Editar</a>
                                        <a href="?excluir=<?php echo $locacao['id']; ?>" 
                                           class="btn btn-sm btn-delete" 
                                           onclick="return confirm('Tem certeza que deseja excluir esta locação?');">🗑️ Excluir</a>
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
