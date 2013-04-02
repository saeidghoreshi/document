<?php

class System extends Controller
{
	
	const KEY = "ARCHIMEDES";
	
	/**
	* @var system_model System_model
	*/
	public $system_model;
	
	public function dbdevtotest($key)
	{
		if($key!=self::KEY) die($key);
		$this->prepSite('test');
	}
	
	private function prepSite($db)
	{
		$this->load->model('system_model');
		$this->system_model->addStructure('dump');
		/*$this->system_model->addStructure('public');
		$this->system_model->addStructure('finance');
		$this->system_model->addStructure('permissions');
		$this->system_model->addStructure('prize');
		$this->system_model->addStructure('schedule');
		$this->system_model->addStructure('websites');
		$this->system_model->addStructure('websites_2');*/
		$this->system_model->run($db);
	}
	
}

?>
