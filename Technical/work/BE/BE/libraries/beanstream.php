<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
  
class Beanstream
{
	
	private $CI;
	
	private $debug = false;//change to false to hide all debug messages // if($this->debug)
	
	private $str_error = null;
	
	
	
	public function __construct()
	{ 
		$this->CI = &get_instance();
	}
	
	public function get_error()
	{
		return $this->str_error;
	}
	
	/**
	* this was in finance controller : _make_beanstream_report_params 
	* requires constants to be already defined for the current application
	* 
	* @param mixed $batch_id
	* @param mixed $batch_to
	*/
	public function make_curl_report_params($batch_id,$batch_to=false)
	{
		//if no range, then we ony want one batch
		//otherwase we get whole range
		if(!$batch_to) $batch_to = $batch_id+1;//tried plus 1
		
		$post=array();
		 
		$post['merchantId']   = BEANSTREAM_MERCHANT_ID;
		$post['loginCompany'] = BEANSTREAM_COMPANY_ID;
		$post['loginUser']    = BEANSTREAM_LOGIN;
		$post['loginPass']    = BEANSTREAM_PASSWORD;
 
		//then we are looking for a report
		$post['rptFormat'] = 'CSV';//other options are TAB or XML
		$post['rspFormat'] = 'CSV';//name value pair
		$post['rptTarget'] = 'INLINE';
		
		 		/*
 		*quote from an email 
 		* Change "rptAPIVersion" to "rptVersion" and I believe it will work.  This is an error
			in the documentation that is in the process of being reworked.

			Thanks,

			Ben Cameron
			Lead Client Services Consultant 
 		*/
		//$post['rptAPIVersion']=BEANSTREAM_RPT_VERSION;//does not work
		
		$post['rptVersion'] = BEANSTREAM_RPT_VERSION;

		$post['rptType']    = 'BATCH_EFTTRANS';//not a constant: its a string 
		//$post['rptType']='BATCH_FILE';//not a constant: its a string 
		
		$post['rptNoFile']  = '1';// 
		$post['rptRangeSelector'] = '1';//by batch id range
		
		$post['rptStartBatchId'] = $batch_id;//by batch id range
		$post['rptEndBatchId']   = $batch_to;//by batch id range
	  
	//	$post['rptTransStatus'] 
	//	$post['rptBatchState'] 
 
		return $post;
	}
	
	
	/**
	* requires constants to be defined for this application, and merchant agreement
	* 
	* chapter 8 of BEAN_processing_guide.pdf
	*/
	public function make_curl_eft_params()
	{
		$post=array();
	 
		$post['serviceVersion'] = BEANSTREAM_VERSION; ; 
		//$post['merchantId']   = BEANSTREAM_MERCHANT_ID;
		$post['loginCompany']   = BEANSTREAM_COMPANY_ID;
		$post['loginUser']      = BEANSTREAM_LOGIN;
		$post['loginPass']      = BEANSTREAM_PASSWORD;

		
		//today doesnt work, so put tomorrow
		$today    = date('Ymd');
		$tomorrow = date('Ymd',strtotime($today.' + 1 day')); 
		
		
		$post['processDate'] = $tomorrow;
		$post['processNow']  = 0;//zero to use the above date: since NOT credit card
 
		return $post;
	}
	
	
	/**
	* attach this file to teh params array and return it 
	* this way the controller does not have to know the array index to use, or the 
	* special character prefix format
	* 
	* @param mixed $params
	* @param mixed $file_path
	*/
	public function add_file_to_params($params,$file_path)
	{
		$params['batchFile'] = "@". $file_path ;
		return $params;
	}
	
	
	/**
	* built the eft url for sending batch files 
	* using constants and input params
	* 
	* @param mixed $params
	*/
	public function make_eft_url($params)
	{
		return BEANSTREAM_BATCH_URL.http_build_query($params);
	}
	
	/**
	* the url for the report s, with the given parameters
	* 
	* @param mixed $params
	*/
	public function make_report_url($params)
	{
		return BEANSTREAM_REPORT_URL.http_build_query($params);
	}
	
	private $withdraw_index=false;
	public function set_withdraw_index($idx)
	{
		$this->withdraw_index = $idx;
	}
	
 	/**
 	* for one single withdraw, create the csv line item 
	* that will take up one single line in teh batch file to be 
	* uploaded to beanstream
	* 
	* this function does NOT write to any physical files, it just returns the string
	*  
	* @return string $newLine
 	* 
 	* @param mixed $amt
 	* @param mixed $inst
 	* @param mixed $transit
 	* @param mixed $account
 	* @param mixed $local_reference
 	* @param mixed $name
 	*/
	public function make_eft_item_data( $amt,$inst,$transit,$account,$local_reference,$name )
	{ 
		//$EOL = "\n";
		$c=',';
		
		//basic data: electronic and ddebit (not C for credit card)
		$newLine = 'E'.$c.'D';
		
		$amt = $amt*100;//convert to pennies
		
		//default  if unset
		//$local_id =  ($this->withdraw_index && isset($w[$this->withdraw_index])) ? $w[$this->withdraw_index] : null; 
		
		
		//page 60 of 81 of the BEAN PROCESSING GUIDE  manual
		
		$newLine.= $c.$inst;
		$newLine.= $c.$transit;
		$newLine.= $c.$account;
		$newLine.= $c.$amt;
		$newLine.= $c.$local_reference  ;//reference numer, can be anyuthing, beanstream ignores this remotelyh, it is four our use only
		$newLine.= $c.$name; 

		//$newLine.= $EOL;
		
		return $newLine; 
	}
	
	public function is_status_failed($state)
	{
		//from sectoin 8.3.1
		
		return $state == 2;//2 is failed
	}
	
	
	public function is_state_complete($state)
	{
		//from sectoin 8.3.1
		
		return $state == 4;//4 is complete
	}
	
	
	public function parse_message_id($msgids)
	{
		return explode(",",$msgids);
	}
	
 
	
	
	public function do_cancel_fees_apply($csv_msg_ids)
	{
		$msg_ids = $this->parse_message_id($csv_msg_ids);
		//var_dump($msg_ids);
		//from pgs 46, 47 of 80. EBP & ACH response messages
		//$fees_apply_here = array(1,2,3,4,5,31,36,16,17,19,20,21,22,23,46,47);
		//new data : any fee code 16 or higher means fees applied
		foreach($msg_ids as $msg_id)
		{
			//echo $msg_id." means what?";
			//if(in_array($msg_id,$fees_apply_here)) return true;
			$msg_id = (int)$msg_id;
			if($msg_id >= 16) return true;
			
			//else keep looking
		}
		//if none of the many types have fees, then false		
		return false;
	}
	
	
	/**
	* similar to make_eft_batch_file_withdrawls, except instead of taking raw withdrawls
	* it takse array of line items and writes those to a file directly
	* 
	* @param mixed $file_path
	* @param mixed $lineitems
	*/
	public function make_eft_batch_file_lineitems($file_path,$array_of_lines)
	{
		if(!is_array($array_of_lines))
		{
			$this->str_error = "make_eft_batch_file_lineitems : is_array failed";
	 
			return false;
		}
		$file = @fopen($file_path,FOPEN_READ_WRITE_CREATE) ;// 
		
		if($file===false)//if fopen failed
		{			
			$this->str_error = "make_eft_batch_file_lineitems : fopen failed";
			return false;
		} 
		
		$first = true;
		
		$EOL = "\r\n";
		
		foreach($array_of_lines as $newLine)
		{
			//echo "write new line $newLine \n";
			if(!$first) $newLine = $EOL.$newLine;
			$first = false;
			fwrite($file,$newLine);
		}
		 
		fclose($file);
		
		if(file_exists($file_path))
		{
			//sucess; it was created
			
			$this->file_path = $file_path;
			//echo "COMPLETE $file_path";
			return true;			
		}
		else
		{
			$this->str_error = "make_eft_batch_file_lineitems : file_exists failed";
			return false;
		}
	}
	
    private $file_path=false;
	 /**
	 * 
	 * used to be finance controller _make_beanstream_batch_file
    * chapter 7 of BEAN_processing_guide.pdf
    * this also inserts into eft, and eft_items tables
    * 
    * not really deprecated , but is not currently in use, as our new process requires the eft_item records
    * to be made at the time of the withdrawl,, at which point the eft file has not been creaed yet
    * 
    * use make_eft_batch_file_lineitems if you have premade line items, and are createing a file that way
    * 
    * assumse $withdraws array has following array keys
    */
    public function make_eft_batch_file_withdrawls($file_path,$withdraws)
    {  
		if(!is_array($withdraws) || count($withdraws)==0) 
		{
			return false;
		}
 
		$file = @fopen($file_path,FOPEN_READ_WRITE_CREATE) ;// 
		
		if($file===false)//if fopen failed
		{
			return false;
		}
		//BEANSTREAM uses plain csv, with no header or footers to the file 
		$array_of_lines=array();
		$i=0;
		foreach($withdraws as $w)
		{
			//E for electronic , D stands for debit (so not credit card)
			$i++;
			$newLine = $this->make_eft_item_data($w);
			 
			fwrite($file,$newLine);
			 
			$array_of_lines[$local_id] = $newLine;//indexed by i or the withdraw_index
		}
		 
		fclose($file);
		
		if(file_exists($file_path))
		{
			//sucess; it was created
			
			//1 for pending, null for batch id
			//
			$this->file_path = $file_path;
			return $array_of_lines;			
		}
		else
		{
			return false;
		}
		 
    }
    
    
    
    
    
    /**
	* generates filename with given prefix and extension
	* at most maxlength characters including both
	* pass maxLength as false or zero to ignore name length completely
	* @author sam
	* @param mixed $maxlength
	* @param mixed $prefix
	* @param mixed $ext
	* @return string
	*/
	public function generate_eft_filename($maxlength=false,$prefix='',$ext='.csv')
	{
		//the function uniqid returns 13 character long string
		$name = uniqid($prefix).$ext;
		
		$strlen = strlen($name);
		if($maxlength && $strlen > $maxlength )
		{
			$cut = $strlen - $maxlength;
			
			$name = substr($name,$cut);
			
		} 
		return $name;
	}
	
	
	
	/**
	* from finance->_handle_beanstream_eft_response
	* 
	* @return :
	*    on success : true
	*    on failure : false
	*    on tryAgainLater : -1
	* @param mixed $code
	* @param mixed $message
	* @param mixed $batch_id
	* @param mixed $params
	*/
	public function handle_curl_batch_eft_response($code)
	{
 
		//beanstream uses these codes:
		
		$success = false;
		//$tryagain=false;//not used
		switch($code)
		{
			//leave in a special case for each error type.  all but the first one are a failure state
			//in future we may need different behaviour for each
			case BSERROR_SUCCESS:
			
			
			
				$success = true;
			break;
			case BSERROR_ACCOUNT:
			
			break;
			
			//tf do not have break; , can be added later
			case BSERROR_AUTH:
			
			
			case BSERROR_DATE:
			
			case BSERROR_DISABLED:
			
			case BSERROR_FILENAME:
			
			
			case BSERROR_FILENOTSENT:
			
			case BSERROR_FILESIZE:
			
			case BSERROR_INSECURE:
			
			
			case BSERROR_LOGIN:

			case BSERROR_OUTDATED:
			
			
			
			case BSERROR_UNKNOWN:
			default:
			
				$success = false;
			break;			
			case BSERROR_SERVERBUSY:
				$success = -1;//this only happnes if we upload two in a row
			break;
			
			
		}
		
		
		
  
		return $success;
		//
	}
	
	
	
	/**
	* was _parse_beanstream_xml_response
	* 
	* @param mixed $file_contents
	* @param mixed $rootnode
	* @return string
	*/
	public function parse_xml_eft_response($file_contents,$rootnode='response')
	{
	 	//strip out whitespace from start of file using ltrim
	 	//this avoids DOCTYPE errors if there is whitespace before the DOCTYPE XML identifiers
		$file_contents= ltrim($file_contents);
		 
		$this->CI->load->library('xml');
 
 		$this->CI->xml->load_string($file_contents);
 		
		$parsed =  $this->CI->xml->parse();
		
		if($parsed===false)
		{
			//echo "\nunable to open or parse file" .$full_filepath;
			return false;
		}
		
		$response=false;
		if(is_array($parsed) && isset($parsed[$rootnode]) && isset($parsed[$rootnode][0]))
		{
			$response=$parsed[$rootnode][0];
 
			foreach($response as $node=>$data)
			{ 
				$response[$node]=implode(',',$data);
				//echo $node. "-> ".$response[$node]."\n";
				 
			}
		
		}
		return $response;
	}
	/**
	* parse the response
	* 
	* @param mixed $file_contents
	* @return string
	*/
	public function parse_csv_report_response($file_contents)
	{
		$rsp=array();
		
		
		$lines = explode("\n",$file_contents);
		
		if(!is_array($lines)|| !count($lines)) return false;
		//its like a table: top row lists the keys. then the rest is laid out in column
		$keys= explode(',', array_shift($lines) ) ;
		foreach($keys as $i=>$k)
		{
			$keys[$i] = trim($k);//strip whitespace from keys
		}
		 
		foreach($lines as $ln)
		{
			//if its an empty line, skip it. happens sometimes at end of report
			if(!strlen($ln)) {continue;}
			$row=array();
			//build associative array using keys
			$ln_array = explode(',',$ln);
			foreach($keys as $k)
			{
				//use the column header , then pull data from front of array
				//also, strip outt the double quotes that beanstream uses to delimiate strings
				//also trim tabs/newlines/whitespace from ENDpoints of string
				$row[$k] = trim(str_replace('"','', array_shift($ln_array)  )  );
			}
			$rsp[]=$row;
			
		}
		return $rsp;
	}
	
	/**
	* @deprecated
	* using CSV instead
	* 
	* @param string $file_contents
	*/
	public function parse_xml_report_response($file_contents)
	{
		$file_contents= ltrim($file_contents);
 
		$this->CI->load->library('xml');
 
 		$this->CI->xml->load_string($file_contents);
 		
		$parsed =  $this->CI->xml->parse();

		return $parsed;
	}
	
 
	
	
	/**
	* output an error report message to email, and a data for vardump to capture
	* set test to false (default) for live
	* 
	* @param string $message
	* @param array $rawdata
	* @param bool $test
	*/
	public function email_error_report($message,$rawdata)
	{ 
		//capture vardump as string to pass into the email
		ob_start();
		echo "<pre>";
		var_dump($rawdata);
		echo "</pre>";
		$ob = ob_get_contents();
		ob_end_clean();
 
		$message.= "\nData var_dump below:\n".$ob;
		 
		$today = date('Y-m-d, g:i:s');
		$subject = 'ERROR: Spectrum Beanstream CRON failed at '.$today; 
 		
 		//in any system other than live, do not email the error just do this
 		if(SYS_STATE != 'LIVE')
 		{ 
 			//if in test mode do not emial just vardump
 			$rawdata['subject'] = $subject;
 			$rawdata['message'] = $message;
 			var_dump($rawdata);
			return;			
 		}
 		
 		
 		//now we know this error happened in live
        //not using a VIEW currently
        $this->CI->load->library('email');
        $this->CI->email->from(DEFAULT_FROM_EMAIL);
        $this->CI->email->to(ERROR_EMAIL);
        $this->CI->email->subject($subject);
        $this->CI->email->message($message);
        $this->CI->email->send(false);
        
        
		
      //  if($test) echo "email_error sent to ".$sendto;
	}
}
?>
