<?php
  require 'database.php';
  
  // Condición para revisar que el valor POST no esté vacío
  if (!empty($_POST)) {

    $id = $_POST['id'];
    $Peso_de_Residuo_Organico = $_POST['Peso_de_Residuo_Organico'];
    $Peso_de_Residuo_Inorganico = $_POST['Peso_de_Residuo_Inorganico'];
    $Estado_Sensores = $_POST['Estado_Sensores'];
    $Bote_ID = $_POST['Bote_ID'];
    
    // Obtener la fecha y hora actual
    date_default_timezone_set("America/Tijuana"); 
    $fecha = date("Y-m-d");
    $hora = date("H:i");

    
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar la última fecha registrada para el ID dado
        $sql = "SELECT fecha FROM esp32_table_actualizarpesos WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $result = $q->fetch(PDO::FETCH_ASSOC);

        // Solo actualizar si la fecha es diferente
            // Actualizar datos en la tabla
            $sql = "UPDATE esp32_table_actualizarpesos SET Peso_de_Residuo_Organico = ?, Peso_de_Residuo_Inorganico = ?, Estado_Sensores = ?, fecha = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($Peso_de_Residuo_Organico, $Peso_de_Residuo_Inorganico, $Estado_Sensores, $fecha, $id));

            // Insertar un registro en la tabla de registro de pesos
            $id_key;
            $board = $_POST['id'];
            $found_empty = false;

            
            while ($found_empty == false) {
                $id_key = generate_string_id(10);
                $sql = 'SELECT * FROM esp32_table_registropesos WHERE id = ?';
                $q = $pdo->prepare($sql);
                $q->execute(array($id_key));
                
                if (!$data = $q->fetch()) {
                    $found_empty = true;
                }
            }
    // Verificar si es medianoche y es otro dia

            if (!$result || $result['fecha'] !== $fecha) {
            if ($hora === "00:00") {
            // Insertar los nuevos datos
            $sql = "INSERT INTO esp32_table_registropesos (id, board, Peso_de_Residuo_Organico, Peso_de_Residuo_Inorganico, Estado_Sensores, Bote_ID, fecha) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($id_key, $board, $Peso_de_Residuo_Organico, $Peso_de_Residuo_Inorganico, $Estado_Sensores, $Bote_ID, $fecha));
        }

        Database::disconnect();
    }
  }
   
  // Creación de una ID basada en caracteres
  function generate_string_id($strength = 16) {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($permitted_chars);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
      $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
      $random_string .= $random_character;
    }
    return $random_string;
  } 
?>