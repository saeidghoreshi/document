<?php

class Ads extends Controller
{
	
	public function get($adname)
	{
		
		switch ($adname)
		{
			case "200x200":
				$this->load->view('ads/200200');
				break;
			case "336x280":
				$this->load->view('ads/336280');
				break;
		}
		
	}
	
}

?>
