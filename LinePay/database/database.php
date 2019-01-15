<?php

	require "config.php";
	require "common.php";


	class Database{

		private $connDB;
		private $arr_config;
		private $insertId = -1;
		//插入的ID

		/**
		 * [__construct description]
		 * @return [void]
		 * @param [string] $argStrHostname
		 * @param [string] $argStrDatabase
		 * @param [string] $argStrUsername
		 * @param [string] $argStrPassword
		 * @param [bool] $argDefSetting
		 */
		function __construct($argStrHostname=NULL,$argStrDatabase=NULL,$argStrUsername=NULL,$argStrPassword=NULL,$argDefSetting=true)
		{
			$config = new Config();
			$this->arr_config = $config->getArrConfig();
			try{
				$this->connDB = new PDO($this->arr_config['dsn'], $this->arr_config['username'], $this->arr_config['password'], $this->arr_config['options']);
				//Encoding To UTF8
				$this->connDB->exec("set names utf8");
				ini_set("display_errors", "On"); // 顯示錯誤是否打開( On=開, Off=關 )
				error_reporting(E_ALL & ~E_NOTICE);
			}catch(PDOException $error) {
			    echo $sql . "<br>" . $error->getMessage();
			}
		}

		/**
		 * [GetConnDB description]
		 * @return [mysqli | false] [Return The Information Of Connecting DB]
		 */
		function GetConnDB()
		{
			return $this->connDB;
		}

		function InsertData($argStrTable,$argArrData){
			try{
			    $arrData = $argArrData;
			    $this->insertId = -1;

			    $sql = sprintf(
			      	"INSERT INTO %s (%s) values (%s)",
			      	$argStrTable,
			      	implode(", ", array_keys($arrData)),
			      	":" . implode(", :", array_keys($arrData))
			    );
				$statement = $this->connDB->prepare($sql);
				$statement->execute($arrData);
				$this->insertId = $this->connDB->lastInsertId();
				return true;
		 	} catch(PDOException $error) {
		      	echo $sql . "<br>" . $error->getMessage();
		      	return false;
		  	}
		  	return false;
		}

		function GetInsertId()
		{
			return $this->insertId;
		}

		function GetDataByID($argStrTable,$argArrField,$argId){

			try  {
				$strField = implode(',', $argArrField);

			    $sql = "SELECT $strField
			            FROM ".$argStrTable.
			            " WHERE id=:id";


			    $statement = $this->connDB->prepare($sql);
			    $statement->bindParam(':id', $argId, PDO::PARAM_INT);
			    $statement->execute();

			    $result = $statement->fetch(PDO::FETCH_ASSOC);
			    $statement->closeCursor();
			    return $result;
		  	} catch(PDOException $error) {
		      	echo $sql . "<br>" . $error->getMessage();
		  	}

		}

		function GetDataByCondition($argStrTable,$argArrField,$argArrCondition,$argBoolLimit=false,$argStart=0,$argEnd=0,$argOrderBy=''){
			try  {
				$strField = implode(',', $argArrField);
				$strTable = $argStrTable;
				$arrCondition = $argArrCondition;
				$strCondition = '';
				$times = 0;
				foreach ($arrCondition as $key => $value) {
					if(is_string($value))
					{
						$value = "'".$value."'";
					}
					if($times===(count($arrCondition)-1)){
						$strCondition.=$key."=:".$key;
					}else{
						$strCondition.=$key."=:".$key.' AND ';
					}
					$times++;
				}

				//Whether There Is A Condition
			    if(count($arrCondition)!=0){
			    	//Yes
			    	$sql = sprintf("SELECT %s
			            	FROM %s
			            	WHERE %s",$strField,$strTable,$strCondition);
				}else{
					//No
					$sql = sprintf("SELECT %s
			            	FROM %s",$strField,$strTable);
				}
				$sql .= ' '.$argOrderBy;
				if($argBoolLimit === true){
					$start = $argStart;
					$end = $argEnd;
					$sql.= " LIMIT $start, $end";
				}
			    $statement = $this->connDB->prepare($sql);
			    $statement->execute($arrCondition);
			    $statement->setFetchMode(PDO::FETCH_ASSOC);

			    $result = $statement->fetchAll();
			    $statement->closeCursor();
			    return $result;
		  	} catch(PDOException $error) {
		      	echo $sql . "<br>" . $error->getMessage();
		      	exit();
		  	}
		}

		function ModifyDataByCondition($argStrTable,$argArrModifiedData,$argArrCondition){
			try {
				$strTable = $argStrTable;
				$arrModifiedData = $argArrModifiedData;
				$arrCondition = $argArrCondition;
				$strCondition='';

				$arrMdfSentence = array();
				foreach ($arrModifiedData as $key => $value) {
					//Segment The Variable
					$arrMdfSentence[$key] = $key." = :c_".$key;
					$arrModifiedData['c_'.$key] = $value;
					unset($arrModifiedData[$key]);
				}

			    $strModifiedData = implode(",", $arrMdfSentence);

			    $times = 0;
				foreach ($arrCondition as $key => $value) {
					if(is_string($value))
					{
						$value = "'".$value."'";
					}
					if($times===(count($arrCondition)-1)){
						$strCondition.=$key."=:".$key;
					}else{
						$strCondition.=$key."=:".$key.' AND ';
					}
					$times++;
				}


			    $sql = sprintf("UPDATE %s
			            SET %s
			            WHERE %s",$strTable,$strModifiedData,$strCondition);

				$statement = $this->connDB->prepare($sql);
				$arrMerge = array_merge($arrModifiedData,$arrCondition);
				$statement->execute($arrMerge);
			} catch(PDOException $error) {
		    	echo $sql . "<br>" . $error->getMessage();
		  	}
		}

		function DropDataByCondition($argStrTable,$argArrCondition){
			try{
				$sql = "DELETE FROM users WHERE id = :id";

				$arrCondition = $argArrCondition;
				$strTable = $argStrTable;
				$strCondition = '';
				$times = 0;
				foreach ($arrCondition as $key => $value) {
					if(is_string($value))
					{
						$value = "'".$value."'";
					}
					if($times===(count($arrCondition)-1)){
						$strCondition.=$key."=:".$key;
					}else{
						$strCondition.=$key."=:".$key.' AND ';
					}
					$times++;
				}
				$sql = sprintf("DELETE
				            	FROM %s
				            	WHERE %s",$strTable,$strCondition);

			    $statement = $this->connDB->prepare($sql);
			    $statement->execute($arrCondition);
			}catch(PDOException $error) {
		    	echo $sql . "<br>" . $error->getMessage();
		  	}
		}
	};

?>