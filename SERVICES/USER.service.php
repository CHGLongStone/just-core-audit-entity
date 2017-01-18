<?php
/**
 * USER_PROFILE
 * 
 * 
 * @author	Jason Medland<jason.medland@gmail.com>
 * @package	JCORE\SERVICE\AUDIT
 * 
 */
 
 

namespace JCORE\SERVICE\AUDIT;

use JCORE\TRANSPORT\SOA\SOA_BASE as SOA_BASE;
use JCORE\DAO\DAO as DAO;

use JCORE\SERVICE\AUDIT\USER_AUDIT_ENTITY as USER_ENTITY;
/**
 * Class USER_PROFILE
 *
 * @package JCORE\SERVICE\AUDIT 
*/
class USER_PROFILE extends SOA_BASE{ 
	/** 
	* serviceRequest
	*
	* @access protected 
	* @var array
	*/
	protected $serviceRequest = null;
	/** 
	* serviceResponse
	*
	* @access public 
	* @var array
	*/
	public $serviceResponse = null;
	/** 
	* error
	*
	* @access public 
	* @var array
	*/
	public $error = null;
	/** 
	* params
	*
	* @access public 
	* @var array
	*/
	public $params = array();
	/** 
	* changeList
	*
	* @access public 
	* @var array
	*/
	public $changeList = array();
	/** 
	* ACL
	* role
	*
	* @access private 
	* @var array
	*/
	private $role = null;
	
	/**
	* DESCRIPTOR: an empty constructor, the service MUST be called with 
	* the service name and the service method name specified in the 
	* in the method property of the JSONRPC request in this format
	* 		""method":"AJAX_STUB.aServiceMethod"
	* 
	* @param param 
	* @return return  
	*/
	public function __construct(){
		$this->parentTable = 'client_user';
		return;
	}
	/**
	* DESCRIPTOR: an example namespace call 
	* @param param 
	* @return return  
	*/
	public function init($args){
		$this->params = $args;
		
		if(!isset($this->params["DSN"])){
			$this->params["DSN"] = "JCORE";
		}
		
		
		$config = array(
			"DSN" => $this->params["DSN"],
			"table" => $this->parentTable,
			"pk_field" => "client_user_pk",
			"pk" => $this->params["client_user_pk"],
		);
		/*
		$DAO2 = new JCORE\DAO\DAO($config);		
		*/
		$this->USER_ENTITY = new USER_ENTITY();
		$this->USER_ENTITY->init($config);
		$this->role = $this->USER_ENTITY->getRole($args = null);
		/*
		$this->USER_ENTITY->initialize($config["DSN"], $config["table"], true);
		$this->USER_ENTITY->initializeChildRecord($config["DSN"], $this->childTableXXX, 'principal_contact', 'client_user_pk');
		$this->USER_ENTITY->initializeChildRecord($config["DSN"], $this->childTableXXX, 'client_user_pk', 'principal_contact');
		*/
		return;
	}
	/**
	* DESCRIPTOR: an example namespace call 
	* 
	* @param param 
	* @return return  
	*/
	public function init2($args){
		#echo __METHOD__.'@'.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'; 

		return;
	}
	

	
	
	/**
	* DESCRIPTOR: an example namespace call 
	* @param param 
	* @return return  
	*/
	public function UPDATE($args){
		$this->init($args);
		/**
		look up the user
		verify that the client_user_pk and email match the $args['SESSIONID']
		#echo __METHOD__.'@'.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'; 
	
		echo __METHOD__.'@'.__LINE__.'$this->USER_ENTITY->get()<pre>['.var_export($this->USER_ENTITY->get($this->parentTable, 'alias22'), true).']</pre>'.'<br>'; 
		*/
		foreach($this->params AS $key => $value){
			//NO SUCH VALUE
			if('password' != $key){
				$testVal = $this->USER_ENTITY->get($this->parentTable, $key);
				if('NO SUCH VALUE' != $testVal && $value != $testVal){
					$this->changeList[] = $key.' was updated from ['.$testVal.'] to ['.$value.']';
					$this->USER_ENTITY->set($this->parentTable, $key, $value);
					//echo '$key['.$key.'] $testVal['.$testVal.'] <pre>['.var_export($value, true).']</pre>'.'<br>'.PHP_EOL; 
				}
			}else{
				if('password' == $key && '' != $value){ 
					$this->changeList[] = 'password  updated';
					$password = \password_hash($value, PASSWORD_DEFAULT);
					$this->USER_ENTITY->set($this->parentTable, 'password', $password);
				}
				
			}
		}
		#echo __METHOD__.'@'.__LINE__.' '.'<br>'.PHP_EOL; 
		
		
		$result = $this->USER_ENTITY->save($this->parentTable);
		#		echo __METHOD__.'@'.__LINE__.'$result<pre>['.var_export($result, true).']</pre>'.'<br>'; 
		if(
			isset($result[0]['EXCEPTION']['ID']) 
			&& 
			1062 == $result[0]['EXCEPTION']['ID']
		){
			$result['status'] = 'FALED';
			$this->serviceResponse = $result;
			
			return $this->serviceResponse;
			#return $result;
		}

		#echo __METHOD__.'@'.__LINE__.' '.'<br>'.PHP_EOL; 
		if(1 <= count($this->changeList)){
			#$info = implode(',',$this->changeList);
			$info = $this->changeList;
		}else{
			$info = 'update info before submitting updates ';
		}
		$response = array(
			'status' => 'OK',
			'info' => array(
				'msg' => $info,
			),
		);
		if(isset($args['callback'])){
			$response['info']['callback'] = $args['callback'];
		}
		$this->serviceResponse = $response;
		#		echo __METHOD__.'@'.__LINE__.'$this->serviceResponse[]<pre>['.var_export($this->serviceResponse, true).']</pre>'.'<br>'; 
		
		
		#echo __METHOD__.'@'.__LINE__.' '.'<br>'.PHP_EOL; 
		return $this->serviceResponse;
	}
	
}



?>