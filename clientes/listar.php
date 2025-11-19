<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Buscar clientes
$stmt = $pdo->query("SELECT * FROM " . TABLE_CLIENTE . " ORDER BY ID_CLIENTE");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Gerenciar Clientes';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php echo flash('sucesso'); ?>
        <?php echo flash('erro'); ?>

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
                    <td><?php echo esc($cliente['ID_CLIENTE']); ?></td>
                    <td><?php echo esc($cliente['NOME']); ?></td>
                    <td><?php echo esc($cliente['CPF']); ?></td>
                    <td><?php echo esc($cliente['TELEFONE']); ?></td>
                    <td><?php echo esc($cliente['EMAIL']); ?></td>
                    <td class="actions">
                        <a href="editar.php?id=<?php echo esc($cliente['ID_CLIENTE']); ?>" class="btn btn-edit">Editar</a>
                                <a href="../actions/excluir.php?entity=cliente&id=<?php echo esc($cliente['ID_CLIENTE']); ?>" 
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

<?php include __DIR__ . '/../includes/footer.php'; ?>