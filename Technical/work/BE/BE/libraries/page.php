<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page
{
	
	private $html="";
	private $jspath="";
	private $js = array();
	private $csspath="";
	private $css = array();
	private $CI;
	protected $output = array();
	
	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	public function set_body($body)
	{
		$this->html = $body;
	}
	
	public function set_js_path($path)
	{
		$this->jspath = $path;	
	}
	
	public function add_js($js,$ignorePath=false)
	{
		if(!$ignorePath) $js = $this->jspath.$js;
		$this->js[] = $js;
	}
	
	public function set_css_path($path)
	{
		$this->csspath = $path;	
	}
	
	public function add_css($css,$ignorePath=false)
	{
		if(!$ignorePath) $css = $this->csspath.$css;
		$this->css[] = $css;
	}
	
	public function	json()
	{
		$this->output['HTML'] = $this->html;
		if($this->js)		$this->output['JS'] = $this->js;
		if($this->css)		$this->output['CSS'] = $this->css;
		
		$data = array();
		echo json_encode($this->output);
	}
	
}

?>
