<?php
require_once '../config/database.php';

$erro = '';
$locacao = null;

// Buscar locação
if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = intval($_GET['id']);
$conn = getConnection();

// Buscar locação com dados relacionados
$stmt = $conn->prepare("SELECT l.*, c.nome as cliente_nome, f.titulo as filme_titulo 
                        FROM locacoes l
                        INNER JOIN clientes c ON l.cliente_id = c.id
                        INNER JOIN filmes f ON l.filme_id = f.id
                        WHERE l.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$locacao = $result->fetch_assoc();
$stmt->close();

if (!$locacao) {
    header("Location: listar.php");
    exit;
}

// Buscar clientes e filmes
$clientes_result = $conn->query("SELECT id, nome FROM clientes ORDER BY nome ASC");
$clientes = $clientes_result->fetch_all(MYSQLI_ASSOC);

// Buscar filmes disponíveis + o filme atual da locação
$filmes_result = $conn->query("SELECT id, titulo FROM filmes WHERE disponivel = 1 OR id = " . $locacao['filme_id'] . " ORDER BY titulo ASC");
$filmes = $filmes_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $filme_id = intval($_POST['filme_id']);
    $data_locacao = $_POST['data_locacao'];
    $data_devolucao_prevista = $_POST['data_devolucao_prevista'];
    $data_devolucao_real = !empty($_POST['data_devolucao_real']) ? $_POST['data_devolucao_real'] : null;
    $valor = floatval($_POST['valor']);
    $status = $_POST['status'];
    $observacoes = trim($_POST['observacoes']);
    
    if (empty($cliente_id) || empty($filme_id) || empty($data_locacao) || empty($data_devolucao_prevista) || empty($valor)) {
        $erro = 'Todos os campos obrigatórios devem ser preenchidos!';
    } else {
        // Se mudou o filme, atualizar disponibilidade
        if ($filme_id != $locacao['filme_id']) {
            // Liberar filme antigo
            $stmt = $conn->prepare("UPDATE filmes SET disponivel = 1 WHERE id = ?");
            $stmt->bind_param("i", $locacao['filme_id']);
            $stmt->execute();
            $stmt->close();
            
            // Reservar novo filme
            $stmt = $conn->prepare("UPDATE filmes SET disponivel = 0 WHERE id = ?");
            $stmt->bind_param("i", $filme_id);
            $stmt->execute();
            $stmt->close();
        }
        
        // Se status mudou para devolvida, marcar filme como disponível
        if ($status == 'devolvida' && $locacao['status'] == 'aberta') {
            $stmt = $conn->prepare("UPDATE filmes SET disponivel = 1 WHERE id = ?");
            $stmt->bind_param("i", $filme_id);
            $stmt->execute();
            $stmt->close();
        }
        
        // Atualizar locação
        $stmt = $conn->prepare("UPDATE locacoes SET cliente_id = ?, filme_id = ?, data_locacao = ?, data_devolucao_prevista = ?, data_devolucao_real = ?, valor = ?, status = ?, observacoes = ? WHERE id = ?");
        $stmt->bind_param("iisssdssi", $cliente_id, $filme_id, $data_locacao, $data_devolucao_prevista, $data_devolucao_real, $valor, $status, $observacoes, $id);
        
        if ($stmt->execute()) {
            header("Location: listar.php?msg=Locação atualizada com sucesso!");
            exit;
        } else {
            $erro = 'Erro ao atualizar locação: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Locação - Sistema de Locadora</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>✏️ Editar Locação</h1>
        </header>
        
        <nav class="menu">
            <a href="../index.php" class="menu-item">Início</a>
            <a href="../clientes/listar.php" class="menu-item">Clientes</a>
            <a href="listar.php" class="menu-item active">Locações</a>
            <a href="abertas.php" class="menu-item">Locações em Aberto</a>
        </nav>
        
        <main>
            <?php if ($erro): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="cliente_id">Cliente *</label>
                        <select id="cliente_id" name="cliente_id" required>
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>" <?php echo $cliente['id'] == $locacao['cliente_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cliente['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filme_id">Filme *</label>
                        <select id="filme_id" name="filme_id" required>
                            <option value="">Selecione um filme</option>
                            <?php foreach ($filmes as $filme): ?>
                                <option value="<?php echo $filme['id']; ?>" <?php echo $filme['id'] == $locacao['filme_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($filme['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_locacao">Data de Locação *</label>
                            <input type="date" id="data_locacao" name="data_locacao" required value="<?php echo $locacao['data_locacao']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="data_devolucao_prevista">Data de Devolução Prevista *</label>
                            <input type="date" id="data_devolucao_prevista" name="data_devolucao_prevista" required value="<?php echo $locacao['data_devolucao_prevista']; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_devolucao_real">Data de Devolução Real</label>
                            <input type="date" id="data_devolucao_real" name="data_devolucao_real" value="<?php echo $locacao['data_devolucao_real'] ? $locacao['data_devolucao_real'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="aberta" <?php echo $locacao['status'] == 'aberta' ? 'selected' : ''; ?>>Aberta</option>
                                <option value="devolvida" <?php echo $locacao['status'] == 'devolvida' ? 'selected' : ''; ?>>Devolvida</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="valor">Valor (R$) *</label>
                        <input type="number" id="valor" name="valor" step="0.01" min="0" required value="<?php echo $locacao['valor']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($locacao['observacoes']); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Atualizar</button>
                        <a href="listar.php" class="btn btn-secondary">❌ Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
