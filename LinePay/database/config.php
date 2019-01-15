<?php

/**
 * Configuration for database connection
 *
 */
	class Config{

		private $arr_config;
		private $host       = "localhost";
		private $username   = "taiwan";
		private $password   = "taiwan123";
		private $dbname     = "funb";
		private $options    = array(
                				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
         					);

		function __construct(){
			$dsn = "mysql:host=$this->host;dbname=$this->dbname";
			$this->arr_config = array(
									'host' => $this->host,
									'username' => $this->username,
									'password' => $this->password,
									'dbname' => $this->dbname,
									'dsn' => $dsn,
									'options' => $this->options
								);
		}

		function getArrConfig(){
			return $this->arr_config;
		}

	}
?>