<?php
/**
 * 
 * @author	Jason Medland<jason.medland@gmail.com>
 * @package	JCORE\SERVICE\AUDIT
 */
 

namespace JCORE\SERVICE\AUDIT;
use JCORE\DAO\DAO as DAO;


/**
* Class AUDIT_ENTITY
*
* @package JCORE\SERVICE\AUDIT
*/
class AUDIT_ENTITY extends DAO{ 
	/** 
	* logConfig
	*
	* @access public 
	* @var array
	*/
	public $logConfig = array();
	
	/**
	* DESCRIPTOR: __construct
	* 
	* @param param 
	* @return return  
	*/
	public function __construct($args =null){
		/*
		echo __METHOD__.__LINE__.'<br>'.PHP_EOL;
		echo __METHOD__.__LINE__.'get_class<pre>['.var_export(get_class($this), true).']</pre>'.'<br>'.PHP_EOL; 
		echo __METHOD__.__LINE__.'get_class_methods<pre>['.var_export(get_class_methods($this), true).']</pre>'.'<br>'.PHP_EOL; 
		echo __METHOD__.__LINE__.'get_parent_class<pre>['.var_export(get_parent_class(get_class()), true).']</pre>'.'<br>'.PHP_EOL; 
		#echo __METHOD__.__LINE__.'get_class: parent<pre>['.var_export(get_class(parent), true).']</pre>'.'<br>'.PHP_EOL; 		
		#echo __METHOD__.__LINE__.'get_class_methods: parent<pre>['.var_export(get_class_methods(parent), true).']</pre>'.'<br>'.PHP_EOL; 		
		#echo __METHOD__.__LINE__.'get_parent_class: parent<pre>['.var_export(get_parent_class(parent), true).']</pre>'.'<br>'.PHP_EOL; 		
		//
		*/
		parent::__construct($args);
		return;
	}
	/**
	* DESCRIPTOR: init
	* @param param 
	* @return return  		
	*/
	public function init($args = null){
		/*
		echo __METHOD__.__LINE__.'<br>'.PHP_EOL;
		echo __METHOD__.__LINE__.'get_class<pre>['.var_export(get_class($this), true).']</pre>'.'<br>'.PHP_EOL; 
		echo __METHOD__.__LINE__.'get_parent_class<pre>['.var_export(get_parent_class(__CLASS__), true).']</pre>'.'<br>'.PHP_EOL; 
		echo __METHOD__.__LINE__.'<br>'.PHP_EOL;
		echo __METHOD__.__LINE__.'get_class_methods<pre>['.var_export(get_class_methods($this), true).']</pre>'.'<br>'.PHP_EOL; 
		*/
		#parent::__construct($this->logConfig);
		
		$this->logConfig = $GLOBALS["CONFIG_MANAGER"]->getSetting('AUTH','AUDIT','CLIENT_LOG');
		
		parent::__construct($args);
		return;
	}

	
	/**
	* magic wrapper
	* @param	string 	method
	* @param	array 	args
	* @return bool
	*/
	public function __call($method, $args ){
		#echo __METHOD__.__LINE__.'<br>'.PHP_EOL;
		$table 	= $args[0];
		$column = $args[1];
		$result = parent::__call($method, $args );

		if('set' == $method){
			$this->logChange($args);
		}
		return $result;
	}
	/**
	* Log an entity change
	* 
	* @param	array 	args
	* @return bool
	*/
	public function logChange($args){
		#echo __METHOD__.__LINE__.'$this->AUDIT_CODE['.$this->AUDIT_CODE.'] args<pre>['.var_export($args, true).']</pre>'.'<br>'.PHP_EOL; 
		if(!isset($args["code"]) || !is_numeric($args["code"])){
			if(isset($this->AUDIT_CODE)){
				$args["code"] = $this->AUDIT_CODE;
			}else{
				return false;
			}
		}
		
		if(!isset($args["data"]) || '[]' == $args["data"]){
			return false;
		}
		
		
		$query = '
		INSERT INTO client_log (
			client_user_fk, 
			client_fingerprint_fk, 
			log_code, 
			log_message, 
			log_data
		)
		VALUES (
			'.$_SESSION['user_id'].',
			'.$args["client_fingerprint_pk"].', 
			'.$args["code"].', 
			"'.$args["log_message"].'",
			\''.$args["data"].'\'
		)';
		
		#echo __METHOD__.'@'.__LINE__.'  query<pre>['.var_export($query, true).']</pre> '.'<br>'.PHP_EOL; 
		#echo __METHOD__.'@'.__LINE__.'  $this->logConfig<pre>['.var_export($this->logConfig, true).']</pre> '.'<br>'.PHP_EOL; 
		$result = $GLOBALS["DATA_API"]->create($this->logConfig['DSN'], $query, $extArgs=array('returnArray' => true));
		#echo __METHOD__.'@'.__LINE__.'  result<pre>['.var_export($result, true).']</pre> '.'<br>'.PHP_EOL; 
		if(isset($result["INSERT_ID"])){
			return true;
		}
		return false;
	}
	/**
	* DESCRIPTOR: STORES CHANGES TO THE DAO TO THE DB(s)
	* @param	string 	table
	* @return outputErrors 
	*/
	public function save($table=null){
		#echo PHP_EOL.PHP_EOL.__METHOD__.__LINE__.'get_class['.get_class($this).'] chr(92) ['.chr(92).']<pre>['.var_export($this->modifiedColumns , true).']</pre>'.'<br>'.PHP_EOL; 
		
		if(0 < count($this->modifiedColumns)){
			$codeDescription = $GLOBALS['CONFIG_MANAGER']->getSetting($LOAD_ID = 'ERROR', $SECTION_NAME = $this->AUDIT_CODE);
			
			$replace = array("->"); 
			$search  = array(
				chr(92), # \
			); 
			$calledClassName = str_replace($search, $replace, get_class($this) );

			$args = array(
				'client_fingerprint_pk' => 1,
				'log_message' => $codeDescription.' Modified Data in: '.$calledClassName,
				'code' => $this->AUDIT_CODE,
				'data' => json_encode($this->modifiedColumns),
			);
			$this->logChange($args);
		}
		parent::save($table);
	}
}

?>
