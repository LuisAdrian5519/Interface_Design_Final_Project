<!DOCTYPE HTML>
<html>
  <head>
    <title>ESP32 WITH MYSQL DATABASE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="icon" href="data:,">
    <style>
      html {font-family: Arial; display: inline-block; text-align: center;}
      p {font-size: 1.2rem;}
      h4 {font-size: 1rem;}
      h5 {font-size: 1rem;}

      body {margin: 0;}
      .topnav {overflow: hidden; background-color: #000000; color: white; font-size: 1.2rem;}
      .content {padding: 5px; }
      .card {background-color: white; box-shadow: 0px 0px 10px 1px rgba(140,140,140,.5); border: 1px solid #0c6980; border-radius: 15px;}
      .card.header {background-color: #000000; color: white; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-top-right-radius: 12px; border-top-left-radius: 12px;}
      .cards {max-width: 700px; margin: 0 auto; display: grid; grid-gap: 2rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));}
      .reading {font-size: 1.5rem;}
      .packet {color: #bebebe;}
      .temperatureColor {color: #ff0000;}
      .humidityColor {color: #008f39;}
      .statusreadColor {color: #702963; font-size:12px;}
      .LEDColor {color: #183153;}
      
      /* ----------------------------------- Toggle Switch */
      .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
      }

      .switch input {display:none;}

      .sliderTS {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #D3D3D3;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px;
      }

      .sliderTS:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: #f7f7f7;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50%;
      }

      input:checked + .sliderTS {
        background-color: #00878F;
      }

      input:focus + .sliderTS {
        box-shadow: 0 0 1px #2196F3;
      }

      input:checked + .sliderTS:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
      }

      .sliderTS:after {
        content:'OFF';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 70%;
        font-size: 10px;
        font-family: Verdana, sans-serif;
      }

      input:checked + .sliderTS:after {  
        left: 25%;
        content:'ON';
      }

      input:disabled + .sliderTS {  
        opacity: 0.3;
        cursor: not-allowed;
        pointer-events: none;
      }

      
      /* ----------------------------------- */
    </style>
  </head>
  
  <body>
    <div class="topnav">
      <div id="image-and-title">
        <div id="image-container">
          <img src="Logo_O.png" alt="Descripción de la imagen" style="width: 75px; height: auto;">
        </div>
          <h1>¡Bienvenido, Cliente!</h1>
        </div>
    </div>
    
    <br>
    
    <!-- __ DISPLAYS MONITORING AND CONTROLLING ____________________________________________________________________________________________ -->
    <div class="content">
      <div class="cards">
        
        <!-- == MONITORING ======================================================================================== -->
        <div class="card">
          <div class="card header">
            <h3 style="font-size: 1rem;">Estadística del R.S.U.</h3>
          </div>
          
          <!-- Displays the humidity and temperature values received from ESP32. *** -->
          <h4 class="temperatureColor"> R.S.U</h4>
          <p class="temperatureColor"><span class="reading"><span id="Peso_de_Residuo_Inorganico"></span> Kg </span></p>
          <h4 class="humidityColor"> Residuo Orgánico</h4>
          <p class="humidityColor"><span class="reading"><span id="Peso_de_Residuo_Organico"></span> Kg </span></p>
          <!-- *********************************************************************** -->
          
          <button 
              style="display: inline-block; width: 90%; max-width: 200px; padding: 10px; border: none; background-color: #000000; color: white; font-size: 16px; border-radius: 5px; cursor: pointer; text-align: center;"
              onclick="window.location.href='recordtable.php';"              onmouseover="this.style.backgroundColor='#a4a4a4';" 
              onmouseout="this.style.backgroundColor='#000000';">
              Tabla de registros
          </button>

          <p class="statusreadColor"><span> Sensor de peso (Status):  </span><span id="Estado_Sensores"></span></p>
        </div>
        <!-- ======================================================================================================= -->
        
        <!-- == CONTROLLING ======================================================================================== -->
        <div class="card">
          <div class="card header">
            <h3 style="font-size: 1rem;">Vincular dispositivo</h3>
          </div>
          
          <h5 class="LEDColor"></i> Ingresa el código numérico del Bote Separador</h5>
    <form method="POST" action="actualizarBoteID.php">
    <!-- Campo oculto para el ID del dispositivo (puede ser dinámico dependiendo del dispositivo) -->
    <input type="hidden" name="id" value="esp32_01" /> <!-- Cambiar "esp32_01" por el id real -->
    
    <input type="text" name="Bote_ID" placeholder="Ingresa el código del Bote"  required />
    <br><br> <br>

        <button 
            type="submit" 
            style="display: inline-block; width: 90%; max-width: 200px; padding: 10px; border: none; background-color: #000000; color: white; font-size: 16px; border-radius: 5px; cursor: pointer; text-align: center;" 
            onmouseover="this.style.backgroundColor='#a4a4a4';" 
            onmouseout="this.style.backgroundColor='#000000';">
            Enviar Bote ID
        </button>
      </form>   
      </div>
    </div>
    
    <br>
    
    <div class="content">
      <div class="cards">
        <div class="card header" style="border-radius: 15px; padding: 20px; margin: 20px 0;">
            <button 
              style="display: inline-block; width: 90%; max-width: 300px; padding: 10px; border: none; background-color: #000000; color: white; font-size: 16px; border-radius: 5px; cursor: pointer; text-align: center;"
              onclick="window.location.href='Organizations.php';"              onmouseover="this.style.backgroundColor='#a4a4a4';" 
              onmouseout="this.style.backgroundColor='#000000';">
              Oferta a organización
            </button>
        </div>
      </div>
    </div>

    <script>
      Get_Data("esp32_01");
      
      setInterval(myTimer, 5000);
      
      //------------------------------------------------------------
      function myTimer() {
        Get_Data("esp32_01");
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function Get_Data(id) {
				if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp = new XMLHttpRequest();
        } else {
          // code for IE6, IE5
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            const myObj = JSON.parse(this.responseText);
            if (myObj.id == "esp32_01") {
              document.getElementById("Peso_de_Residuo_Organico").innerHTML = myObj.Peso_de_Residuo_Organico;
              document.getElementById("Peso_de_Residuo_Inorganico").innerHTML = myObj.Peso_de_Residuo_Inorganico;
              document.getElementById("Estado_Sensores").innerHTML = myObj.Estado_Sensores;
              document.getElementById("Fecha").innerHTML = "fecha : " + myObj.fecha + " (dd-mm-yyyy)";
            }
          }
        };
        xmlhttp.open("POST","obtenerDatos.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("id="+id);
			}
      //------------------------------------------------------------
          
    </script>

  </body>
</html>