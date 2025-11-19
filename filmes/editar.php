<?php
include '../config.php';

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = $_GET['id'];
$erro = '';

// Buscar dados do filme
$stmt = $pdo->prepare("SELECT * FROM " . TABLE_FILME . " WHERE ID_FILME = ?");
$stmt->execute([$id]);
$filme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$filme) {
    header("Location: listar.php?erro=Filme não encontrado");
    exit;
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
            header("Location: listar.php?sucesso=Filme atualizado com sucesso!");
            exit;
        } catch(PDOException $e) {
            $erro = "Erro ao atualizar filme: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Filme</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Filme</h1>
            <a href="listar.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" 
                       value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : htmlspecialchars($filme['TITULO']); ?>" 
                       required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="number" id="ano" name="ano" 
                       value="<?php echo isset($_POST['ano']) ? htmlspecialchars($_POST['ano']) : htmlspecialchars($filme['ANO']); ?>" 
                       required min="1900" max="<?php echo date('Y') + 5; ?>">
            </div>
            
            <div class="form-group">
                <label for="id_categoria">Categoria:</label>
                <select id="id_categoria" name="id_categoria">
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['ID_CATEGORIA']; ?>" 
                            <?php 
                            $selected = false;
                            if (isset($_POST['id_categoria'])) {
                                $selected = ($_POST['id_categoria'] == $categoria['ID_CATEGORIA']);
                            } else {
                                $selected = ($filme['ID_CATEGORIA'] == $categoria['ID_CATEGORIA']);
                            }
                            echo $selected ? 'selected' : '';
                            ?>>
                            <?php echo htmlspecialchars($categoria['NOME_CATEGORIA']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>