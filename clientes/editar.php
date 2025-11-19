<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_GET['id'])) {
    redirect('listar.php');
}

$id = $_GET['id'];
$erro = '';

// Buscar dados do cliente
$stmt = $pdo->prepare("SELECT * FROM " . TABLE_CLIENTE . " WHERE ID_CLIENTE = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    redirect('listar.php?erro=' . urlencode('Cliente não encontrado'));
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
            $stmt = $pdo->prepare("UPDATE " . TABLE_CLIENTE . " SET NOME = ?, CPF = ?, TELEFONE = ?, EMAIL = ? WHERE ID_CLIENTE = ?");
            $stmt->execute([$nome, $cpf, $telefone, $email, $id]);
            redirect('listar.php?sucesso=' . urlencode('Cliente atualizado com sucesso!'));
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro = "Erro: CPF ou Email já cadastrado em outro cliente!";
            } else {
                $erro = "Erro ao atualizar cliente: " . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Editar Cliente';
$cssPath = '../css/style.css';
$baseUrl = '../';
include __DIR__ . '/../includes/header.php';
?>

        <?php if (!empty($erro)): ?>
            <div class="alert error"><?php echo esc($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="../actions/cadastrar.php?entity=cliente">
            <input type="hidden" name="id" value="<?php echo esc($cliente['ID_CLIENTE']); ?>">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? esc($_POST['nome']) : esc($cliente['NOME']); ?>" required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo isset($_POST['cpf']) ? esc($_POST['cpf']) : esc($cliente['CPF']); ?>" required maxlength="11" pattern="[0-9]{11}" title="Digite apenas números (11 dígitos)">
            </div>
            
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo isset($_POST['telefone']) ? esc($_POST['telefone']) : esc($cliente['TELEFONE']); ?>" required maxlength="20">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? esc($_POST['email']) : esc($cliente['EMAIL']); ?>" required maxlength="100">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="listar.php" class="btn">Cancelar</a>
            </div>
        </form>

<?php include __DIR__ . '/../includes/footer.php'; ?>