<?php
// index.php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Locadora</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Sistema de Gerenciamento - Locadora</h1>
        </header>


        <nav>
            <ul>
                <li><a href="clientes/listar.php">Gerenciar Clientes</a></li>
                <li><a href="filmes/listar.php">Gerenciar Filmes</a></li> <!-- NOVO -->
                <li><a href="locacoes/listar.php">Gerenciar Locações</a></li>
            </ul>
        </nav>

        <main>
            <h2>Bem-vindo ao Sistema</h2>
            <p>Selecione uma das opções acima para começar.</p>
        </main>
    </div>
</body>

</html>