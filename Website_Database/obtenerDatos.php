<?php //Obtener datos
  include 'database.php';
  
  // Condicion para revisar que POST no este vacio
  if (!empty($_POST)) {
    $id = $_POST['id'];
    
    $myObj = (object)array();
    
    $pdo = Database::connect();

    //update solo reemplaza un valor con otro, asi que solo es para una sola fila
    $sql = 'SELECT * FROM esp32_table_actualizarpesos WHERE id="' . $id . '"';
    foreach ($pdo->query($sql) as $row) {
      $fecha = date_create($row['fecha']);
      $dateFormat = date_format($fecha,"d-m-Y");
      
      $myObj->id = $row['id'];
      $myObj->Peso_de_Residuo_Organico = $row['Peso_de_Residuo_Organico'];
      $myObj->Peso_de_Residuo_Inorganico = $row['Peso_de_Residuo_Inorganico'];
      $myObj->Estado_Sensores = $row['Estado_Sensores'];
      $myObj->Bote_ID = $row['Bote_ID'];
      $myObj->ls_fecha = $dateFormat;
      
      $myJSON = json_encode($myObj);
      
      echo $myJSON;
    }
    Database::disconnect();
  }
?>
