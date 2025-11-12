<?php
require_once '../config/database.php';

$erro = '';

// Buscar clientes e filmes disponíveis
$conn = getConnection();
$clientes_result = $conn->query("SELECT id, nome FROM clientes ORDER BY nome ASC");
$clientes = $clientes_result->fetch_all(MYSQLI_ASSOC);

$filmes_result = $conn->query("SELECT id, titulo FROM filmes WHERE disponivel = 1 ORDER BY titulo ASC");
$filmes = $filmes_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $filme_id = intval($_POST['filme_id']);
    $data_locacao = $_POST['data_locacao'];
    $data_devolucao_prevista = $_POST['data_devolucao_prevista'];
    $valor = floatval($_POST['valor']);
    $observacoes = trim($_POST['observacoes']);
    
    if (empty($cliente_id) || empty($filme_id) || empty($data_locacao) || empty($data_devolucao_prevista) || empty($valor)) {
        $erro = 'Todos os campos obrigatórios devem ser preenchidos!';
    } else {
        $stmt = $conn->prepare("INSERT INTO locacoes (cliente_id, filme_id, data_locacao, data_devolucao_prevista, valor, observacoes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissds", $cliente_id, $filme_id, $data_locacao, $data_devolucao_prevista, $valor, $observacoes);
        
        if ($stmt->execute()) {
            // Marcar filme como indisponível
            $stmt2 = $conn->prepare("UPDATE filmes SET disponivel = 0 WHERE id = ?");
            $stmt2->bind_param("i", $filme_id);
            $stmt2->execute();
            $stmt2->close();
            
            header("Location: listar.php?msg=Locação cadastrada com sucesso!");
            exit;
        } else {
            $erro = 'Erro ao cadastrar locação: ' . $conn->error;
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
    <title>Cadastrar Locação - Sistema de Locadora</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>➕ Cadastrar Locação</h1>
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
                                <option value="<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filme_id">Filme *</label>
                        <select id="filme_id" name="filme_id" required>
                            <option value="">Selecione um filme</option>
                            <?php foreach ($filmes as $filme): ?>
                                <option value="<?php echo $filme['id']; ?>"><?php echo htmlspecialchars($filme['titulo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_locacao">Data de Locação *</label>
                            <input type="date" id="data_locacao" name="data_locacao" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="data_devolucao_prevista">Data de Devolução Prevista *</label>
                            <input type="date" id="data_devolucao_prevista" name="data_devolucao_prevista" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="valor">Valor (R$) *</label>
                        <input type="number" id="valor" name="valor" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea id="observacoes" name="observacoes" rows="3"><?php echo isset($_POST['observacoes']) ? htmlspecialchars($_POST['observacoes']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Salvar</button>
                        <a href="listar.php" class="btn btn-secondary">❌ Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        // Definir data de devolução padrão (7 dias a partir de hoje)
        document.addEventListener('DOMContentLoaded', function() {
            const dataLocacao = document.getElementById('data_locacao');
            const dataDevolucao = document.getElementById('data_devolucao_prevista');
            
            if (dataLocacao.value && !dataDevolucao.value) {
                const data = new Date(dataLocacao.value);
                data.setDate(data.getDate() + 7);
                dataDevolucao.value = data.toISOString().split('T')[0];
            }
            
            dataLocacao.addEventListener('change', function() {
                if (this.value && !dataDevolucao.value) {
                    const data = new Date(this.value);
                    data.setDate(data.getDate() + 7);
                    dataDevolucao.value = data.toISOString().split('T')[0];
                }
            });
        });
    </script>
</body>
</html>
