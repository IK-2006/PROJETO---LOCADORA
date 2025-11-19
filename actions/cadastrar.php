<?php
// actions/cadastrar.php - handler genérico para criar/atualizar cliente, filme e locacao
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

$entity = isset($_REQUEST['entity']) ? $_REQUEST['entity'] : null;
if (!$entity) {
    redirect('../index.php');
}

$entity = strtolower($entity);

// mapping de entidades para tabelas e colunas
$map = [
    'cliente' => [
        'table' => TABLE_CLIENTE,
        'id' => 'ID_CLIENTE',
        'fields' => ['NOME','CPF','TELEFONE','EMAIL']
    ],
    'filme' => [
        'table' => TABLE_FILME,
        'id' => 'ID_FILME',
        'fields' => ['TITULO','ANO','ID_CATEGORIA']
    ],
    'locacao' => [
        'table' => TABLE_LOCACAO,
        'id' => 'ID_LOCACAO',
        'fields' => ['DATA_LOCACAO','DATA_DEVOLUCAO','MULTA','ID_FILME','ID_CLIENTE']
    ],
];

if (!isset($map[$entity])) {
    redirect('../index.php');
}

$meta = $map[$entity];
$table = $meta['table'];
$idCol = $meta['id'];
$fields = $meta['fields'];

$erro = '';

// coletar dados do POST de forma segura
$data = [];
foreach ($fields as $f) {
    $key = strtolower($f);
    // manter como vindo do form (campos podem ter nomes diferentes em HTML)
    $data[$f] = isset($_POST[$key]) ? $_POST[$key] : (isset($_POST[$f]) ? $_POST[$f] : null);
}

$isUpdate = !empty($_POST['id']);

// validações básicas por entidade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($entity === 'cliente') {
        if (empty($data['NOME']) || empty($data['CPF']) || empty($data['TELEFONE']) || empty($data['EMAIL'])) {
            $erro = 'Todos os campos são obrigatórios!';
        } elseif (!filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
            $erro = 'Email inválido!';
        }
    } elseif ($entity === 'filme') {
        if (empty($data['TITULO']) || empty($data['ANO'])) {
            $erro = 'Título e Ano são obrigatórios!';
        }
    } elseif ($entity === 'locacao') {
        if (empty($data['DATA_LOCACAO']) || empty($data['DATA_DEVOLUCAO']) || empty($data['ID_FILME']) || empty($data['ID_CLIENTE'])) {
            $erro = 'Todos os campos são obrigatórios!';
        } elseif ($data['DATA_DEVOLUCAO'] < $data['DATA_LOCACAO']) {
            $erro = 'Data de devolução não pode ser anterior à data de locação!';
        }
    }

    if ($erro !== '') {
        // redirecionar de volta para o formulário com erro (espera-se que o formulário esteja na pasta correta)
        redirect('../' . $entity . 's/cadastrar.php?erro=' . urlencode($erro));
    }

    try {
        if ($isUpdate) {
            // UPDATE genérico
            $id = $_POST['id'];
            $sets = [];
            $values = [];
            foreach ($fields as $f) {
                $sets[] = "$f = ?";
                $values[] = $data[$f];
            }
            $values[] = $id;
            $sql = "UPDATE $table SET " . implode(', ', $sets) . " WHERE $idCol = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            redirect('../' . $entity . 's/listar.php?sucesso=' . urlencode(ucfirst($entity) . ' atualizado com sucesso!'));
        } else {
            // INSERT genérico
            $novo_id = getNextId($pdo, $table, $idCol);
            $cols = array_merge([$idCol], $fields);
            $placeholders = array_fill(0, count($cols), '?');
            $values = [$novo_id];
            foreach ($fields as $f) {
                $values[] = $data[$f];
            }
            $sql = "INSERT INTO $table (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            redirect('../' . $entity . 's/listar.php?sucesso=' . urlencode(ucfirst($entity) . ' cadastrado com sucesso!'));
        }
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        redirect('../' . $entity . 's/listar.php?erro=' . urlencode('Erro: ' . $msg));
    }
}

// Se não POST, redireciona para a listagem
redirect('../' . $entity . 's/listar.php');
