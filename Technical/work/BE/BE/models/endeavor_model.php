<?php

class Endeavor_model extends Model
{
	
	public $db;
	
	public function __construct()
	{
		parent::Model();
		$this->db = $this->load->database(DBKEY,true);
	}
	
	public function get_menu($pid=false,$user,$org)
	{
		//declarations
		$sql = array();
		$params = array();
		
		//default query
		$sql[] = "SELECT 		 sm.id
								,sm.parent
								,sm.menu_label
								,sm.menu_domid
								,sm.menu_group
								,sm.menu_order
								,sm.menu_rowspan
								,sm.menu_colspan
								,sm.menu_default
								,sm.image
								,smt.type_name
								,smt.type_id
								,smw.window_controller
								,smw.window_method
								,smw.window_parameters
								,smw.window_dimensions
								,smw.window_id
								,sml.link_href
								,smh.handler_id
								,smh.handler_method
								,permissions.userorg_can_use_menu(?,?,sm.id) as allowed
				FROM 			system.sys_menu sm
				INNER JOIN 		system.lu_sys_menu_type smt ON sm.menu_type = smt.type_id
				LEFT OUTER JOIN system.sys_menu_window  smw ON sm.id = smw.menu_id
				LEFT OUTER JOIN system.sys_menu_link    sml ON sm.id = sml.menu_id 
				LEFT OUTER JOIN system.sys_menu_handler smh ON sm.id=smh.menu_id
				WHERE			sm.menu_active = TRUE";
		
		$params[] = $user;
		$params[] = $org;
		
		//consider parent, if required
		if($pid)
		{ 
			$sql[] = "AND parent = ?";
			$params[] = $pid;
		} else {
			$sql[] = "AND parent IS NULL";
		}
		
		/*//consider user
		if($user==null||$org==null)
		{
			$sql[] = "AND sm.menu_default = TRUE";
			//$sql[] = "AND permissions.userorg_can_use_menu(10,1,sm.id) = TRUE"; //test data
		} else {
			$sql[] = "AND permissions.userorg_can_use_menu(?,?,sm.id) = TRUE";
			$params[] = $user;
			$params[] = $org;
		}
		*/
		
		//order results
		$sql[] = "ORDER BY menu_group, menu_order";
		
		//combine sql
		$sql = implode("\r\n", $sql);
		
		//make query
		$query = $this->db->query($sql, $params);
		$menu = $query->result_array();
		//mail('operations_bradley@servillian.ca','Last Query from Menubar',$this->db->last_query(),'From: bholbrook@servillian.com');
		
		//check for children
		foreach($menu as $x=>$link)
		{
			$menu[$x]['items'] = $this->get_menu($link['id'],$user,$org); ##RECURSIVE
			if($link['type_id']==3 and count($menu[$x]['items'])==0) unset($menu[$x]); ##remove empty categories
		}
		
		return $menu;
		
	}
	
	public function get_used_icons()
	{
		$sql="SELECT icon FROM system.sys_icons";
		return $this->db->query( $sql,array())->result_array();  
	}
	
	public function process_controllers()
	{
		if ($handle = opendir('./endeavor/controllers/')) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != "..") {
		            $this->process_controller($file);
		        }
		    }
		    closedir($handle);
		}
		
		//$file = './endeavor/controllers/prize.php';
        return true;
	}
	private $classname='';
	public function process_controller($file)
	{	
		$file = './endeavor/controllers/'.$file;
        //var_dump($file);
		$fh = fopen($file, 'r');
		$php = fread($fh, filesize($file));
		fclose($fh); 

		$lines = explode("\n",$php);

		$methods = array();
		$classfound = false;
		foreach($lines as $number=>$line)
		{
			$x = $number + 1;
			$method = false;
			$line = trim($line);
            
			if(strstr($line,'class') and !$classfound)
			{
				$classfound = true;	
				$split = explode(' ', $line);
				$class = strtolower($split[1]);
                
                $classname=$class;
                $this->process_class($class);
                
			}
			/**
			if(strstr($line,'###'))
			{
				$split = explode('###', $line);
				//$line = json_decode($split[1]);
				if($line==NULL) continue;
                
				$methods[$x] =  json_decode($split[1]);//$line;
                $methods[$x]->CLASS= $classname;
                
			}
            */
          //  if($class == "endeavor")  var_dump(strlen($line));
            if(strstr($line,'public function '))
            {                                             
                // var_dump($line);        
                $split = explode('public function ', $line);
                $temp=explode('(',$split[1]);
                                   
                //$line= json_decode('{"FUNCTION":["'.$temp[0].'"]}');
                if($line==NULL) continue;
                $this->process_method($temp[0]);
               // $methods[$x-1]->FUNCTION= $temp[0];//$line;
            }
			//if(array_key_exists($number,$methods))
			//{
                /*
				$items = explode(" ", $line);
				if($items[0]=='public' AND $items[1]=='function') $items = explode("(", $items[2]);
				*/
				//$method = $items[0]; $i = 0;
				//foreach($methods[$number] as $key=>$permissions) $this->process_method($class, $method, $key, $permissions);
			//}
		}
        /*
        foreach($methods as $i=>$v)
        {
            //echo var_dump($v);
            //
            $functionname;
            $classname;
            $groupaccess;
            foreach($v as $m=>$c)
            {
                if($m=='FUNCTION')
                {
                    //echo '<='.$c;
                    $functionname=$c;
                } 
                if($m=='CLASS')
                {
                    //echo '('.$c.')';
                    $classname=$c;
                } 
                //if($m!='FUNCTION' && $m!='CLASS') echo $m.' '. $c[0].'-'.$c[1].'-'.$c[2].'-'.$c[3];
            }
            foreach($v as $x=>$t)  
            {
                if($x!='FUNCTION' && $x!='CLASS')
                $this->db->query('select permissions.handle_functions_list(?,?,?,?,?,?,?);'
                    ,array($classname//controller
                    ,$x,$functionname,($t[0]==1?'true':'false'),($t[1]==1?'true':'false'),($t[2]==1?'true':'false'),($t[3]==1?'true':'false')));
                
                //if($x!='FUNCTION' && $x!='CLASS')
                //echo $classname.'-'.$x.'-'.$functionname.'-'.$t[0].'-'.$t[1].'-'.$t[2].'-'.$t[3].'*******';
            }
            
        }*/
        $this->classname = null;
        
	}
	
	public function process_class($class)
	{
        
        //handle controller
		//var_dump($class);
        $this->classname = $class;
	}
	
	public function process_method( $method)
	{
        //TODO: first check for single underscore 
		//code igniter requires we ignore these:
        //also ignores __constructor()
        if(substr($method,0,1) == "_") return;
        
        $controller = $this->classname;
        $params = array($method,$controller);
        
       // var_dump($controller.":".$method); 
       
        $sql = "SELECT permissions.handle_method_controller(?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        $id= $result->handle_method_controller;
        //var_dump($id);
  
	}
    

    public function userorg_can_update()
    {
		$result = $this->db->query("SELECT ".userorg_can_update());
		$value = $result->first_row()->userorg_can;
		return ($value) ? true : false;
    }



	/**
	* get help file data for this controller/method combo
	* for use with window_ methods specifically
	* 
	* @param mixed $ctr
	* @param mixed $meth
	*/
	public function get_help($ctr,$meth)
	{
		$sql="SELECT data FROM system.sys_help WHERE controller_id = ? AND method_id = ? LIMIT 1";
		return $this->db->query( $sql,array($ctr,$meth))->result_array();  
	}	
	/**
	* adds name of help view file to mc pair
	* 
	* @param int $ctr
	* @param int $meth
	*/
	public function insert_help($ctr,$meth,$data)
	{ 
		$sql="SELECT system.insert_help_data_ids(?,?,?)";
		return $this->db->query( $sql,array($ctr,$meth,$data))->first_row()->insert_help_data_ids;  
	}
   
    public function adjust_fb_friends($fb_id,$fb_friends)
    {
        return $this->db->query( "select * from permissions.\"adjust_fb_friends\"(?,?)",array($fb_id,$fb_friends))->result_array();      
    }
    public function adjust_fb_groups($fb_id,$fb_groups)
    {
        return $this->db->query( "select * from permissions.\"adjust_fb_groups\"(?,?)",array($fb_id,$fb_groups))->result_array();      
    }
    
    /**
    * internal trigger not for public use.  saves to a sql file any permissions changes
    * triggered by updates in maintenance screen. used for pushing changes to live.
    * 
    * 
    * @access private
    * @author Sam Bassett
    * 
    * @param mixed $schema
    * @param mixed $fn
    * @param mixed $args
    */
    public function _track_changes($schema,$fn,$roleid,$meth,$ctr)
    {
 		//always handle first in case it doesnt exist
		$sql="SELECT permissions.handle_method_controller('".$meth."','".$ctr."');";
		
		$sql.="SELECT ".$schema."."."$fn"."(".$roleid.",'".$meth."','".$ctr."');";

		echo $sql;
		//echo "\n";echo getcwd(); "\n";

 		//put in tmp dir
		$name = tempnam('/tmp','maintsql_');
		$fstream = fopen( $name,'a+b')or die("TMP NOT FOUND ".$name);
		fwrite($fstream,$sql);
		fclose($fstream);
		echo "\n$name\n";
		
		if(!isset($_SESSION['maintenance_filename'])) $_SESSION['maintenance_filename']="unknown";//default
		
		//ftp
		$this->load->library('ftp');   

        $today=(int)date('YmdHis');

		$today=date('YmdHis');


		$dest="sql/maintenance/".$_SESSION['maintenance_filename']."-".$today.".sql";;
		//echo "\n$dest\n";
		echo $this->ftp->upload($name,$dest );


		unlink($name);//delete local tmp
    }
    
    public function get_server()
    {
		$split = explode('.',$_SERVER['HTTP_HOST']);
		return $split[0];
    }
    /**
    * used for image upload
    * gets directory root structure based on url
    * 
    */
    public function get_server_http_root()
    {
		$root=$this->get_server();
		//makes it devbrad from just brad
		if( in_array($root,array('sam','ryan','brad'))) $root = "dev".$root;
		//else it is just stage or live, leave it alone
		
		return "endeavor/".$root."/";
		
    }
    public function get_sysmenus()
    {                             
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $whereClause='';
        $filter_id      =$this->input->get_post("filter_id");
        if($filter_id==1)
            $whereClause    =' where menu_active=true ';
        //----------------------------------------------------------------------        
        $q="
            SELECT 
                sm.id,    
                sm.parent,
                sm.menu_label as label,
                sm.menu_order as order,
                sm.menu_type,
                sm.menu_active,
                sm.menu_group,
                sm.image,
                sm.menu_default,
                sm.menu_rowspan,
                sm.menu_colspan,
                smw.window_id,
                sl.link_href
                
            FROM        system.sys_menu         sm
            left join   system.sys_menu_window  smw     on  smw.menu_id =sm.id
            left join system.sys_menu_link      sl      on sl.menu_id   =sm.id
            ".$whereClause." 
            order by menu_group ,menu_order desc
        ";
        
        return $this->db->query($q)->result_array();
    }
    public function updateSysMenuOrdering($complex_str)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("SELECT * FROM \"system\".\"updatesysmenuordering\"(?)",array($complex_str))->result_array();
    }
    public function getMenuTypeItem()
    {                      
        $result=$this->db->query("select type_id,type_name from system.lu_sys_menu_type")->result_array();
        return $result;    
    }
    public function getMenuAuthStore()
    {                      
        $result=$this->db->query("select auth_id as type_id,code as type_name from permissions.lu_authentication")->result_array();
        return $result;    
    }
    public function getRoleMenu($menu_id)
    {
        $q=
        "
            select 
            r.role_id,
            r.role_name,
            rm.menu_id,
            rm.view_auth_id,
            rm.update_auth_id,
            auth1.code                                              as view_auth_name,
            auth2.code                                              as update_auth_name,
            case when auth1.auth_id is not null then 1 else 0 end   as alreadyassigned,
            sw.window_id
            
            from permissions.lu_role r
            left join permissions.role_menu rm              on r.role_id    =rm.role_id         and menu_id=?
            left join permissions.lu_authentication auth1   on auth1.auth_id=rm.view_auth_id
            left join permissions.lu_authentication auth2   on auth2.auth_id=rm.update_auth_id
            left join system.sys_menu_window        sw      on sw.menu_id   =rm.menu_id
            order by rm.view_auth_id
        ";
        return $this->db->query($q,array($menu_id))->result_array();        
    }
    public function deleteSelectedMenuItemsRoles($menurole_ids)
    {
        $result=$this->db->query('select * from "system"."deleteSelectedMenuItemsRoles"(?)',array($menurole_ids))->result_array();
        return $result;    
    }
    public function addSelectedMenuItemsRoles($menuroleauth_ids)
    {
        $result=$this->db->query('select * from "system"."addSelectedMenuItemsRoles"(?)',array($menuroleauth_ids))->result_array();
        return $result;    
    }
    public function updateMenuItem($menu_id,$menuItemLabel,$menuItemType,$menu_group,$menu_default,$menu_rowspan,$menu_colspan,$menu_mage)
    {                   
        $menu_default=($menu_default=='')?'true':'false';
        $result=$this->db->query('select * from "system"."updateMenuItem"(?,?,?,?,?,?,?,?)',array($menu_id,$menuItemLabel,$menuItemType,$menu_group,$menu_default,$menu_rowspan,$menu_colspan,$menu_mage))->result_array();
        return $result;        
    }
    public function getMenuWindowType($menu_id)
    {
        $q=
        "
            select window_id type_id , window_controller||'::'||window_method type_name 
                from system.sys_menu_window 
                where menu_id = ? or menu_id is null
        "
        ;
        $result=$this->db->query($q,array($menu_id))->result_array();
        return $result;        
    }
    public function updateControllerWindow($menu_id,$window_controller,$window_method,$window_id,$new_old_cb)
    {                
        $result=$this->db->query('select * from "system"."updateControllerWindow"(?,?,?,?,?);',array($menu_id,$window_controller,$window_method,$window_id,$new_old_cb))->result_array();
        return $result;
    }
    public function updateExternalLink($menu_id,$link_href)
    {
        $result=$this->db->query('select * from "system"."updateExternalLink"(?,?);',array($menu_id,$link_href))->result_array();
        return $result;
    }
    public function saveNewMenuItem($menu_label)
    {
        $result=$this->db->query('select * from "system"."saveNewMenuItem"(?);',array($menu_label))->result_array();
        return $result;    
    }
    public function deleteMenuItem($menu_id)
    {
        $result=$this->db->query('select * from "system"."deleteMenuItem"(?);',array($menu_id))->result_array();
        return $result;        
    }
    
    
    
    
    public function handle_icon($icon)
    {
		$sql="SELECT system.handle_icon(?)";
        return $this->db->query($sql,array($icon))->first_row()->handle_icon;
    }
    
}

?>
