<?php
/**
 * AUDIT_LOG
 * log changes from external sources
 * 
 * @author	Jason Medland<jason.medland@gmail.com>
 * @package	JCORE\SERVICE\AUDIT
 * 
 */
 
 

namespace JCORE\SERVICE\AUDIT;

use JCORE\TRANSPORT\SOA\SOA_BASE as SOA_BASE;
use JCORE\DAO\DAO as DAO;

use JCORE\SERVICE\AUDIT\AUDIT_ENTITY as AUDIT_ENTITY;
/**
 * Class AUDIT_LOG
 *
 * @package JCORE\SERVICE\AUDIT
*/
class AUDIT_LOG extends SOA_BASE{ 
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
	* config
	*
	* @access public 
	* @var array
	*/
	public $config = array();
	
	/** 
	* changeList
	*
	* @access public 
	* @var array
	*/
	public $changeList = array();
	
	
	/**
	* DESCRIPTOR: an empty constructor, the service MUST be called with 
	* the service name and the service method name specified in the 
	* in the method property of the JSONRPC request in this format
	* 		""method":"AJAX_STUB.aServiceMethod"
	* 
	* @param null 
	* @return null  
	*/
	public function __construct(){
		#$this->parentTable = 'client_log';
		return;
	}
	/**
	* DESCRIPTOR: an example namespace call 
	* @param param 
	* @return return  
	*/
	public function init($args){
		$this->params = $args;
		

		$this->AUDIT_ENTITY = new AUDIT_ENTITY();
		$this->AUDIT_ENTITY->init($this->params);
		/*
		echo __METHOD__.'@'.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'; 
		*/		
		return;
	}
	
	/**
	* DESCRIPTOR: an example namespace call 
	* $this->AUDIT->createLogEntry($args);
	*
	* @param array args
	* @return mixed serviceResponse  
	*/
	public function createAuditLogEntry($args){
		
		//echo __METHOD__.'@'.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'.PHP_EOL; 
		$this->init($args);

		$result = $this->AUDIT_ENTITY->save();
		
		/*
		echo __METHOD__.'@'.__LINE__.'$this->AUDIT_ENTITY->tables<pre>['.var_export($this->AUDIT_ENTITY->tables, true).']</pre>'.'<br>'.PHP_EOL;
		echo __METHOD__.'@'.__LINE__.'$result<pre>['.var_export($result, true).']</pre>'.'<br>'.PHP_EOL;
		*/	
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
			$info = implode(',',$this->changeList);
		}else{
			$info = 'update info before submitting updates ';
		}
		$response = array(
			'status' => 'OK',
			'info' => $info,
		);
		if(isset($args['callback'])){
			//angular.callbacks._0
			//$response[$args['callback']] = 'we did something else with your record';
		}
		$this->serviceResponse = $response;
		#		echo __METHOD__.'@'.__LINE__.'$this->serviceResponse[]<pre>['.var_export($this->serviceResponse, true).']</pre>'.'<br>'; 
		
		
		#echo __METHOD__.'@'.__LINE__.' '.'<br>'.PHP_EOL; 
		return $this->serviceResponse;
	}
	

	

}



?>