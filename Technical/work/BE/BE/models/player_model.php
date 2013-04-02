<?php

require_once('./endeavor/models/person_model.php');
require_once('./endeavor/models/statistics_model.php');
class Player_model extends Person_model //extends Statistics_model via __call
{
	
	private $ext = array();
	
	///// MAGIC METHODS /////////////////////////////////////////////////////////
	
	public function __construct()
	{
		parent::__construct();
		$this->ext[] = new Statistics_model();
	}
	
	/**
	* Looks for additional classes used for extending. Called magically.
	* 
	* @param mixed $method
	* @param mixed $params
	* @return mixed
	*/
	public function __call($method, $params)
	{
		foreach($this->ext as $class){
		   if (method_exists($class, $method)){
		      return call_user_func_array(array($class, $method), $params);
		   }
		}
	}
	
}

?>
