<?php
include '../config.php';

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
        $stmt = $pdo->query("SELECT COALESCE(MAX(ID_FILME), 0) + 1 as novo_id FROM " . TABLE_FILME);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $novo_id = $result['novo_id'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO " . TABLE_FILME . " (ID_FILME, TITULO, ANO, ID_CATEGORIA) VALUES (?, ?, ?, ?)");
            $stmt->execute([$novo_id, $titulo, $ano, $id_categoria]);
            header("Location: listar.php?sucesso=Filme cadastrado com sucesso!");
            exit;
        } catch(PDOException $e) {
            $erro = "Erro ao cadastrar filme: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Filme</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Cadastrar Filme</h1>
            <a href="listar.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" 
                       value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>" 
                       required maxlength="100" placeholder="Digite o título do filme">
            </div>
            
            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="number" id="ano" name="ano" 
                       value="<?php echo isset($_POST['ano']) ? htmlspecialchars($_POST['ano']) : date('Y'); ?>" 
                       required min="1900" max="<?php echo date('Y') + 5; ?>" 
                       placeholder="Ex: 2024">
            </div>
            
            <div class="form-group">
                <label for="id_categoria">Categoria:</label>
                <select id="id_categoria" name="id_categoria">
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['ID_CATEGORIA']; ?>" 
                            <?php echo (isset($_POST['id_categoria']) && $_POST['id_categoria'] == $categoria['ID_CATEGORIA']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['NOME_CATEGORIA']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>