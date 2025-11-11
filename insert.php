<?php
 include_once("conexao.php");

 if($_POST["tabela"] == 'clientes'){
    cadastraCliente($_POST['codCliente'], $_POST['nomeCliente'], $_POST['emailCliente'], $_POST['telefoneCliente'], $_POST['cpfCliente'], $_POST['enderecoCliente']);
 }

 if($_POST["tabela"] == 'filmes'){
    cadastraFilme($_POST['nomeFilme'], $_POST['generoFilme'], $_POST['anoFilme']);  
 }


# -----------------------------------------------------------------------
 function cadastraCliente($codCliente, $nomeClie, $emailClie, $telefoneClie, $cpfClie, $enderecoClie) {
    $conexao = conectaBD();
    $insertQuery = "INSERT INTO Clientes (codCliente, nome, email, telefone, cpf, endereco) VALUES ('$nomeClie', '$emailClie', '$telefoneClie', '$cpfClie', '$enderecoClie')";
    if(mysqli_query($conexao, $insertQuery)){
        echo "Cliente cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar cliente: " . mysqli_error($conexao);
    }
 }

 function cadastraFilme($nome, $genero, $ano){

}

