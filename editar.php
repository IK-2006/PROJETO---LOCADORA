<?php
require_once '../config/database.php';

$erro = '';
$cliente = null;

// Buscar cliente
if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = intval($_GET['id']);
$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();
$stmt->close();

if (!$cliente) {
    header("Location: listar.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $endereco = trim($_POST['endereco']);
    
    if (empty($nome) || empty($cpf)) {
        $erro = 'Nome e CPF são obrigatórios!';
    } else {
        // Verificar se CPF já existe em outro cliente
        $stmt = $conn->prepare("SELECT id FROM clientes WHERE cpf = ? AND id != ?");
        $stmt->bind_param("si", $cpf, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $erro = 'CPF já cadastrado para outro cliente!';
        } else {
            $stmt = $conn->prepare("UPDATE clientes SET nome = ?, cpf = ?, telefone = ?, email = ?, endereco = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $nome, $cpf, $telefone, $email, $endereco, $id);
            
            if ($stmt->execute()) {
                header("Location: listar.php?msg=Cliente atualizado com sucesso!");
                exit;
            } else {
                $erro = 'Erro ao atualizar cliente: ' . $conn->error;
            }
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
    <title>Editar Cliente - Sistema de Locadora</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>✏️ Editar Cliente</h1>
        </header>
        
        <nav class="menu">
            <a href="../index.php" class="menu-item">Início</a>
            <a href="listar.php" class="menu-item active">Clientes</a>
            <a href="../locacoes/listar.php" class="menu-item">Locações</a>
            <a href="../locacoes/abertas.php" class="menu-item">Locações em Aberto</a>
        </nav>
        
        <main>
            <?php if ($erro): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nome">Nome *</label>
                        <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($cliente['nome']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cpf">CPF *</label>
                        <input type="text" id="cpf" name="cpf" required value="<?php echo htmlspecialchars($cliente['cpf']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="endereco">Endereço</label>
                        <textarea id="endereco" name="endereco" rows="3"><?php echo htmlspecialchars($cliente['endereco']); ?></textarea>
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
