<?php
include '../config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    
    // Validações
    if (empty($nome) || empty($cpf) || empty($telefone) || empty($email)) {
        $erro = "Todos os campos são obrigatórios!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido!";
    } else {
        // Buscar próximo ID
        $stmt = $pdo->query("SELECT COALESCE(MAX(ID_CLIENTE), 0) + 1 as novo_id FROM CLIENTE");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $novo_id = $result['novo_id'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO CLIENTE (ID_CLIENTE, NOME, CPF, TELEFONE, EMAIL) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$novo_id, $nome, $cpf, $telefone, $email]);
            header("Location: listar.php?sucesso=Cliente cadastrado com sucesso!");
            exit;
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro = "Erro: CPF ou Email já cadastrado!";
            } else {
                $erro = "Erro ao cadastrar cliente: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Cadastrar Cliente</h1>
            <a href="listar.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" required maxlength="11" pattern="[0-9]{11}" title="Digite apenas números (11 dígitos)">
            </div>
            
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" required maxlength="20">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required maxlength="100">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>