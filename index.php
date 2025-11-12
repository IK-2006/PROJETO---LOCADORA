<?php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Locadora</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🎬 Sistema de Locadora</h1>
        </header>
        
        <nav class="menu">
            <a href="index.php" class="menu-item active">Início</a>
            <a href="clientes/listar.php" class="menu-item">Clientes</a>
            <a href="locacoes/listar.php" class="menu-item">Locações</a>
            <a href="locacoes/abertas.php" class="menu-item">Locações em Aberto</a>
        </nav>
        
        <main class="dashboard">
            <div class="card">
                <h2>Bem-vindo ao Sistema de Locadora</h2>
                <p>Gerencie clientes e locações de filmes de forma simples e eficiente.</p>
            </div>
            
            <div class="stats">
                <?php
                $conn = getConnection();
                
                // Contar clientes
                $result = $conn->query("SELECT COUNT(*) as total FROM clientes");
                $clientes = $result->fetch_assoc()['total'];
                
                // Contar locações em aberto
                $result = $conn->query("SELECT COUNT(*) as total FROM locacoes WHERE status = 'aberta'");
                $locacoes_abertas = $result->fetch_assoc()['total'];
                
                // Contar filmes disponíveis
                $result = $conn->query("SELECT COUNT(*) as total FROM filmes WHERE disponivel = 1");
                $filmes_disponiveis = $result->fetch_assoc()['total'];
                
                $conn->close();
                ?>
                
                <div class="stat-card">
                    <h3><?php echo $clientes; ?></h3>
                    <p>Clientes Cadastrados</p>
                </div>
                
                <div class="stat-card">
                    <h3><?php echo $locacoes_abertas; ?></h3>
                    <p>Locações em Aberto</p>
                </div>
                
                <div class="stat-card">
                    <h3><?php echo $filmes_disponiveis; ?></h3>
                    <p>Filmes Disponíveis</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
