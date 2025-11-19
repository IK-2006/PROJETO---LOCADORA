<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

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
        $novo_id = getNextId($pdo, TABLE_CLIENTE, 'ID_CLIENTE');

        try {
            $stmt = $pdo->prepare("INSERT INTO " . TABLE_CLIENTE . " (ID_CLIENTE, NOME, CPF, TELEFONE, EMAIL) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$novo_id, $nome, $cpf, $telefone, $email]);
            redirect('listar.php?sucesso=' . urlencode('Cliente cadastrado com sucesso!'));
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro = "Erro: CPF ou Email já cadastrado!";
            } else {
                $erro = "Erro ao cadastrar cliente: " . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Cadastrar Cliente';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo esc($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="../actions/cadastrar.php?entity=cliente">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? esc($_POST['nome']) : ''; ?>" required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo isset($_POST['cpf']) ? esc($_POST['cpf']) : ''; ?>" required maxlength="11" pattern="[0-9]{11}" title="Digite apenas números (11 dígitos)">
            </div>
            
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo isset($_POST['telefone']) ? esc($_POST['telefone']) : ''; ?>" required maxlength="20">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? esc($_POST['email']) : ''; ?>" required maxlength="100">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>

<?php include __DIR__ . '/../includes/footer.php'; ?>