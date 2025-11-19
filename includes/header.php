<?php
// header.php - inclui conexão e helpers e renderiza a parte superior do HTML
if (!isset($cssPath)) {
    $cssPath = 'css/style.css';
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/helpers.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? esc($pageTitle) : 'Sistema Locadora'; ?></title>
    <link rel="stylesheet" href="<?php echo esc($cssPath); ?>">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema de Gerenciamento - Locadora</h1>
        </header>

        <nav>
            <ul>
                <li><a href="<?php echo isset($baseUrl) ? esc($baseUrl) . 'clientes/listar.php' : 'clientes/listar.php'; ?>">Gerenciar Clientes</a></li>
                <li><a href="<?php echo isset($baseUrl) ? esc($baseUrl) . 'filmes/listar.php' : 'filmes/listar.php'; ?>">Gerenciar Filmes</a></li>
                <li><a href="<?php echo isset($baseUrl) ? esc($baseUrl) . 'locacoes/listar.php' : 'locacoes/listar.php'; ?>">Gerenciar Locações</a></li>
            </ul>
        </nav>

        <main>
