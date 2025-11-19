<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    redirect('listar.php?erro=' . urlencode('ID da locação não especificado'));
}

$id = $_GET['id'];

// Buscar dados da locação com informações relacionadas
$stmt = $pdo->prepare(" 
    SELECT L.*, C.NOME as CLIENTE_NOME, F.TITULO as FILME_TITULO 
    FROM " . TABLE_LOCACAO . " L 
    INNER JOIN " . TABLE_CLIENTE . " C ON L.ID_CLIENTE = C.ID_CLIENTE 
    INNER JOIN " . TABLE_FILME . " F ON L.ID_FILME = F.ID_FILME 
    WHERE L.ID_LOCACAO = ?
");
$stmt->execute([$id]);
$locacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$locacao) {
    redirect('listar.php?erro=' . urlencode('Locação não encontrada'));
}

// NOTE: exclusão é processada pelo handler genérico em actions/excluir.php

$pageTitle = 'Excluir Locação';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php if (isset($erro)): ?>
            <div class="alert error"><?php echo esc($erro); ?></div>
        <?php endif; ?>

        <div class="confirmation-box">
            <h2>Confirmar Exclusão</h2>
            
            <div class="locacao-info">
                <p><strong>ID da Locação:</strong> <?php echo esc($locacao['ID_LOCACAO']); ?></p>
                <p><strong>Cliente:</strong> <?php echo esc($locacao['CLIENTE_NOME']); ?></p>
                <p><strong>Filme:</strong> <?php echo esc($locacao['FILME_TITULO']); ?></p>
                <p><strong>Data de Locação:</strong> <?php echo esc(formatarData($locacao['DATA_LOCACAO'])); ?></p>
                <p><strong>Data de Devolução:</strong> <?php echo esc(formatarData($locacao['DATA_DEVOLUCAO'])); ?></p>
                <p><strong>Multa:</strong> <?php echo esc(formatarMoeda($locacao['MULTA'])); ?></p>
            </div>

            <div class="alert error">
                <strong>Atenção!</strong> Esta ação não pode ser desfeita. Tem certeza que deseja excluir esta locação?
            </div>

            <form method="POST" action="../actions/excluir.php?entity=locacao&id=<?php echo esc($locacao['ID_LOCACAO']); ?>">
                <div class="form-actions">
                    <button type="submit" name="confirmar" value="1" class="btn btn-delete">Sim, Excluir Locação</button>
                    <a href="listar.php" class="btn btn-primary">Cancelar</a>
                </div>
            </form>
        </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>