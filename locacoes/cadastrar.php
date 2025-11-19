<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Buscar clientes e filmes para os selects
$clientes = $pdo->query("SELECT ID_CLIENTE, NOME FROM " . TABLE_CLIENTE . " ORDER BY NOME")->fetchAll(PDO::FETCH_ASSOC);
$filmes = $pdo->query("SELECT ID_FILME, TITULO FROM " . TABLE_FILME . " ORDER BY TITULO")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_locacao = $_POST['data_locacao'];
    $data_devolucao = $_POST['data_devolucao'];
    $multa = $_POST['multa'] ? floatval($_POST['multa']) : 0.00;
    $id_filme = $_POST['id_filme'];
    $id_cliente = $_POST['id_cliente'];

    // Validações
    if (empty($data_locacao) || empty($data_devolucao) || empty($id_filme) || empty($id_cliente)) {
        $erro = "Todos os campos são obrigatórios!";
    } elseif ($data_devolucao < $data_locacao) {
        $erro = "Data de devolução não pode ser anterior à data de locação!";
    } else {
        // Buscar próximo ID
        $novo_id = getNextId($pdo, TABLE_LOCACAO, 'ID_LOCACAO');

        try {
            $stmt = $pdo->prepare("INSERT INTO " . TABLE_LOCACAO . " (ID_LOCACAO, DATA_LOCACAO, DATA_DEVOLUCAO, MULTA, ID_FILME, ID_CLIENTE) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$novo_id, $data_locacao, $data_devolucao, $multa, $id_filme, $id_cliente]);
            redirect('listar.php?sucesso=' . urlencode('Locação cadastrada com sucesso!'));
        } catch(PDOException $e) {
            $erro = "Erro ao cadastrar locação: " . $e->getMessage();
        }
    }
}

$pageTitle = 'Cadastrar Locação';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo esc($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="../actions/cadastrar.php?entity=locacao">
            <div class="form-group">
                <label for="data_locacao">Data Locação:</label>
                <input type="date" id="data_locacao" name="data_locacao" 
                       value="<?php echo isset($_POST['data_locacao']) ? esc($_POST['data_locacao']) : date('Y-m-d'); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="data_devolucao">Data Devolução:</label>
                <input type="date" id="data_devolucao" name="data_devolucao" 
                       value="<?php echo isset($_POST['data_devolucao']) ? esc($_POST['data_devolucao']) : date('Y-m-d', strtotime('+3 days')); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="multa">Multa (R$):</label>
                <input type="number" id="multa" name="multa" step="0.01" min="0" 
                       value="<?php echo isset($_POST['multa']) ? esc($_POST['multa']) : '0.00'; ?>">
            </div>
            
            <div class="form-group">
                <label for="id_cliente">Cliente:</label>
                <select id="id_cliente" name="id_cliente" required>
                    <option value="">Selecione um cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo esc($cliente['ID_CLIENTE']); ?>" 
                            <?php echo (isset($_POST['id_cliente']) && $_POST['id_cliente'] == $cliente['ID_CLIENTE']) ? 'selected' : ''; ?> >
                            <?php echo esc($cliente['NOME']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_filme">Filme:</label>
                <select id="id_filme" name="id_filme" required>
                    <option value="">Selecione um filme</option>
                    <?php foreach ($filmes as $filme): ?>
                        <option value="<?php echo esc($filme['ID_FILME']); ?>" 
                            <?php echo (isset($_POST['id_filme']) && $_POST['id_filme'] == $filme['ID_FILME']) ? 'selected' : ''; ?> >
                            <?php echo esc($filme['TITULO']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>

<?php include __DIR__ . '/../includes/footer.php'; ?>