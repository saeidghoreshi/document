<?php

class ChaseOrbital
{

	public $IndustryType;
	public $MessageType;
	public $Bin;
	public $MerchantID;
	public $TerminalID;
	public $CardBrand;
	public $CardNumber;
	public $Exp;
	public $CurrencyCode;
	public $CurrencyExponent;
	public $CardSecVal;
	public $AVSzip;
	public $AVSaddress1;
	public $AVSaddress2;
	public $AVScity;
	public $AVSstate;
	public $AVSphoneNum;
	public $AVSname;
	public $OrderID;
	public $Amount;
	public $Comments;

	public function __construct()
	{

	}

	public function setDefault($aDefault)
	{
		foreach($aDefault as $k=>$v)
		{
			if(isset($this->$$k)) $this->$$k = $v;
		}
	}

	public function cc_auth_capture($aValues)
	{

		$data = array(
			'IndustryType'		=> "",
		    'MessageType'		=> "",
		    'Bin'				=> "",
		    'MerchantID'		=> "",
		    'TerminalID'		=> "",
		    'CardBrand'			=> "",
		    'CardNumber'		=> "",
		    'Exp'				=> "",
		    'CurrencyCode'		=> "",
		    'CurrencyExponent'	=> "",
		    'CardSecVal'		=> "",
		    'AVSzip'			=> "",
		    'AVSaddress1'		=> "",
		    'AVSaddress2'		=> "",
		    'AVScity'			=> "",
		    'AVSstate'			=> "",
		    'AVSphoneNum'		=> "",
		    'AVSname'			=> "",
		    'OrderID'			=> "",
		    'Amount'			=> "",
		    'Comments'			=> ""
		);

		return $this->load->view('chase/cc_auth_capture',true,$data);

	}

	public function cc_refund()
	{
		
	}

	public function cc_void()
	{
		
	}

	public function check_cvv()
	{
		
	}

	public function check_avs()
	{
		
	}

	public function send_request($xml)
	{
		
	}

}
