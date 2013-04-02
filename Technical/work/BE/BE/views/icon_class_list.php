<?php
header('Content-Type: text/css');

echo "/* This file is a generated CSS file. Do not edit. */\n\n";

foreach($rules as $rule)
{
	echo "
	a.x-menu-item-link img.{$rule['class']}, 
	div.x-btn em button span.{$rule['class']}, 
	span.{$rule['class']}
	{ 
		background-image: url({$rule['path']}{$rule['entry']}); 
		background-color:transparent; 
	}\r\n";
}
