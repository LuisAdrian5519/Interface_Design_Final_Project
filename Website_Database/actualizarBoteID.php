<?php //Actualizacion Bote ID
  require 'database.php';
  
  // Condicion para revisar que POST no este vacio
  if (!empty($_POST)) {
    $id = $_POST['id'];
    $Bote_ID = $_POST['Bote_ID']; //Nuevo valor de Bote_ID
    
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE esp32_table_actualizarpesos SET Bote_ID = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($Bote_ID, $id));
    Database::disconnect();

    //Redirigir a la misma pagina despues de proesar
    header("Location: home.php");
    exit; // Termina la ejecución del script
  }
?>
