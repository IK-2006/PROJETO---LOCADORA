<?php
include '../config.php';

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = $_GET['id'];
$erro = '';

// Buscar dados do cliente
$stmt = $pdo->prepare("SELECT * FROM CLIENTE WHERE ID_CLIENTE = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    header("Location: listar.php?erro=Cliente não encontrado");
    exit;
}

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
        try {
            $stmt = $pdo->prepare("UPDATE CLIENTE SET NOME = ?, CPF = ?, TELEFONE = ?, EMAIL = ? WHERE ID_CLIENTE = ?");
            $stmt->execute([$nome, $cpf, $telefone, $email, $id]);
            header("Location: listar.php?sucesso=Cliente atualizado com sucesso!");
            exit;
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro = "Erro: CPF ou Email já cadastrado em outro cliente!";
            } else {
                $erro = "Erro ao atualizar cliente: " . $e->getMessage();
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
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Cliente</h1>
            <a href="listar.php" class="btn-voltar">← Voltar</a>
        </header>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : htmlspecialchars($cliente['NOME']); ?>" required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : htmlspecialchars($cliente['CPF']); ?>" required maxlength="11" pattern="[0-9]{11}" title="Digite apenas números (11 dígitos)">
            </div>
            
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : htmlspecialchars($cliente['TELEFONE']); ?>" required maxlength="20">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($cliente['EMAIL']); ?>" required maxlength="100">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>