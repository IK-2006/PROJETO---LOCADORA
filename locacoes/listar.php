<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Buscar locações com dados relacionados
$stmt = $pdo->query(" 
    SELECT L.*, C.NOME as CLIENTE_NOME, F.TITULO as FILME_TITULO 
    FROM " . TABLE_LOCACAO . " L 
    INNER JOIN " . TABLE_CLIENTE . " C ON L.ID_CLIENTE = C.ID_CLIENTE 
    INNER JOIN " . TABLE_FILME . " F ON L.ID_FILME = F.ID_FILME 
    ORDER BY L.ID_LOCACAO DESC
");
$locacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Gerenciar Locações';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php echo flash('sucesso'); ?>
        <?php echo flash('erro'); ?>

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
                    <td><?php echo esc($locacao['ID_LOCACAO']); ?></td>
                    <td><?php echo esc(formatarData($locacao['DATA_LOCACAO'])); ?></td>
                    <td><?php echo esc(formatarData($locacao['DATA_DEVOLUCAO'])); ?></td>
                    <td><?php echo esc(formatarMoeda($locacao['MULTA'])); ?></td>
                    <td><?php echo esc($locacao['CLIENTE_NOME']); ?></td>
                    <td><?php echo esc($locacao['FILME_TITULO']); ?></td>
                    <td class="actions">
                        <a href="editar.php?id=<?php echo esc($locacao['ID_LOCACAO']); ?>" class="btn btn-edit">Editar</a>
                                <a href="../actions/excluir.php?entity=locacao&id=<?php echo esc($locacao['ID_LOCACAO']); ?>" 
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

<?php include __DIR__ . '/../includes/footer.php'; ?>