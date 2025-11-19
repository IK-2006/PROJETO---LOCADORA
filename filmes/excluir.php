<?php
include '../config.php';

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    header("Location: listar.php?erro=ID do filme não especificado");
    exit;
}

$id = $_GET['id'];

// Verificar se o filme existe
$stmt = $pdo->prepare("SELECT F.*, C.NOME_CATEGORIA FROM FILME F 
                      LEFT JOIN CATEGORIA C ON F.ID_CATEGORIA = C.ID_CATEGORIA 
                      WHERE F.ID_FILME = ?");
$stmt->execute([$id]);
$filme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$filme) {
    header("Location: listar.php?erro=Filme não encontrado");
    exit;
}

// Verificar se o filme tem locações vinculadas
$stmt_locacoes = $pdo->prepare("SELECT COUNT(*) as total FROM LOCACAO WHERE ID_FILME = ?");
$stmt_locacoes->execute([$id]);
$result = $stmt_locacoes->fetch(PDO::FETCH_ASSOC);

$tem_locacoes = $result['total'] > 0;

// Processar exclusão se confirmada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirmar'])) {
        // Se o filme tem locações, não permitir exclusão
        if ($tem_locacoes) {
            header("Location: listar.php?erro=Não é possível excluir filme com locações vinculadas");
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("DELETE FROM FILME WHERE ID_FILME = ?");
            $stmt->execute([$id]);
            header("Location: listar.php?sucesso=Filme excluído com sucesso!");
            exit;
        } catch(PDOException $e) {
            $erro = "Erro ao excluir filme: " . $e->getMessage();
        }
    } else {
        // Se cancelou, voltar para a listagem
        header("Location: listar.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Filme</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Excluir Filme</h1>
            <a href="listar.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (isset($erro)): ?>
            <div class="alert error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <div class="confirmation-box">
            <h2>Confirmar Exclusão</h2>
            
            <?php if ($tem_locacoes): ?>
                <div class="alert error">
                    <strong>Atenção!</strong> Este filme possui <?php echo $result['total']; ?> locação(ões) vinculada(s). 
                    Não é possível excluir filmes com locações ativas.
                </div>
                <div class="actions">
                    <a href="listar.php" class="btn btn-primary">Voltar para Listagem</a>
                </div>
            <?php else: ?>
                <div class="filme-info">
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($filme['ID_FILME']); ?></p>
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($filme['TITULO']); ?></p>
                    <p><strong>Ano:</strong> <?php echo htmlspecialchars($filme['ANO']); ?></p>
                    <p><strong>Categoria:</strong> <?php echo htmlspecialchars($filme['NOME_CATEGORIA'] ?? 'Sem categoria'); ?></p>
                </div>

                <div class="alert error">
                    <strong>Atenção!</strong> Esta ação não pode ser desfeita. Tem certeza que deseja excluir este filme?
                </div>

                <form method="POST">
                    <div class="form-actions">
                        <button type="submit" name="confirmar" value="1" class="btn btn-delete">Sim, Excluir Filme</button>
                        <a href="listar.php" class="btn btn-primary">Cancelar</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>