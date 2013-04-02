<?php

class Dispatch_model extends Model
{
	
	public function __construct()
	{
		
		
	}
	/*
	if a different statusid is needed: check this list
	<?xml version="1.0" encoding="UTF-8" ?>
<ajax-response>
	<response type="object" id="StatusList" rel="f_statusID">
					<item>
						<id><![CDATA[70228]]></id>
						<name><![CDATA[Assembling Details]]></name>
						</item>
					<item>
						<id><![CDATA[64940]]></id>
						<name><![CDATA[Client Review]]></name>
						</item>
					<item>
						<id><![CDATA[64939]]></id>
						<name><![CDATA[Closed]]></name>
						</item>
					<item>
						<id><![CDATA[120815]]></id>
						<name><![CDATA[Converted]]></name>
						</item>
					<item>
						<id><![CDATA[120828]]></id>
						<name><![CDATA[Development Complete]]></name>
						</item>
					<item>
						<id><![CDATA[120804]]></id>
						<name><![CDATA[In Progress]]></name>
						</item>
					<item>
						<id><![CDATA[64942]]></id>
						<name><![CDATA[Need Assistance]]></name>
						</item>
					<item>
						<id><![CDATA[64938]]></id>
						<name><![CDATA[Open]]></name>
						</item>
					<item>
						<id><![CDATA[120712]]></id>
						<name><![CDATA[Queued]]></name>
						</item>
					<item>
						<id><![CDATA[120711]]></id>
						<name><![CDATA[Ready for Billing]]></name>
						</item>
					<item>
						<id><![CDATA[120714]]></id>
						<name><![CDATA[Sales Lead]]></name>
						</item>
					<item>
						<id><![CDATA[120715]]></id>
						<name><![CDATA[Validate]]></name>
						</item>
					<item>
						<id><![CDATA[64943]]></id>
						<name><![CDATA[Verify &amp; Bill]]></name>
						</item>
			</response>
</ajax-response>
	 
	*/
	
	public function intervals_createtask($title,$description,$module_id)
	{
		
		//create the xml file to POST
		$putString  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$putString .= "<task>\n";
		//$putString .= "<statusid>104807</statusid>\n";
		$putString .= "<statusid>64938</statusid>\n";//Sam: had to fix this for 2.0: july 12 2011
		$putString .= "<projectid>75210</projectid>\n";
		$putString .= "<moduleid>$module_id</moduleid>\n";
		$putString .= "<title>$title</title>\n";
		$putString .= "<dateopen>".date('Y-m-d')."</dateopen>\n";
		$putString .= "<priorityid>62835</priorityid>\n";
		$putString .= "<ownerid>30091</ownerid>\n";
		$putString .= "<assigneeid>30091,69569,67147</assigneeid>\n";//Order:Brad,Sam,Ryan
		$putString .= "<summary>$description</summary>\n";
		$putString .= "</task>\n";

		//intialize and send the curl request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.myintervals.com/task");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, INTERVALS_API_KEY. ":");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/xml", "Content-type: application/xml"));
		//POST
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $putString);
		$response = curl_exec($ch);
		curl_close($ch);

		//output the resulting XML
		return $response;
		
		
	}
	
	
}

?>
