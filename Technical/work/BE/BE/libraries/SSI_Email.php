<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class SSI_Email extends CI_Email
{
	
	public function __construct($config=array())
	{
		$this->CI = &get_instance();
		
		// force config
		$config['mailtype'] = 'html';
		$config['send_multipart'] = TRUE;
		
		// construct
		parent::CI_Email($config);
		
		// add additional actions
		$this->from(DEFAULT_FROM_EMAIL, DEFAULT_FROM_NAME);
		$this->bcc('operations_bradley@servillian.ca');
	}
	
	public function message($body,$data=array())
	{
		$data['css']    = $this->CI->load->view("emails/email.css",null,true);
		$data['body']   = $body;
		$body           = $this->CI->load->view("emails/_letterhead",$data,true);
		parent::message($body);
	}
	/**
	* this function dissapeared or was renamed to 'message'. so I just remade it and callled message
	* 
	* @param mixed $message
	* @param mixed $data
	*/
	public function letterhead($message,$data)
	{
		if(!is_array($data))$data=array();
		$this->message($message,$data);
		
	}
	public function send($test=false)
	{
		//avoids missing emails if we're live
		if(SYS_STATE=="LIVE" || $test==false) 
		{
			parent::send();
			return;//and we are done, dont duplicate the emails if test is false
		}
		// if we explicitly say test, or if we're in dev
		if(SYS_STATE=="DEV" || $test==true)
		{
			$this->_set_header('x-spectrum-to', @$this->_recipients);
			$this->_set_header('x-spectrum-cc', @$this->_headers['Cc']);
			$this->_set_header('x-spectrum-bcc',@$this->_headers['Bcc']);
			$this->to('test@playerspectrum.com');
			$this->cc('ryan@servillian.com');
			$this->bcc('test@playerspectrum.com');
			parent::send();
			echo parent::print_debugger();	
		}
		
	}
	
}
