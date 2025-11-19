<?php
// helpers.php - funções utilitárias compartilhadas
if (!function_exists('esc')) {
    function esc($value)
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('flash')) {
    function flash($key)
    {
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            $type = ($key === 'sucesso') ? 'success' : (($key === 'erro') ? 'error' : 'info');
            return '<div class="alert ' . $type . '">' . esc($_GET[$key]) . '</div>';
        }
        return '';
    }
}

if (!function_exists('getNextId')) {
    function getNextId(PDO $pdo, $table, $idColumn)
    {
        $sql = "SELECT COALESCE(MAX($idColumn), 0) + 1 as novo_id FROM $table";
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['novo_id'] : 1;
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}

?>
