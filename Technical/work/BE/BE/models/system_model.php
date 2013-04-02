<?php

class System_model extends Model
{
	
	private $fk = array();
	private $trigger = array();
	private $sqltxt = array();
	private $sqlline = 1;
	
	public function addStructure($schema)
	{
		//$this->sqltxt[] = "DROP SCHEMA IF EXISTS $schema CASCADE;";
		//$this->sqltxt[] = "CREATE SCHEMA $schema;";
		
		$lines = file("../updates/struct/$schema.sql");
		$sqllines = array();
		$nsqllines = 1;
		
		$type = 'code';
		
		foreach ($lines as $line) {
		    
		    if(strstr($line,'/*')){ $type = 'comment'; }
		    if(preg_match("/^[-]{2}[.]*/",$line)){ $type = 'comment'; }
		    
		    if($type=='code')
		    {
				$line = trim($line);
				if(!array_key_exists($nsqllines,$sqllines)) $sqllines[$nsqllines] = "";
				$sqllines[$nsqllines] .= $line." ";
				if(substr($line,-1)==";") $nsqllines++;
		    }
		    
		    if(strstr($line,'*/')){ $type = 'code'; }
			if(preg_match("/^[-]{2}[.]*/",$line)){ $type = 'code'; }
			
		}
		
		foreach($sqllines as $line)
		{
			
			if( empty($line)
				|| ($line=="\n")
				//|| preg_match("/INSERT INTO/", $line)
				//|| preg_match("/DROP/", $line)
				//|| preg_match("/ALTER SEQUENCE/", $line)
			)  continue;
			
			if(preg_match("/ADD FOREIGN KEY/", $line)){ $this->fk[] = $line; continue; }
			
			//if(preg_match("/CREATE TRIGGER/", $line)){ $this->trigger[] = $line; continue; }
			
			//$line = str_replace('INHERITS ("'.$schema.'"."statistics")', 'INHERITS ("system"."statistics")',$line);
			
			$this->sqltxt[] = $line;
			
		}
		
	}
	
	public function addData($schema)
	{
		
	}
	
	private function addForeignKeys()
	{
		
	}
	
	public function run($db)
	{
		$db = $this->load->database($db,true);
		
		foreach($this->sqltxt as $sql)
		{
			
			try
			{
				$query = $sql;
				
				//LOOK FOR KEYWORDS
				$keywords = array('CREATE','TABLE','DROP','SCHEMA','SEQUENCE','WITH','TRUE','FALSE','IF','EXISTS','NOT','NULL','INCREMENT','MINVALUE','MAXVALUE','START','CACHE',
								  'CASCADE','OIDS','DEFAULT','INHERITS','ALTER','ADD','PRIMARY','KEY','UNIQUE','FOREIGN','SET','TYPE','FUNCTION','RETURNS','LANGUAGE','OWNER','TO',
								  'AS');
				foreach($keywords as $w) $sql = str_replace($w, "<span style='color:#000099; FONT-WEIGHT:BOLD'>$w</span>", $sql);
				echo "<div style='font-size:9px;FONT-FAMILY:ARIAL'><p>$sql</p></div>";
				
				$db->query($query);
				
			}
			catch(Exception $e)
			{
				
			}
		}
	}
	
}

?>
