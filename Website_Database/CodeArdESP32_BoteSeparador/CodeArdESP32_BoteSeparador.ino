// Probando Acceso a Base de Datos 
// Incluyendo Librerias
#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino_JSON.h>
#include <HX711_ADC.h>
#include <Preferences.h>

// Declarando
#define LED_ESP32 2

//////////////Sensores de Peso//////////////////////////////////////////////////////////////
// Pines de conexión para las dos celdas de carga
#define DOUT1 21  // Pin de datos del HX711 para la pesa 1
#define CLK1  22  // Pin de reloj del HX711 para la pesa 1
#define DOUT2 19  // Pin de datos del HX711 para la pesa 2
#define CLK2  18  // Pin de reloj del HX711 para la pesa 2

HX711_ADC scale1(DOUT1, CLK1);
HX711_ADC scale2(DOUT2, CLK2);
Preferences preferences;

// Variables de calibración
float calibration_factor1=2280.0; // Factor de calibración para pesa 1
float calibration_factor2=2280.0; // Factor de calibración para pesa 2
const char *calibration_key1 = "cal_factor1";
const char *calibration_key2 = "cal_factor2";

// Tiempo entre lecturas
const unsigned long measure_interval = 10 * 1000; // 10 minutos en milisegundos
unsigned long last_measurement = 0;
/////////////////////////////////////////////////////////////////////////////////////////////

// SSID and Contraseña del Router
//Internet Abajo: INFINITUM08A6    NeNtNhUA6y
const char* ssid = "INFINITUM08A6";
const char* password = "NeNtNhUA6y";
const char* ID_BS= "123";
int verif_BoteID=0;

// Variables de petición de datos para HTTP POST
String postData = ""; // Variables enviadas para HTTP POST datos de petición
String payload = "";  // Variable para respuesta recibida del HTTP POST

// Variables for DHT11 sensor data.
float send_RO; //Residuo Orgánico
float send_RI; //Residuo Inorgánico
String send_Estado_Sensores="";
String send_Bote_ID="";

void control_BoteID() {
  Serial.println();
  Serial.println("Inicio de control_BoteID()");
  JSONVar myObject = JSON.parse(payload);

  // JSON.typeof(jsonVar) se usa para obtener el tipo de la variable recibida
  if (JSON.typeof(myObject) == "undefined") {
    Serial.println("Parsing input failed!");
    Serial.println("---------------");
    return;
  }

  if (myObject.hasOwnProperty("Bote_ID")) {
    Serial.print("myObject[\"Bote_ID\"] = ");
    Serial.println(myObject["Bote_ID"]);
  }

  if(strcmp(myObject["Bote_ID"], ID_BS) == 0){
    verif_BoteID=1;
  }else{
    verif_BoteID=0;
  }

  send_Bote_ID=String(myObject["Bote_ID"]);

  Serial.println(verif_BoteID);
}


void calibrateSensor(HX711_ADC &scale, float &cal_factor, const char *sensor_name) {
  Serial.print("Iniciando calibración de ");
  Serial.println(sensor_name);
  delay(2000);

  // Paso 1: Tarar el sensor
  Serial.println("Quitando cualquier peso. Espera...");
  scale.tare();
  delay(2000);

  // Paso 2: Colocar un peso conocido
  Serial.println("Coloca un peso conocido sobre el sensor y escribe el valor del peso en kg:");
  while (!Serial.available()) {
    delay(100);
  }

  // Leer el peso conocido del usuario
  float known_weight = Serial.parseFloat();
  delay(5000);

  // Calcular el factor de calibración
  scale.refreshDataSet(); // Actualizar datos
  float raw_value = scale.getData();
  cal_factor = raw_value / known_weight;

  // Configurar el nuevo factor de calibración
  scale.setCalFactor(cal_factor);

  Serial.println("Calibración completa:");
  Serial.print("Nuevo factor de calibración para ");
  Serial.print(sensor_name);
  Serial.print(": ");
  Serial.println(cal_factor, 2);

  // Confirmar al usuario
  Serial.println("Reinicia el ESP32 para guardar el factor de calibración.");
}

void IDinvalido(){

    HTTPClient http;  // Declarar objeto clase HTTPClient
    int httpCode;     // Variables para código de regreso de HTTP

    postData = "id=esp32_01";
    postData += "&Peso_de_Residuo_Organico=" + String(0.00); //Anidar Peso de Residuo Organico
    postData += "&Peso_de_Residuo_Inorganico=" + String(0.00); //Anida Peso de Residuo Inorganico
    postData += "&Estado_Sensores=" + String("FAILED"); //Anida Peso de Residuo Inorganico
    postData += "&Bote_ID=" + String(send_Bote_ID); //Anida Peso de Residuo Inorganico

    payload = "";

  Serial.println("actualizarTablas.php");
    // Example : http.begin("http://192.168.0.0/ESP32_MySQL_Database/Test/updateDHT11data.php");
    http.begin("http://192.168.1.83/ESP32_MySQL_Database/BoteSeparadorSitioWeb/actualizarTablas.php");  // Especificar dirección a la que se envía petición
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");  // Especificar content-type header
   
    httpCode = http.POST(postData); //--> Send the request
    payload = http.getString();  //--> Get the response payload
  
    Serial.print("httpCode : ");
    Serial.println(httpCode); //--> Imprimir código HTTP de regreso de la página
    Serial.print("payload  : ");
    Serial.println(payload);  //--> Print request response payload
    
    http.end();  //Cerrar conexión
    Serial.println("---------------");
    delay(5000);
}

void setup() {

  Serial.begin(115200); //--> Inicializar comunicación serial con PC

  WiFi.mode(WIFI_STA);  // Activar modo Estación para actuar como cliente 
  WiFi.begin(ssid,password); //Comenzar conexión a Router
  
  Serial.println();
  Serial.print("Connecting");

  // Proceso para conexión el WiFi del ESP32 al del Router
  // Si no se conecta en 20 segundos se detiene conexión y reinicia ESP32
  int Time_Out = 20; 
  Time_Out = Time_Out * 2;

  while (WiFi.status() != WL_CONNECTED) { //Obtener status de conectividad y solo seguir en el ciclo si no se ha conectado
    // Encender luz de ESP32 solo mientras se conecta a WiFi
    digitalWrite(LED_ESP32, HIGH);
    delay(250);
    digitalWrite(LED_ESP32, LOW);
    delay(250);

    // Conteo descendente del proceso de conexión usando los 20 segundos
    if(Time_Out > 0){ 
      Time_Out--;
    }

    // Si se llega a conteo y no ha salido del ciclo --> No se ha conectado --> Procede a Reiniciar ESP32
    if(Time_Out == 0) {
      delay(1000);
      ESP.restart();
    }

  preferences.begin("hx711", false);
  //Iniciarlizar celdas de carga
  Serial.println("Iniciando sensores de peso");
  scale1.begin();
  scale2.begin();

  delay(2000); //Esperar estabilizacion inicial

  scale1.start(5000); // Tiempo para estabilizarse en ms
  scale2.start(5000);

  if (scale1.getTareTimeoutFlag() || scale2.getTareTimeoutFlag()) {
    Serial.println("Error: Timeout durante la estabilización.");
  } else {
    Serial.println("HX711 listo");
  }

  scale1.setCalFactor(calibration_factor1);
  scale2.setCalFactor(calibration_factor2);

  scale1.tare(); // Tare inicial para peso 1
  scale2.tare(); // Tare inicial para peso 2
  Serial.println("Tare aplicado. Sensores listos para medir");
  }
  
  digitalWrite(LED_ESP32, LOW); // Se logra establecer conexión con WiFi --> Mantener LED de ESP32 apagado
  
  // Se muestra Direccion IP que se visitará
  Serial.println();
  Serial.print("Se ha logrado conectar a: ");
  Serial.println(ssid);
  Serial.print("Dirección IP: ");
  Serial.println(WiFi.localIP());
 
  delay(2000);
}

void loop() { //Codigo para correr sensores y enviar informacion en formato JSON a sitio HTTP

  // Revisar status de WiFi para que solo se envie información cuando haya conectividad
  if(WiFi.status()== WL_CONNECTED) {
    HTTPClient http;  // Declarar objeto clase HTTPClient
    int httpCode;     // Variables para código de regreso de HTTP

// Proceso de obtener dato del ID del Bote Separador para identificacion y dar permiso
postData = "id=esp32_01";
payload="";

    digitalWrite(LED_ESP32, HIGH);
    Serial.println();
    Serial.println("Inicializa obtenerDatos.php");

    http.begin("http://192.168.1.83/ESP32_MySQL_Database/BoteSeparadorSitioWeb/obtenerDatos.php"); 
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
   
    httpCode = http.POST(postData); // Enviar Peticion a Sitio Web
    payload = http.getString();     // Respuesta Obtenida
  
    Serial.print("httpCode : ");
    Serial.println(httpCode); // Imprimir codigo HTTP de regreso
    Serial.print("payload  : ");
    Serial.println(payload);  // Imprimir respuesta obtenida
    
    http.end();  // Finaliza Conexion 
    Serial.println("---------------");
    digitalWrite(LED_ESP32, LOW);
    //........................................ 

    // Calls the control_LEDs() subroutine.
    control_BoteID();
    
    delay(1000);

  if(verif_BoteID==1){ //Solo medira y enviara peso de sensores a sitio web Si coincide la ID instalada en
        //en el ESP con la que se escriba en el Sitio Web

//Detectar si se desea calibracion de sensores de peso
 if (Serial.available() > 0) {
    char input = Serial.read();
    if (input == '1') { // Calibrar el sensor 1
      calibrateSensor(scale1, calibration_factor1, "Sensor 1");
    } else if (input == '2') { // Calibrar el sensor 2
      calibrateSensor(scale2, calibration_factor2, "Sensor 2");
    }
  }

  // Leer pesos
  scale1.update();
  scale2.update();

  send_RI = scale1.getData() / 3; //Envio de RSU per capita final (con separacion)
  send_RO = scale2.getData(); //Envio de peso organico


// Check if any reads failed.
  if (isnan(send_RO) || isnan(send_RI)) {
    Serial.println("Fallo deteccion del peso!");
    send_RO = 0.00;
    send_RI = 0.00;
    send_Estado_Sensores = "FAILED";
  } else {
    send_Estado_Sensores = "SUCCESS";
  }
  
  Serial.printf("Peso Organico : %.2f kg\n", send_RO);
  Serial.printf("Peso Inorganico : %.2f kg\n", send_RI);
  Serial.printf("Estado de Lectura de Sensores : %s\n", send_Estado_Sensores);
  Serial.println("-------------");

  delay(1000);


    // Proceso de enviar Datos de Sensores de peso a base de datos

    //Datos a postear en Pagina
    postData = "id=esp32_01";
    postData += "&Peso_de_Residuo_Organico=" + String(send_RO); //Anidar Peso de Residuo Organico
    postData += "&Peso_de_Residuo_Inorganico=" + String(send_RI); //Anida Peso de Residuo Inorganico
    postData += "&Estado_Sensores=" + String(send_Estado_Sensores); //Anida Peso de Residuo Inorganico
    postData += "&Bote_ID=" + String(send_Bote_ID); //Anida Peso de Residuo Inorganico
    
    payload = "";
  
    digitalWrite(LED_ESP32, HIGH);
    Serial.println();
    Serial.println("actualizarTablas.php");
    // Example : http.begin("http://192.168.0.0/ESP32_MySQL_Database/Test/updateDHT11data.php");
    http.begin("http://192.168.1.83/ESP32_MySQL_Database/BoteSeparadorSitioWeb/actualizarTablas.php");  // Especificar dirección a la que se envía petición
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");  // Especificar content-type header
   
    httpCode = http.POST(postData); //--> Send the request
    payload = http.getString();  //--> Get the response payload
  
    Serial.print("httpCode : ");
    Serial.println(httpCode); //--> Imprimir código HTTP de regreso de la página
    Serial.print("payload  : ");
    Serial.println(payload);  //--> Print request response payload
    
    http.end();  //Cerrar conexión
    Serial.println("---------------");
    digitalWrite(LED_ESP32, LOW);
    //........................................ 
    
    delay(10000);
  }else{
    Serial.println("Verificacion de ID Invalida");
    Serial.println("Ingrese una ID válida para el Bote seleccionado.");
    IDinvalido();
    send_RI = 0.00;
    send_RO = 0.00;
  }
  } 
}
