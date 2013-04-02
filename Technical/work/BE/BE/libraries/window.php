<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Window extends Page
{
	
	private $window = "";
	
	public function __construct()
	{
		parent::__construct();
		$this->window = new stdClass();
	}
	
	public function set_header($header)
	{
		$this->window->header = $header;
	}
	
	public function set_body($body)
	{
		$this->window->body = $body;
		parent::set_body($body);
	}
	
	//depreciated, use setHelpFile
	public function set_footer($footer)
	{
		$this->window->footer = $footer;
	}
	
	public function set_id($id)
	{
		$this->window->id= $id;
	}
	
	public function json()
	{
		if($this->window) $this->output['WINDOW'] = $this->window;
		parent::json();
	}
	
}

?>
