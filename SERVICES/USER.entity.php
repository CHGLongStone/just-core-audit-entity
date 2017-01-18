<?php
/**
 * 
 * @author	Jason Medland<jason.medland@gmail.com>
 * @package	JCORE\SERVICE\AUDIT
 */
 

namespace JCORE\SERVICE\AUDIT;
#use JCORE\DAO\DAO as DAO;
use SERVICE\AUDIT\AUDIT_ENTITY as AUDIT_ENTITY;


/**
 * Class USER_AUDIT_ENTITY
 *
 * @package JCORE\SERVICE\AUDIT
*/
class USER_AUDIT_ENTITY extends AUDIT_ENTITY{ 

	/** 
	* ACL different joins on DAO
	* roleCfg
	*
	* @access private 
	* @var array
	*/
	private $roleCfg = array(
		'table' => 'user_role',
		'pk_field' => 'user_role_pk',
		'fk_field' => 'user_role_fk',
		'user_role' => 'role',
	);
	
	/**
	* error.global.php  
	*	USER - NOTICES
	* @access public 
	* @var array
	*/
	public $AUDIT_CODE = 1102; # USER_PROFILE_CHANGE;
	
	/** 
	* ACL role
	* 
	* @access private 
	* @var array
	*/
	private $role = null;
	/** 
	* parentTable
	* 
	* @access private 
	* @var array
	*/
	private $parentTable = 'client';
	
	
	/**
	* DESCRIPTOR: __construct
	* 
	* @param param 
	* @return return  
	*/
	public function __construct($args =null){
		//parent::__construct($args);
		return;
	}
	/**
	* DESCRIPTOR: 
	* 
	* @param param 
	* @return return  
	*/
	public function init($args){
		//$this->cfg = $args;
		#parent::__construct($args); extending from DAO
		parent::init($args);	# extending from AUDIT_ENTITY
		/*
		$this->parentTable = '';
		initializeChildRecord(
			$DSN, 
			$table, 
			$pk_field, 
			$fk_field
		)
		joinRecord(
			$DSN, 
			$table, 
			$pk_field, 
			$fk_field, 
			$fk
		)
		$fk = $this->get($this->parentTable, $this->root_pk); //; fk/parent::pk
		$this->initializeChildRecord(
			$args["DSN"], 
			$this->roleCfg["table"], 
			$this->roleCfg["pk_field"], 
			$this->roleCfg["fk_field"], 
			$fk
		);
		*/
		return;
	}
	
	/**
	* DESCRIPTOR: 
	* 
	* @param args 
	* @return return  
	*/
	public function getSessionID($args = null){
		#echo __METHOD__.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'.PHP_EOL; 
		#$IDCOMPONENTS = $GLOBALS['CONFIG_MANAGER']->getSetting($LOAD_ID = 'USER', 'IDCOMPONENTS');
		$checkString = '';
		foreach($args AS $key => $value){
			if('' != $checkString){
				$checkString .= ':::';
			}
			$checkString .= $value;
		}
		$SessionID = password_hash($checkString, PASSWORD_DEFAULT);
		return $SessionID;
	}
	/**
	* DESCRIPTOR: 
	* 
	* @param param 
	* @return return  
	*/
	public function checkSessionID($args){
		if(!is_array($args)){
			return false;
		}
		#if(!isset($args["IDCOMPONENTS"]) || !is_array($args["IDCOMPONENTS"] || !isset($args["hash"]) ){
		/*
		*/
		if(!isset($args["hash"]) ){
			if (isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] != 'production') {
				$args = array(
					"Code" => -32602,
					"Data" => __FILE__.'@'.__LINE__.' IDCOMPONENTS missing '.json_encode($args),
					//"Message" => "",
				);
				$error = new ERROR($args);
				return $error;
			}else{
				return false;
			}
		}
		$useArgs = false;
		if(isset($args["IDCOMPONENTS"]) && is_array($args["IDCOMPONENTS"]) && 0 < count($args["IDCOMPONENTS"])){
			$iteration = $args["IDCOMPONENTS"];
			$useArgs = true;
		}else{
			$iteration = $GLOBALS['CONFIG_MANAGER']->getSetting($LOAD_ID = 'USER', 'IDCOMPONENTS');
		}
		$checkString = '';
		foreach($iteration AS $key => $value){
			if('' != $checkString){
				$checkString .= ':::';
			}
			if(true === $useArgs){
				$checkString .= $value;
			}else{
				if(is_array($value) && isset($value["SCOPE"]) && 'SESSION' == strtoupper($value["SCOPE"])){
					$checkString .= $_SESSION[$value["REFERENCE"]];
				}elseif(isset($$value)){
					$checkString .= $$value;
				}
			}
				
		}
		$isValid = password_verify($checkString,$args["hash"]);
		return $isValid;
	}
	
	/**
	* DESCRIPTOR: 
	* 
	* @param args 
	* @return return  
	*/
	public function getRole($args = null){
		/*
		$roleCfg = array(
			'table' => 'user_role',
			'pk_field' => 'user_role_pk',
			'fk_field' => 'user_role_fk',
			'user_role' => 'role',
		);
		$this->config
		echo 'config<pre>'.print_r($this->config, true).'</pre>'.PHP_EOL;
		*/
		if(null === $this->role){
			#$this->role = $this->get($this->roleCfg["tableName"], $this->root_pk);
			$role = $this->get($this->config["table"], $this->roleCfg["fk_field"]);
			echo 'role<pre>'.print_r($role, true).'</pre>'.PHP_EOL;
			/*
			$role = 10;
			*/
			$query = 'SELECT * FROM '.$this->roleCfg["table"].' WHERE '.$this->roleCfg["pk_field"].' = '.$role.' ';
			#$result = $db->SQL_select($config["DSN"], $query, $returnArray=true);
			$result = $GLOBALS["DATA_API"]->retrieve($this->config["DSN"], $query, $args=array('returnArray' => true));
			#echo 'result<pre>'.print_r($result, true).'</pre>'.PHP_EOL;
			$this->role = $result[0][$this->roleCfg["user_role"]];
			if('NO SUCH VALUE' == $this->role || 0 == count($result)){
				$this->role = 'guest';
			}
		}
		#echo 'this->role<pre>'.print_r($this->role, true).'</pre>'.PHP_EOL;
		
		return $this->role;
	}
	/**
	* DESCRIPTOR: 
	* 
	* @param args 
	* @return return  
	*/
	public function getAPIKey($args = null){
		if(!isset($this->config["DSN"])){
			$this->config["DSN"] = 'JCORE';
		}
		$query = '
			SELECT api_key 
			FROM client 
			WHERE client_pk = (
				SELECT client_fk 
				FROM client_user
				WHERE client_user_pk = '.$_SESSION["user_id"].'
			)
		';
		#echo __METHOD__.__LINE__.'$query<pre>['.var_export($query, true).']</pre>'.'<br>'.PHP_EOL; 
		#$result = $db->SQL_select($config["DSN"], $query, $returnArray=true);
		$result = $GLOBALS["DATA_API"]->retrieve($this->config["DSN"], $query, $args=array('returnArray' => true));
		#echo __METHOD__.__LINE__.'$result<pre>['.var_export($result, true).']</pre>'.'<br>'.PHP_EOL; 
		return $result[0]["api_key"];
	}
	/**
	* DESCRIPTOR: 
	* 
	* @param args 
	* @return return  
	*/
	public function getClientID($args = null){
		if(!isset($this->config["DSN"])){
			$this->config["DSN"] = 'JCORE';
		}
		$ClientID = $this->get('client', 'client_pk');
		#echo __METHOD__.__LINE__.'$ClientID<pre>['.var_export($ClientID, true).']</pre>'.'<br>'.PHP_EOL; 
		if(!is_numeric($ClientID) || 'NO SUCH VALUE' == $ClientID){
			$query = '
				SELECT client_pk 
				FROM client 
				WHERE client_pk = (
					SELECT client_fk 
					FROM client_user
					WHERE client_user_pk = '.$_SESSION["user_id"].'
				)
			';
			#echo __METHOD__.__LINE__.'$query<pre>['.var_export($query, true).']</pre>'.'<br>'.PHP_EOL; 
			#$result = $db->SQL_select($config["DSN"], $query, $returnArray=true);
			$result = $GLOBALS["DATA_API"]->retrieve($this->config["DSN"], $query, $args=array('returnArray' => true));
			#echo __METHOD__.__LINE__.'$result<pre>['.var_export($result, true).']</pre>'.'<br>'.PHP_EOL; 
			$ClientID = $result[0]["client_pk"];
			
		}
		
		return $ClientID;
	}
}

?>
