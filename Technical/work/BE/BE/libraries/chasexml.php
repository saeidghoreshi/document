<?php

class ChaseOrbital
{

	public $CI;
	public $aDefault = array();
	public $Url;

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

	public $xmlCcAuthCapture;

	public function __construct()
	{
		$this->CI = &get_instance();
	}

	public function setDefault($aDefault, $mode="TEST")
	{
		$this->aDefault[$mode] = $aDefault;
	}

	public function loadMode($mode)
	{
		foreach($this->aDefault[$mode] as $k=>$v)
		{
			if(isset($this->$k)) $this->$k = $v;
		}
	}

	public function prepData($data, $values)
	{
		foreach($data as $k=>$v)
		{
			if(isset($this->$k)) $data[$k] = $this->$k;
			if(array_key_exists($k, $values)) $data[$k] = $values[$k];
		}

		return $data;
	}

	public function cc_auth_capture($aValues, $send=true)
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

		$data = $this->prepData($data, $aValues);

		$this->xmlCcAuthCapture = $this->load->view('chase/cc_auth_capture',true,$data);

		if($send) return $this->send_request($this->xmlCcAuthCapture);

	}

	public function cc_refund()
	{
		
	}

	public function cc_void()
	{
		
	}

	/**
	* Send request to Chase Orbital
	*
	*/
	public function send_request($xml)
	{
		$this->CI->load->library('curl');
		$this->curl->create($this->Url);

		$input = "POST /AUTHORIZE HTTP/1.0\r\n";
        $input.= "MIME-Version: 1.0\r\n";
        $input.= "Content-type: application/PTI46\r\n";
        $input.= "Content-length: ".strlen($xml)."\r\n";
        $input.= "Content-transfer-encoding: text\r\n";
        $input.= "Request-number: 1\r\n";
        $input.= "Document-type: Request\r\n";
        $input.= "Interface-Version: Test 1.4\r\n";
        $input.= "Connection: close \r\n\r\n"; 
        $input.= $xml;
                     
        $this->curl->option(CURLOPT_HEADER, false);
        $this->curl->option(CURLOPT_CUSTOMREQUEST, $header);
        $this->curl->option(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->option(CURLOPT_SSL_VERIFYHOST, 1);
        $response = $this->curl->execute();
        
        if($response===false) echo "[".$this->curl->error_code."][".$this->curl->error_string."]<br/>\n";
                              
        $xml_parser = xml_parser_create();
        xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($xml_parser, $response, $values, $index);
        xml_parser_free($xml_parser);
        
        return $values;
	}

	public function check_cvv()
	{
		
	}

	public function check_avs()
	{
		
	}

	

}
