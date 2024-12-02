<?php
	class Database {
		private static $dbName = 'esp32_pesosregistradosdb'; // Base de datos en Servidor Local
		private static $dbHost = 'localhost'; // Nombre del Servidor Local
		private static $dbUsername = 'root'; // Nombre de Usuario de Servidor Local
		private static $dbUserPassword = 'admin123'; // Contraseña del Servidor Local
		 
		private static $cont  = null;
		 
		public function __construct() {
			die('Init function is not allowed');
		}
		 
		public static function connect() {
      // One connection through whole application
      if ( null == self::$cont ) {     
        try {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword); 
        } catch(PDOException $e) {
          die($e->getMessage()); 
        }
      }
      return self::$cont;
		}
		 
		public static function disconnect() {
			self::$cont = null;
		}
	}
?>
