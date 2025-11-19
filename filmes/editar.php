<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_GET['id'])) {
    redirect('listar.php');
}

$id = $_GET['id'];
$erro = '';

// Buscar dados do filme
$stmt = $pdo->prepare("SELECT * FROM " . TABLE_FILME . " WHERE ID_FILME = ?");
$stmt->execute([$id]);
$filme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$filme) {
    redirect('listar.php?erro=' . urlencode('Filme não encontrado'));
}

// Buscar categorias para o select
$categorias = $pdo->query("SELECT * FROM " . TABLE_CATEGORIA . " ORDER BY NOME_CATEGORIA")->fetchAll(PDO::FETCH_ASSOC);

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
        try {
            $stmt = $pdo->prepare("UPDATE " . TABLE_FILME . " SET TITULO = ?, ANO = ?, ID_CATEGORIA = ? WHERE ID_FILME = ?");
            $stmt->execute([$titulo, $ano, $id_categoria, $id]);
            redirect('listar.php?sucesso=' . urlencode('Filme atualizado com sucesso!'));
        } catch(PDOException $e) {
            $erro = "Erro ao atualizar filme: " . $e->getMessage();
        }
    }
}

$pageTitle = 'Editar Filme';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo esc($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="../actions/cadastrar.php?entity=filme">
            <input type="hidden" name="id" value="<?php echo esc($filme['ID_FILME']); ?>">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" 
                       value="<?php echo isset($_POST['titulo']) ? esc($_POST['titulo']) : esc($filme['TITULO']); ?>" 
                       required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="number" id="ano" name="ano" 
                       value="<?php echo isset($_POST['ano']) ? esc($_POST['ano']) : esc($filme['ANO']); ?>" 
                       required min="1900" max="<?php echo date('Y') + 5; ?>">
            </div>
            
            <div class="form-group">
                <label for="id_categoria">Categoria:</label>
                <select id="id_categoria" name="id_categoria">
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo esc($categoria['ID_CATEGORIA']); ?>" 
                            <?php 
                            $selected = false;
                            if (isset($_POST['id_categoria'])) {
                                $selected = ($_POST['id_categoria'] == $categoria['ID_CATEGORIA']);
                            } else {
                                $selected = ($filme['ID_CATEGORIA'] == $categoria['ID_CATEGORIA']);
                            }
                            echo $selected ? 'selected' : '';
                            ?> >
                            <?php echo esc($categoria['NOME_CATEGORIA']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>

<?php include __DIR__ . '/../includes/footer.php'; ?>