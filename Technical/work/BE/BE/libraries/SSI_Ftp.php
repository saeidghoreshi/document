<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @author: Sam Bassett, January 3, 2012
* 
* extend base ftp to include some defaults, 
* and add new functions for dynamic domains
* 
* 
* 
*/
class SSI_FTP extends CI_FTP
{
 
	/**
	* same as parent upload, but it handles connect and close for you
	* and also has default values for second two parameters
	* 
	* @param mixed $file
	* @param mixed $path_name
	* @param mixed $encoding
	* @param mixed $chmod
	*/
	public function upload($file,$path_name,$encoding='binary',$chmod=0775)
	{
		
		$config['hostname'] = 'endeavor1.servillianhosting.com';
		$config['username'] = 'fileupload@servilliansolutionsinc.com';
		$config['password'] = 'j1135#weep';

		parent::connect($config);  
		
		//append the root onto the path name every time
		$full_path = $this->get_server_http_root().$path_name;
		 
		$s = parent::upload($file,$full_path,$encoding,$chmod);
		parent::close();  
		return $s;
	}
	/**
	* download function: this does not exist in our version of code igniter
	* copied from CI 2.1 source code: currently we are in 1.7 which does not have
	* download 
	* 
	// --------------------------------------------------------------------

	 * Download a file from a remote server to the local server
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function download($rempath, $locpath, $mode = 'auto')
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		// Set the mode if not specified
		if ($mode == 'auto')
		{
			// Get the file extension so we can set the upload type
			$ext = $this->_getext($rempath);
			$mode = $this->_settype($ext);
		}

		$mode = ($mode == 'ascii') ? FTP_ASCII : FTP_BINARY;

		$result = @ftp_get($this->conn_id, $locpath, $rempath, $mode);

		if ($result === FALSE)
		{
			if ($this->debug == TRUE)
			{
				$this->_error('ftp_unable_to_download');
			} 
			return FALSE;
		}

		return TRUE;
	}
	
	/**
	* upload based on root directory depending on which server
	* 
	*/
	public function get_server_http_root()
    {
		$split = explode('.',$_SERVER['HTTP_HOST']);
		
		$root=$split[0];
		//makes it devbrad from just brad
		if( in_array($root,array('sam','ryan','brad'))) $root = "dev".$root;
		//else it is just stage or live, leave it alone
		
		return "endeavor/".$root."/";
		
    }
}
	
?>
