<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Buscar filmes com informações da categoria
$stmt = $pdo->query(" 
    SELECT F.*, C.NOME_CATEGORIA 
    FROM " . TABLE_FILME . " F 
    LEFT JOIN " . TABLE_CATEGORIA . " C ON F.ID_CATEGORIA = C.ID_CATEGORIA 
    ORDER BY F.TITULO
");
$filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Gerenciar Filmes';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php echo flash('sucesso'); ?>
        <?php echo flash('erro'); ?>

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
                    <td><?php echo esc($filme['ID_FILME']); ?></td>
                    <td><?php echo esc($filme['TITULO']); ?></td>
                    <td><?php echo esc($filme['ANO']); ?></td>
                    <td><?php echo esc($filme['NOME_CATEGORIA'] ?? 'Sem categoria'); ?></td>
                    <td class="actions">
                        <a href="editar.php?id=<?php echo esc($filme['ID_FILME']); ?>" class="btn btn-edit">Editar</a>
                        <a href="../actions/excluir.php?entity=filme&id=<?php echo esc($filme['ID_FILME']); ?>" class="btn btn-delete">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert info">Nenhum filme cadastrado.</div>
        <?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>