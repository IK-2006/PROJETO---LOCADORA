<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    redirect('listar.php');
}

$id = $_GET['id'];

// Buscar dados do filme
$stmt = $pdo->prepare("SELECT * FROM " . TABLE_FILME . " WHERE ID_FILME = ?");
$stmt->execute([$id]);
$filme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$filme) {
    redirect('listar.php');
}

// Verificar se tem locações
$stmt_check = $pdo->prepare("SELECT COUNT(*) as total FROM " . TABLE_LOCACAO . " WHERE ID_FILME = ?");
$stmt_check->execute([$id]);
$result = $stmt_check->fetch(PDO::FETCH_ASSOC);

$tem_locacoes = $result['total'] > 0;

// NOTE: exclusão é processada pelo handler genérico em actions/excluir.php

$pageTitle = 'Excluir Filme';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php if ($tem_locacoes): ?>
            <div class="alert error">
                <strong>Não é possível excluir!</strong><br>
                Este filme tem <?php echo esc($result['total']); ?> locação(ões) vinculada(s).
            </div>
            <a href="listar.php" class="btn">Voltar</a>

        <?php else: ?>
            <div class="alert info">
                <strong>Confirma a exclusão?</strong><br>
                Esta ação não pode ser desfeita.
            </div>

            <div class="client-info">
                <p><strong>Filme:</strong> <?php echo esc($filme['TITULO']); ?></p>
                <p><strong>Ano:</strong> <?php echo esc($filme['ANO']); ?></p>
                <p><strong>ID:</strong> <?php echo esc($filme['ID_FILME']); ?></p>
            </div>

            <form method="POST" action="../actions/excluir.php?entity=filme&id=<?php echo esc($filme['ID_FILME']); ?>">
                <button type="submit" name="confirmar" class="btn btn-delete">Sim, Excluir</button>
                <a href="listar.php" class="btn btn-primary">Cancelar</a>
            </form>
        <?php endif; ?>
        
        <br>
        <a href="listar.php" class="btn">← Voltar para lista de filmes</a>

<?php include __DIR__ . '/../includes/footer.php'; ?>