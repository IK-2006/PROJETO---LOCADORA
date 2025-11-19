<?php
// actions/excluir.php - handler genérico de exclusão
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

$entity = isset($_GET['entity']) ? strtolower($_GET['entity']) : null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$entity || !$id) {
    redirect('../index.php');
}

$map = [
    'cliente' => TABLE_CLIENTE,
    'filme' => TABLE_FILME,
    'locacao' => TABLE_LOCACAO,
];

if (!isset($map[$entity])) {
    redirect('../index.php');
}

$table = $map[$entity];

try {
    // validações específicas
    if ($entity === 'cliente') {
        // não permitir exclusão se houver locações vinculadas
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM " . TABLE_LOCACAO . " WHERE ID_CLIENTE = ?");
        $stmt->execute([$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r && $r['total'] > 0) {
            redirect('../clientes/listar.php?erro=' . urlencode('Não é possível excluir cliente com locações vinculadas'));
        }
    }

    if ($entity === 'filme') {
        // não permitir exclusão se houver locações vinculadas
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM " . TABLE_LOCACAO . " WHERE ID_FILME = ?");
        $stmt->execute([$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r && $r['total'] > 0) {
            redirect('../filmes/listar.php?erro=' . urlencode('Não é possível excluir filme com locações vinculadas'));
        }
    }

    // exclusão direta
    $stmt = $pdo->prepare("DELETE FROM $table WHERE " . (strtoupper($entity) === 'LOCACAO' ? 'ID_LOCACAO = ?' : (strtoupper($entity) === 'FILME' ? 'ID_FILME = ?' : 'ID_CLIENTE = ?')));
    $stmt->execute([$id]);
    redirect('../' . $entity . 's/listar.php?sucesso=' . urlencode(ucfirst($entity) . ' excluído com sucesso!'));

} catch (PDOException $e) {
    redirect('../' . $entity . 's/listar.php?erro=' . urlencode('Erro ao excluir: ' . $e->getMessage()));
}
