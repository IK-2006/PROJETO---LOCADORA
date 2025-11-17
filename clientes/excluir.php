<?php
include '../config.php';

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    header("Location: listar.php?erro=ID do cliente não especificado");
    exit;
}

$id = $_GET['id'];

// Verificar se o cliente existe
$stmt = $pdo->prepare("SELECT * FROM CLIENTE WHERE ID_CLIENTE = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    header("Location: listar.php?erro=Cliente não encontrado");
    exit;
}

// Verificar se o cliente tem locações vinculadas
$stmt_locacoes = $pdo->prepare("SELECT COUNT(*) as total FROM LOCACAO WHERE ID_CLIENTE = ?");
$stmt_locacoes->execute([$id]);
$result = $stmt_locacoes->fetch(PDO::FETCH_ASSOC);

$tem_locacoes = $result['total'] > 0;

// Processar exclusão se confirmada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirmar'])) {
        try {
            // Se o cliente tem locações, não permitir exclusão
            if ($tem_locacoes) {
                header("Location: listar.php?erro=Não é possível excluir cliente com locações vinculadas");
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM CLIENTE WHERE ID_CLIENTE = ?");
            $stmt->execute([$id]);
            header("Location: listar.php?sucesso=Cliente excluído com sucesso!");
            exit;
        } catch(PDOException $e) {
            $erro = "Erro ao excluir cliente: " . $e->getMessage();
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
    <title>Excluir Cliente</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Excluir Cliente</h1>
            <a href="listar.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (isset($erro)): ?>
            <div class="alert error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <div class="confirmation-box">
            <h2>Confirmar Exclusão</h2>
            
            <?php if ($tem_locacoes): ?>
                <div class="alert error">
                    <strong>Atenção!</strong> Este cliente possui <?php echo $result['total']; ?> locação(ões) vinculada(s). 
                    Não é possível excluir clientes com locações ativas.
                </div>
                <div class="actions">
                    <a href="listar.php" class="btn btn-primary">Voltar para Listagem</a>
                </div>
            <?php else: ?>
                <div class="client-info">
                    <p><strong>ID:</strong> <?php echo $cliente['ID_CLIENTE']; ?></p>
                    <p><strong>Nome:</strong> <?php echo $cliente['NOME']; ?></p>
                    <p><strong>CPF:</strong> <?php echo $cliente['CPF']; ?></p>
                    <p><strong>Email:</strong> <?php echo $cliente['EMAIL']; ?></p>
                    <p><strong>Telefone:</strong> <?php echo $cliente['TELEFONE']; ?></p>
                </div>

                <div class="alert error">
                    <strong>Atenção!</strong> Esta ação não pode ser desfeita. Tem certeza que deseja excluir este cliente?
                </div>

                <form method="POST">
                    <div class="form-actions">
                        <button type="submit" name="confirmar" value="1" class="btn btn-delete">Sim, Excluir Cliente</button>
                        <button type="submit" name="cancelar" value="1" class="btn btn-primary">Cancelar</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>