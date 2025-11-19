<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    redirect('listar.php?erro=' . urlencode('ID do cliente não especificado'));
}

$id = $_GET['id'];

// Verificar se o cliente existe
$stmt = $pdo->prepare("SELECT * FROM " . TABLE_CLIENTE . " WHERE ID_CLIENTE = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    redirect('listar.php?erro=' . urlencode('Cliente não encontrado'));
}

// Verificar se o cliente tem locações vinculadas
$stmt_locacoes = $pdo->prepare("SELECT COUNT(*) as total FROM " . TABLE_LOCACAO . " WHERE ID_CLIENTE = ?");
$stmt_locacoes->execute([$id]);
$result = $stmt_locacoes->fetch(PDO::FETCH_ASSOC);

$tem_locacoes = $result['total'] > 0;

// NOTE: exclusão é processada pelo handler genérico em actions/excluir.php

$pageTitle = 'Excluir Cliente';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php if (isset($erro)): ?>
            <div class="alert error"><?php echo esc($erro); ?></div>
        <?php endif; ?>

        <div class="confirmation-box">
            <h2>Confirmar Exclusão</h2>

            <?php if ($tem_locacoes): ?>
                <div class="alert error">
                    <strong>Atenção!</strong> Este cliente possui <?php echo esc($result['total']); ?> locação(ões) vinculada(s). 
                    Não é possível excluir clientes com locações ativas.
                </div>
                <div class="actions">
                    <a href="listar.php" class="btn btn-primary">Voltar para Listagem</a>
                </div>
            <?php else: ?>
                <div class="client-info">
                    <p><strong>ID:</strong> <?php echo esc($cliente['ID_CLIENTE']); ?></p>
                    <p><strong>Nome:</strong> <?php echo esc($cliente['NOME']); ?></p>
                    <p><strong>CPF:</strong> <?php echo esc($cliente['CPF']); ?></p>
                    <p><strong>Email:</strong> <?php echo esc($cliente['EMAIL']); ?></p>
                    <p><strong>Telefone:</strong> <?php echo esc($cliente['TELEFONE']); ?></p>
                </div>

                <div class="alert error">
                    <strong>Atenção!</strong> Esta ação não pode ser desfeita. Tem certeza que deseja excluir este cliente?
                </div>

                <form method="POST" action="../actions/excluir.php?entity=cliente&id=<?php echo esc($cliente['ID_CLIENTE']); ?>">
                    <div class="form-actions">
                        <button type="submit" name="confirmar" value="1" class="btn btn-delete">Sim, Excluir Cliente</button>
                        <a href="listar.php" class="btn btn-primary">Cancelar</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>