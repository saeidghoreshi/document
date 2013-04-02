<?php
header('Content-Type: text/css');

$dirs = array();

$dirs[] = array("../../../global_assets/social/","social_"); 
$dirs[] = array("../../../global_assets/silk-disabled/","","-disabled"); 
$dirs[] = array("../../../global_assets/silk/","","");
$dirs[] = array("../../../global_assets/flags/","fflag_","");
$dirs[] = array("../../../global_assets/fugue/","fugue_","");
$dirs[] = array("../../../global_assets/sweetie/","sweet_","");

foreach($dirs as $mydir)
{
	$d = dir($mydir[0]);
	$path = str_replace("../../../","http://endeavor.servilliansolutionsinc.com/",$mydir[0]);
	while($entry = $d->read()) { 
		if ($entry!= "." && $entry!= "..")
		{ 
			list($class,$ext) = explode(".",$entry);
			$class = @$mydir[1].$class.@$mydir[2];
			echo "a.x-menu-item-link img.$class, div.x-btn em button span.$class{ background-image: url({$path}{$entry}); background-color:#FFEEEE}  \r\n";
		}
	} 
	$d->close(); 
}

?>
