<!DOCTYPE HTML>
<html>
  <head>
    <title>ESP32 WITH MYSQL DATABASE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      html {font-family: Arial; display: inline-block; text-align: center;}
      p {font-size: 1.2rem;}
      h4 {font-size: 0.8rem;}
      body {margin: 0;}
      /* ----------------------------------- TOPNAV STYLE */
      .topnav {overflow: hidden; background-color: #000000; color: white; font-size: 1.2rem;}
      /* ----------------------------------- */
      
      /* ----------------------------------- TABLE STYLE */
      .styled-table {
        border-collapse: collapse;
        margin-left: auto; 
        margin-right: auto;
        font-size: 0.9em;
        font-family: sans-serif;
        min-width: 400px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        border-radius: 0.5em;
        overflow: hidden;
        width: 90%;
      }

      .styled-table thead tr {
        background-color: #000000;
        color: #ffffff;
        text-align: left;
      }

      .styled-table th {
        padding: 12px 15px;
        text-align: left;
      }

      .styled-table td {
        padding: 12px 15px;
        text-align: left;
      }

      .styled-table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
      }

      .styled-table tbody tr.active-row {
        font-weight: bold;
        color: #009879;
      }

      .bdr {
        border-right: 1px solid #e3e3e3;
        border-left: 1px solid #e3e3e3;
      }
      
      td:hover {background-color: rgba(12, 105, 128, 0.21);}
      tr:hover {background-color: rgba(12, 105, 128, 0.15);}
      .styled-table tbody tr:nth-of-type(even):hover {background-color: rgba(12, 105, 128, 0.15);}
      /* ----------------------------------- */
      
      /* ----------------------------------- BUTTON STYLE */
      .btn-group .button {
        background-color: #0c6980; /* Green */
        border: 1px solid #e3e3e3;
        color: white;
        padding: 5px 8px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        cursor: pointer;
        float: center;
      }

      .btn-group .button:not(:last-child) {
        border-right: none; /* Prevent double borders */
      }

      .btn-group .button:hover {
        background-color: #094c5d;
      }

      .btn-group .button:active {
        background-color: #0c6980;
        transform: translateY(1px);
      }

      .btn-group .button:disabled,
      .button.disabled{
        color:#fff;
        background-color: #a0a0a0; 
        cursor: not-allowed;
        pointer-events:none;
      }
      /* ----------------------------------- */
    </style>
  </head>
  
  <body>
    <div class="topnav">
      <h3>Oferta a organización</h3>
    </div>

    <!-- Botón que redirige a home.php -->
    <div class="btn-group" style="text-align: left; margin-top: 15px; margin-left: 15px;">
      <a href="home.php" class="button" style="background-color: black; color: white; font-weight: bold;">Home</a>
    </div>
    
    <br>
    
    <h3 style="color: #333;">Catálogo de Organizaciones</h3>
    
    <table class="styled-table" id= "table_id">
      <thead>
        <tr>
          <th>#</th>
          <th>NOMBRE</th>
          <th>Dirección</th>
          <th>Teléfono</th>
        </tr>
      </thead>
      <tbody id="tbody_table_record">
      <tbody id="tbody_table_record">
        <tr>
          <td>1</td>
          <td>Ciclo</td>
          <td>Lote Sin Número Mariano Abasolo, 21600</td>
          <td>686 189 0182</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Compostate Bien</td>
          <td>MM5C+7G, 21606 Mexicali, B.C.</td>
          <td>686 198 0300</td>
        </tr>
        <tr>
          <td>3</td>
          <td>Composta Mexicali</td>
          <td>Granjas Señoriales lote 10 Mexicali, B.C.</td>
          <td>686 216 6196 </td>
        </tr>
        <tr>
          <td>4</td>
          <td>Fundación Hélice</td>
          <td>Marmoleros Sur #1808 Mexicali, B.C.</td>
          <td>686 218 1120</td>
        </tr>
        <tr>
          <td>5</td>
          <td>Comité Ciudadano de Ecología y Medio Ambiente de Mexicali</td>
          <td>jose maria larroque 325-B Mexicali, B.C.</td>
          <td>-</td>
        </tr>
        <tr>
          <td>6</td>
          <td>Recicladora Autoecology</td>
          <td>Carretera San Felipe, Rivera Campestre, 21387 Mexicali, B.C.</td>
          <td>+52 686 561 6404</td>
        </tr>
        <tr>
          <td>7</td>
          <td>Eco Recolección</td>
          <td>Lago Ontario 140, Xochimilco, 21380 Mexicali, B.C.</td>
          <td>+52 686 582 4332</td>
        </tr>
        <tr>
          <td>8</td>
          <td>GEN Industrial SA de CV Mexicali </td>
          <td>13 de Septiembre 171, Col. 5 de Julio, 21387 Mexicali, B.C.</td>
          <td>+52 686 561 6936</td>
        </tr>
        <tr>
          <td>9</td>
          <td>Vivero San Jose</td>
          <td>Blvd. Adolfo López Mateos #1722, Ex-Ejido Zacatecas, 21090 Mexicali, B.C.</td>
          <td>686 557 3760</td>
        </tr>
        <tr>
          <td>10</td>
          <td>Eden Viveros Garden Center</td>
          <td>Calz. Manuel Gómez Morín 1799, 21259 Mexicali, B.C.</td>
          <td>686 488 7879</td>
        </tr>
        <tr>
          <td>11</td>
          <td>Viveros Rosa Del Desierto</td>
          <td>Av. República de Perú 1100, Sonora, 21210 Mexicali, B.C.</td>
          <td>686 905 1194</td>
        </tr>
        <tr>
          <td>12</td>
          <td>Viveros Ortiz</td>
          <td>Blvd. Lázaro Cárdenas 3258, Mirasol, 21396 Mexicali, B.C.</td>
          <td>686 562 9504</td>
        </tr>
        <tr>
          <td>13</td>
          <td>Viveros Adenium</td>
          <td>Tierra y Libertad 1270, Emiliano Zapata, 21396 Mexicali, B.C.</td>
          <td>686 580 3276</td>
        </tr>
        <tr>
          <td>14</td>
          <td>Viveros Nuevo Mexicali</td>
          <td>Río Casas Grandes 2622, Nuevo Mexicali, 21399 Mexicali, B.C.</td>
          <td>686 562 6337</td>
        </tr>
        <tr>
          <td>15</td>
          <td>Viveros Independencia</td>
          <td>Calz Independencia 2050, Rivera, 21259 Mexicali, B.C.</td>
          <td>686 186 4869</td>
        </tr>
      </tbody>
    </table>
    
    <br>
    
    <div class="btn-group">
      <button class="button" id="btn_prev" onclick="prevPage()">Prev</button>
      <button class="button" id="btn_next" onclick="nextPage()">Next</button>
      <div style="display: inline-block; position:relative; border: 0px solid #e3e3e3; float: center; margin-left: 2px;;">
        <p style="position:relative; font-size: 14px;"> Table : <span id="page"></span></p>
      </div>
      <select name="number_of_rows" id="number_of_rows">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <button class="button" id="btn_apply" onclick="apply_Number_of_Rows()">Apply</button>
    </div>

    <br>
    
    <script>
      //------------------------------------------------------------
      var current_page = 1;
      var records_per_page = 10;
      var l = document.getElementById("table_id").rows.length
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function apply_Number_of_Rows() {
        var x = document.getElementById("number_of_rows").value;
        records_per_page = x;
        changePage(current_page);
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function prevPage() {
        if (current_page > 1) {
            current_page--;
            changePage(current_page);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function nextPage() {
        if (current_page < numPages()) {
            current_page++;
            changePage(current_page);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function changePage(page) {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var listing_table = document.getElementById("table_id");
        var page_span = document.getElementById("page");
       
        // Validate page
        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();

        [...listing_table.getElementsByTagName('tr')].forEach((tr)=>{
            tr.style.display='none'; // reset all to not display
        });
        listing_table.rows[0].style.display = ""; // display the title row

        for (var i = (page-1) * records_per_page + 1; i < (page * records_per_page) + 1; i++) {
          if (listing_table.rows[i]) {
            listing_table.rows[i].style.display = ""
          } else {
            continue;
          }
        }
          
        page_span.innerHTML = page + "/" + numPages() + " (Total Number of Rows = " + (l-1) + ") | Number of Rows : ";
        
        if (page == 0 && numPages() == 0) {
          btn_prev.disabled = true;
          btn_next.disabled = true;
          return;
        }

        if (page == 1) {
          btn_prev.disabled = true;
        } else {
          btn_prev.disabled = false;
        }

        if (page == numPages()) {
          btn_next.disabled = true;
        } else {
          btn_next.disabled = false;
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function numPages() {
        return Math.ceil((l - 1) / records_per_page);
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      window.onload = function() {
        var x = document.getElementById("number_of_rows").value;
        records_per_page = x;
        changePage(current_page);
      };
      //------------------------------------------------------------
    </script>
  </body>
</html>