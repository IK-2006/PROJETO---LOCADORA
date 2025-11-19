<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

// Buscar categorias para o select
$categorias = $pdo->query("SELECT * FROM " . TABLE_CATEGORIA . " ORDER BY NOME_CATEGORIA")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $ano = trim($_POST['ano']);
    $id_categoria = $_POST['id_categoria'] ?: null;

    // Validações
    if (empty($titulo) || empty($ano)) {
        $erro = "Título e Ano são obrigatórios!";
    } elseif (!is_numeric($ano) || $ano < 1900 || $ano > date('Y') + 5) {
        $erro = "Ano inválido! Deve ser entre 1900 e " . (date('Y') + 5);
    } else {
        // Buscar próximo ID
        $novo_id = getNextId($pdo, TABLE_FILME, 'ID_FILME');

        try {
            $stmt = $pdo->prepare("INSERT INTO " . TABLE_FILME . " (ID_FILME, TITULO, ANO, ID_CATEGORIA) VALUES (?, ?, ?, ?)");
            $stmt->execute([$novo_id, $titulo, $ano, $id_categoria]);
            redirect('listar.php?sucesso=' . urlencode('Filme cadastrado com sucesso!'));
        } catch(PDOException $e) {
            $erro = "Erro ao cadastrar filme: " . $e->getMessage();
        }
    }
}

$pageTitle = 'Cadastrar Filme';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo esc($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="../actions/cadastrar.php?entity=filme">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" 
                       value="<?php echo isset($_POST['titulo']) ? esc($_POST['titulo']) : ''; ?>" 
                       required maxlength="100" placeholder="Digite o título do filme">
            </div>
            
            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="number" id="ano" name="ano" 
                       value="<?php echo isset($_POST['ano']) ? esc($_POST['ano']) : date('Y'); ?>" 
                       required min="1900" max="<?php echo date('Y') + 5; ?>" 
                       placeholder="Ex: 2024">
            </div>
            
            <div class="form-group">
                <label for="id_categoria">Categoria:</label>
                <select id="id_categoria" name="id_categoria">
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo esc($categoria['ID_CATEGORIA']); ?>" 
                            <?php echo (isset($_POST['id_categoria']) && $_POST['id_categoria'] == $categoria['ID_CATEGORIA']) ? 'selected' : ''; ?> >
                            <?php echo esc($categoria['NOME_CATEGORIA']); ?>
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