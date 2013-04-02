<?
function make_href($links)
{
	foreach($links as $key=>$link)
	{
		switch($link['type_name'])
		{
			case "Window":
				$c = $link['window_controller'];
				$m = $link['window_method'];
				$p = $link['window_parameters'];
                $wid = $link['window_id'];
				$d = addslashes($link['window_dimensions']);
				$links[$key]['menu_href'] = "javascript:App.nav(\"$c\",\"$m\",\"$p\",\"$d\",\"$wid\")";
				break;
			
			case "External Link":
				$links[$key]['menu_href'] = $link['link_href'];
				break;
			
			case "Category":
				$links[$key]['menu_href'] = "#";
				break;
		}
		
		if(array_key_exists('items', $link) and !empty($link['items']))
		{
			$links[$key]['items'] = make_href($links[$key]['items']);
		}
	}
	
	return $links;
}

$links = make_href($links);
	
?>

<div id="menubar" class="yuimenubar yuimenubarnav">
	<div class='bd'>
	<ul class='first-of-type'>
	<?
	$x = 0;
	foreach($links as $link)
	{
		$fot = ($x==0) ? 'first-of-type' : '';
		echo "<li class='yuimenubaritem $fot'><a class='yuimenubaritemlabel {$link['image']}' href='{$link['menu_href']}'>{$link['menu_label']}</a>";
		
		if(array_key_exists('items', $link) and !empty($link['items']))
		{
			echo "<div id='{$link['menu_domid']}' class='yuimenu'>";
			echo "<div class='bd'>";
			echo "<ul>";
				$y = 0;
				foreach($link['items'] as $link2)
				{
					$fot = ($y==0) ? 'first-of-type' : '';
					if($y++!=0 and $group2 != $link2['menu_group']) echo "</ul><ul>";
					echo "<li class='yuimenuitem'><a class='yuimenuitemlabel {$link2['image']}' href='{$link2['menu_href']}'>{$link2['menu_label']}</a>";
					$group2 = $link2['menu_group'];
					
					if(array_key_exists('items', $link2) and !empty($link2['items']))
					{	
						echo "<div id='{$link2['menu_domid']}' class='yuimenu'>";
						echo "<div class='bd'>";
						echo "<ul class='$fot'>";
	                    
	                    $z = 0; $group3 = '';            
	                    foreach($link2['items'] as $link3)
	                    {
	                        if($z++!=0 and $group3 != $link3['menu_group']) echo "</ul><ul>";
	                        echo "<li class='yuimenuitem'><a class='yuimenuitemlabel {$link3['image']}' id='link{$link3['menu_domid']}' href='{$link3['menu_href']}'>{$link3['menu_label']}</a></li>";
	                        $group3 = $link3['menu_group'];
						}
						
						echo "</ul>";
						echo "</div>";
						echo "</div>";		
					}
					echo "</li>";
				}
			echo "</ul>";
			echo "</div>";
			echo "</div>";
		}
		echo "</li>";
		$x++;
	}
	?>
	</ul>
	</div>
</div>