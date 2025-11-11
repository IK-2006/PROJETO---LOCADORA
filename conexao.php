 <?php
 function conectaBD(){
   $servername = "localhost";
   $database = "locadora";
   $username = "root";
   $password = "";

   // criar conexao
   $conexao= mysqli_connect($servername, $username, $password, $database);
   if(!$conexao){
     die("Conexão falhou! ".mysqli_connect_error());
   }else{
    //-- echo "Conexão realizada!"; --//
   }
   return ($conexao);
 }// final da Funcao

//-----------------------------//
   function desconectaBD($conexao){
      mysqli_close($conexao);
  }// final da Funcao
?>