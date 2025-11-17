<?php
// teste_tabelas.php
$host = 'localhost';
$dbname = 'locadora';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Listar todas as tabelas do banco
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Tabelas no banco 'locadora':</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . $table . "</li>";
    }
    echo "</ul>";
    
    // Verificar estrutura da tabela de clientes (se existir)
    foreach ($tables as $table) {
        if (strtolower($table) === 'cliente' || strtolower($table) === 'clientes') {
            echo "<h3>Estrutura da tabela: $table</h3>";
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<table border='1'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            foreach ($columns as $column) {
                echo "<tr>";
                echo "<td>" . $column['Field'] . "</td>";
                echo "<td>" . $column['Type'] . "</td>";
                echo "<td>" . $column['Null'] . "</td>";
                echo "<td>" . $column['Key'] . "</td>";
                echo "<td>" . $column['Default'] . "</td>";
                echo "<td>" . $column['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>