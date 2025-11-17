<?php
// config.php
$host = 'localhost';
$dbname = 'locadora';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Verificar nomes reais das tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Mapear nomes das tabelas
    $table_names = [];
    foreach ($tables as $table) {
        $table_names[strtolower($table)] = $table;
    }
    
    // Definir constantes com os nomes corretos das tabelas
    define('TABLE_CLIENTE', isset($table_names['cliente']) ? $table_names['cliente'] : 'CLIENTE');
    define('TABLE_FILME', isset($table_names['filme']) ? $table_names['filme'] : 'FILME');
    define('TABLE_LOCACAO', isset($table_names['locacao']) ? $table_names['locacao'] : 'LOCACAO');
    define('TABLE_CATEGORIA', isset($table_names['categoria']) ? $table_names['categoria'] : 'CATEGORIA');
    
} catch(PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

// Função para formatar data
function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

// Função para formatar moeda
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
?>