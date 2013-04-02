<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');//important security for all libraries
//Extend the built in CI input library, so that we have access to the input functions like get_post for $_REQUEST
//becuase the load function only exists in Controllers $this->load->library('input');
// , and the &get_instance() workaround is buggy :    
class Result extends CI_Input
{
	function __construct()
    {                      
        parent::CI_Input();//extend input library
        $this->_output=false;
    }
    
	private $_output;
	public $use_stars=true;//set to false to avoid using stars
	/**
	* @deprecated
	* 
	* @param mixed $result
	*/
    public function set_output($result)
	{
		$this->_output = $result;
	}
		
	
	public function	json($output=false)
	{
		if($this->_output)
			echo json_encode($this->_output);
		else
			echo json_encode($output);
	}
	/**
	* echo in Ext.js  expected format with success flag set
	* @author Sam
	* @date January 4 2012
	* @param mixed $result
	*/
	public function success($result,$msg=null)
	{
		
        $this->json(array("success"=>true,"result"=>$result,'resultMsg'=>$msg));  
	}
	/**
	* echo in Ext.js  expected format with success flag set
	* @author Sam
	* @date January 4 2012
	* @param mixed $result
	*/
	public function failure($result,$msg=null)
	{
		
        $this->json(array("success"=>false,"result"=>$result,'resultMsg'=>$msg));  
	}
    public function json_pag($data)
    {           
        //$_REQUEST is not safe for sql injection, use ci_input->get_post
    	//this 'isset' check is needed so the method can be c alled two different ways
    	//cannot load a library from another library
    	if(isset($this->input))
    	{
    		//code will go here if Result : : json_pag  is used
    		//snce the scope '$this' will still be the original object (ie, the controller)
    		$start= (int)$this->input->get_post('start');
	        $limit =(int)$this->input->get_post('limit');
			
    	}
    	else
    	{
    		//otherwise, if $this->result->json_pag is called 
    		//code will go here, and result inherits from input so $this->input does not exist
    		$start= (int)$this->get_post('start');
        	$limit =(int)$this->get_post('limit');
    	}
        
        if(!$limit || $limit > PHP_INT_MAX )$limit=PHP_INT_MAX;
        
    	//total count is for display only
        $final["totalCount"]=count($data);
       	//so the record set is spliced by the given page.  takes the place of sql LIMIT _ , _
        $data = array_slice($data, $start, $limit);
        $final["root"]=$data;//the record set
        //header('Content-Type: text/javascript');//header is important
        $this->json($final) ;
    }
    /**
    * this ignores pagination start and end
    * 
    * @param mixed $data
    */
    public function json_pag_store($data)
    {
        $final["root"]=$data;//the record set
        $final["totalCount"]=count($data);
        //header('Content-Type: text/javascript');
        $this->json($final) ;
    }
    
	public function order_by($sql)
	{
		//if no request was made, do nothing
		$sort=$this->get_post('sort');//sort column name
		if(!$sort) return $sql;
		
		//the json is kind of broken depending on the quotations used so fix it, 
		//rawulrdecode somehow doesnt catch these escaped double quotes
		$str=str_replace( '\"','"' ,$sort) ;

		$sort_request=json_decode( $str,true);

		foreach($sort_request as $sort_array)
		{
			$col=$sort_array['property'];
			$dir=$sort_array['direction'];
			$sql.= " ORDER BY ".$col." ".$dir;
			return $sql;//only first one
		}

	}
	
	
}

?>
