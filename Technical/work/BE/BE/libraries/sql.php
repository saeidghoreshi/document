<?php

class Sql
{	
	public function getQuery($name, $aReplacements=array())
	{
		ob_start();
		include('./endeavor/sql/'.$name);
		$query = ob_get_contents();
		ob_end_clean();
		
		$tag = "@";
		foreach($aReplacements as $sql)
		{
			$pieces = explode($tag,$query,2);
			$query = implode($sql,$pieces);
		}
		
		return $query;
	}
	
}

?>
