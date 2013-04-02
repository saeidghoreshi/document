<?php
require_once('endeavor.php');
class Prize extends Endeavor
{
	
	/**
	* @var Prize_model
	*/
	public $prize_model;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('prize_model');
		$this->load->model('entity_model');
		$this->load->model('finance_model');
		$this->load->library('result');
		$this->load->library('images');
	}
	
	private function load_window()
	{
		
		$this->load->library('window');
		
		  
		$this->window->set_css_path("/assets/css/prizes/");
		//
	}
	
	
	public function window_manage()//manage prizes
	{
		//assoc level
		$this->load_window();

		
		$this->window->set_js_path("/assets/js/models/");
		//models
		$this->window->add_js("prize_asset.js");
		$this->window->add_js("prize_category.js");
		$this->window->add_js("prize_price.js");
		
		$this->window->add_js("prize_size.js");
		$this->window->add_js("warehouse.js");
		$this->window->add_js("inventory.js");
		
		$this->window->set_js_path("/assets/js/components/prizes/");
		
		//load order: toolbar; forms; grids;         class is always last
		$this->window->add_js('toolbar.js');
		$this->window->add_js('forms/prizes.js');
		$this->window->add_js('forms/categories.js');
		$this->window->add_js('forms/warehouses.js');
		$this->window->add_js('forms/sizes.js');
		$this->window->add_js('forms/assets.js');
		$this->window->add_js('windows/assets.js');		
		$this->window->add_js('windows/prizes.js');
		$this->window->add_js('windows/categories.js');
		$this->window->add_js('windows/warehouses.js');
		$this->window->add_js('windows/sizes.js'); 
		
		//root classes
    	//$this->window->add_js('../grids/spectrumgrids.js');
		//grids
		$this->window->add_js('grids/sizes.js');
		$this->window->add_js('grids/assets.js');
		$this->window->add_js('grids/inventory.js');
		$this->window->add_js('grids/prices.js');
		$this->window->add_js('grids/warehouses.js');
		$this->window->add_js('grids/categories.js');
		$this->window->add_js('grids/prizes.js');
		//load the class that is the main controller that links the components together
		$this->window->add_js('controller.js');
		
		//CSS
		$this->window->add_css('DataView.css');
		
		
		//HTML
		$this->window->set_header('Prize Inventory');
		$this->window->set_body($this->load->view('prizes/manage.php',null,true));
		
		$this->window->json();
	}
	
    public function window_manageorders()
    {
    	//get org type:: is it league,assoc,team
    	// in 2.0, leagues can make orders, and assoc can manage orders in a different way
    	$type=$this->permissions_model->get_active_org_type();
    	$data=array('org_type'=>$type);
    	
    	$this->load->library('window');
    	
      	//add the models  	
		$this->window->set_js_path("/assets/js/models/");
		

    	
        $this->window->add_js('prize_order.js');
        $this->window->add_js('order_item.js');
        $this->window->add_js('order_sizes.js');
		
		
    	//css

    	$this->window->add_css('../assets/css/prizes/DataView.css');
    	
    	
    	
    	$this->window->set_js_path("/assets/js/components/orders/");

    	//load some stuff from the prizes component
        $this->window->add_js('../prizes/grids/prizes.js');

    	//$this->window->set_js_path("/assets/js2/orders/");

    	//get forms and windows
        $this->window->add_js('forms/order_upgrade.js');
        $this->window->add_js('forms/order_prizes.js');
        $this->window->add_js('forms/order_create.js');
        $this->window->add_js('windows/order_upgrade.js');
        $this->window->add_js('windows/order_prizes.js');
        $this->window->add_js('windows/order_create.js');
        //data grids
        $this->window->add_js('grids/orders.js');
        $this->window->add_js('grids/order_items.js');
        //class to control all components
        $this->window->add_js('controller.js');
        //HTML:
        $this->window->set_header('Prize Orders');
        $this->window->set_body($this->load->view('prizes/orders.php',$data,true));
        
    	$this->window->json();
    }
    /*
    public function window_manageorders($id=false)
    {
    	//assoc level 
    	
        //$data['details']['categories'] = $this->prize_model->get_categories();
        
        //body & footer
        $data['body'] = array();
        $data['body']['orders'] = $this->load->view('prize/manage/orders',null,true);
        $data['body']['details'] = $this->load->view('prize/manage/details',null,true);
        //$data['body']['validate'] = $this->load->view('prize/manage/validate',null,true);
        $data['footer'] = array();
        
        //window
        $this->load_window();
        $this->window->add_js('class.orders.manage.js');
        $this->window->set_header('Manage Orders');
        $this->window->set_body($this->load->view('prize/order.manage.php',$data['body'],true));
        $this->window->set_footer($this->load->view('prize/order.manage.footer.php',$data['footer'],true));
        if($id) $this->window->set_id($id);
        $this->window->json();
        
    }   
    
 */
    
    /*
    public function window_payableinvoices($id=false)
    {
        $data['body'] = array();
        $data['body']['view'] = $this->load->view('prize/myinvoices/view',null,true);//first tab 
        $data['body']['items'] = $this->load->view('prize/myinvoices/items',null,true);// tab 
        $data['body']['pay'] = $this->load->view('prize/myinvoices/pay',null,true);//
        $data['body']['new'] = $this->load->view('prize/myinvoices/new',null,true);//
        $data['footer'] = array();
        $this->load_window();
        //$this->window->add_css('myinvoices.css');
        $this->window->add_js('class.invoice.payable.js');
        $this->window->set_header('My Invoices');
        $this->window->set_body($this->load->view('prize/myinvoices.php',$data['body'],true));
        $this->window->set_footer($this->load->view('prize/myinvoices.footer.php',$data['footer'],true));
        if($id) $this->window->set_id($id);
        $this->window->json();
        
        
    }*/
	
	/*
	public function window_manageinventoryitems($id=false)
	{
		//details
		//$data['details']['categories'] = $this->prize_model->get_categories();
		
		//body & footer
		$data['body'] = array();
		$data['body']['prizes'] = $this->load->view('prize/modify/prizes',null,true);
		$data['body']['categories'] = $this->load->view('prize/modify/categories',null,true);
		$data['body']['warehouses'] = $this->load->view('prize/modify/warehouses',null,true);
		$data['body']['details'] = $this->load->view('prize/modify/details',null,true);//was data[details]
		$data['body']['inventory'] = $this->load->view('prize/modify/inventory',null,true);		
		$data['body']['prices'] = $this->load->view('prize/modify/prices',null,true);
		//$data['body']['media'] = $this->load->view('prize/modify/media',null,true);
		$data['footer'] = array();
		
		
		//window
		$this->load_window();
		$this->window->add_css('modify.css');
		$this->window->add_js('class.prize.inventory.modify.js');
		$this->window->set_header('Manage Prize Inventory');
		$this->window->set_body($this->load->view('prize/inventory.modify.php',$data['body'],true));
		$this->window->set_footer($this->load->view('prize/inventory.modify.footer.php',$data['footer'],true));
		//if($id) $this->window->set_id($id);
		$this->window->json();
	}*/
    /*
	public function window_shipping()
	{
		//js class.order.shipping.js
        
        //dir prize/shipping 
        //first tab       orders.php
        $data['body'] = array();
        $data['body']['orders'] = $this->load->view('prize/shipping/orders.php',null,true);
        $data['body']['details'] = $this->load->view('prize/shipping/details.php',null,true);
        $data['body']['picked'] = $this->load->view('prize/shipping/picked.php',null,true);
        $data['body']['completed'] = $this->load->view('prize/shipping/completed.php',null,true);
        //$data['body']['pdf'] = $this->load->view('prize/shipping/pdf.php',null,true);
        $data['footer'] = array();
        
        //window
        $this->load_window();
        $this->window->add_css('modify.css');
        $this->window->add_js('class.order.shipping.js');
        $this->window->add_js('pdf/jspdf.js');
        $this->window->set_header('Ready for Shipping');
        $this->window->set_body($this->load->view('prize/order.shipping.php',$data['body'],true));
        $this->window->set_footer($this->load->view('prize/shipping/footer.php',$data['footer'],true));
        //if($id) $this->window->set_id($id);
        $this->window->json();
        
        
	}
*/
	public function post_order_id_pdfsession()
	{
		$_SESSION['pdf_order_id'] = (int)$this->input->get_post('order_id');
	}

	
	public function html_picklist()
	{
		$order_id = (int)$this->input->get_post('order_id');
		
		$order = $this->prize_model->get_order_details($order_id);
		
		$header =  "<p>".$order[0]['created_name']."</p>    "
				  ."<p>".$order[0]['order_id']    ."</p>    "
		   		  ."<p>".$order[0]['org_name']    ."</p>    ";
		$org_id = $order[0]['org_id'];   	
		$entity = $this->org_model->get_entity_from_org($org_id);	  
		$address = $this->entity_model->get_entity_address($entity);  
		
		$header .= "<p>" .$address[0]['address_street']. "</p>"
				//.  "<p>"    "</p>"
				.  "<p>" .$address[0]['address_city']
				.  ", ".  $address[0]['region_abbr'].    "</p>"
				.  "<p>" .$address[0]['country_abbr'].   "</p>"
				.  "<p>" .$address[0]['postal_value'].   "</p>";
		
		$items = $this->prize_model->get_orderitems($order_id);
		$table="<table border='1'  width='100%'> "
		."<tr>"
        	."<td width='10%' align='center'>Qty</td>"
			."<td width='10%' align='center'>Warehouse</td>"
        	."<td width='50%' align='center'>Name</td>"
        	."<td width='10%' align='center'>Size</td>"
        	."<td width='10%' align='center'>SKU</td>"
        	."<td width='10%' align='center'>UPC</td>"
        ."</tr>";
		foreach($items as $i)
	    {
	    	if(!$i['qty']){continue;}
	    	$table.=
	    	"<tr>"
	    		."<td>".$i['qty']      ."</td>"
	    		."<td>".''             .'</td>'
	    		."<td>".$i['name']     ."</td>"
	    		."<td>".$i['size_abbr']."</td>"
	    		."<td>".$i['sku']      ."</td>"
	    		."<td>".$i['upc']      ."</td>"
	    	."<tr>";
			
	    }
	    $table.="</table>";
	    echo $header.$table;
	}
	
	
	
	public function pdf_picklist()
	{
		$order_id=$_SESSION['pdf_order_id'];
		$items = $this->prize_model->get_orderitems($order_id);
		 
		header("Pragma: public");
		header("Pragma: no-cache");//for safari?
		//header("Expires: 0");//for safari?
		header("Expires: 0"); // set expiration time
		
		if(isset($_SESSION['schedule_name']))
			$name=$_SESSION['schedule_name'];
		else
			$name="New_Schedule";
		$file_name = $name.".pdf";
		header('Content-type: application/pdf');
		
		header("Content-Disposition: attachment; filename=\"".$file_name."\"");
		
		 try {
	    $p = new PDFlib();

	    if ($p->begin_document("", "") == 0) {
	        die("Error: " . $p->get_errmsg());
	    }

	    $p->set_info("Creator", "");
	    $p->set_info("Author", "");
	    $p->set_info("Title", $name );

	    $p->begin_page_ext(595, 842, "");

	    $font = $p->load_font("Helvetica-Bold", "winansi", "");

	    $p->setfont($font, 24.0);
	    $p->set_text_pos(50, 700);
	    $p->show("List of items for order#");
	    $p->show($order_id);
	    foreach($items as $i)
	    {
	    	$p->continue_text($i['qty']);
			$p->show($i['name']);
			$p->show($i['size_name']);
			
	    }

    	;
		
		
	    $p->end_page_ext("end first page");
	    //////BEGIN SECOND PAGE
		$p->begin_page_ext(595, 842, "");

	    $font = $p->load_font("Helvetica-Bold", "winansi", "");

	    $p->setfont($font, 24.0);
	    $p->set_text_pos(50, 700);
	    $p->show("PAGE 2...:");


		
	    $p->end_page_ext("");
	    //////
	    $p->end_document("");

	    $buf = $p->get_buffer();
	    $len = strlen($buf);



	   // print $buf;
	    
	    echo $buf;//echo buff works!:!:!!
		}
		catch (PDFlibException $e) {
		    die("PDFlib exception occurred in hello sample:\n" .
		    "[" . $e->get_errnum() . "] " . $e->get_apiname() . ": " .
		    $e->get_errmsg() . "\n");
		}
		catch (Exception $e) {
		    die($e);
		}
		$p = 0;//clear buffer thing?? 

	}
	
    
  
    
    
    
    
	// JSON /////////////////////////////////////////////////////////////////////////

	public function json_getcurrencies()
	{
		$currencies = $this->finance_model->get_currencies();
		foreach($currencies as $i=>$c) $currencies[$i]['is_active'] = false;
		$this->result->json($currencies);
	}
	/**
	* currencies that this entity is using
	* 
	*/
	public function json_active_currencies()
	{
		$org=$this->permissions_model->get_active_org();
		$entity_id=$this->org_model->get_entity_id_from_org($org);
		 
		//for example: NSA has league dollars; SSI has CDN
		$currencies = $this->finance_model->get_entity_and_parent_currencies($entity_id);


		$this->result->json($currencies);
	}
	/**
	* this returns inv specifically based on a warehouse and prize id
	* returns data for all possible sizes in lu_ table
	* 
	*/
	public function json_getinventory()
	{
		$prize = (int)$this->input->get_post('prize_id');
		$wh    = (int)$this->input->get_post('warehouse_id');
		$sizes = $this->prize_model->get_inventory($prize,$wh);
		foreach($sizes as $i=>$size)
		{
			//$sizes[$i]['is_active'] = ($size['is_active']=='t') ? "YES" : "NO";
			$sizes[$i]['quantity_change'] = "0";//thi8s is not a database field, adding it for updates
			
			$sizes[$i]['quantity'] = $size['quantity'] ? $size['quantity']:"0";//turn null into zero
           // $sizes[$i]['wh_qty']="0";//updates later
			//$sizes[$i]['size_insert'] = "0";
			//$sizes[$i]['is_updated'] = false;
			//$sizes[$i]['cur1'] = number_format(($size['cur1']) ? $size['cur1'] : "0.00", 2, ".", "");
			//$sizes[$i]['cur2'] = number_format(($size['cur2']) ? $size['cur2'] : "0.00", 2, ".", "");
			//$sizes[$i]['total'] = ($size['quantity'])?$size['quantity']:"0";//used to be size_qty// changed for warehouses
		}
		echo $this->result->json_pag($sizes);
	}
	/**
	* get a list of all prizes stored in this warehouse, so ignore any with zero quantity
	* merges sizes together, so just a summary
	* gives prize name, id, and total
	* 
	*/
	public function json_warehouse_prizes()
	{
		$whid =(int) $this->input->get_post('warehouse_id');
		$prizes = $this->prize_model->get_prizes($this->permissions_model->get_active_org());
		$inv=array();
		foreach($prizes as $prize)
		{
			$pid   = $prize['prize_id'];
			$pname = $prize['name'];
			$temp_inv= $this->prize_model->get_inventory($pid,$whid);
			$total=0;
			foreach($temp_inv as $sizes)//over all sizes
			{
				$q=$sizes['quantity'];
				if(!$q){$q=0;}//convert null and strings to ints;
				$total+=$q;
			}
			// if($total)//skip if zero items
			 	$inv[] = array('name'=>$pname,'prize_id'=>$pid,'total'=>$total);
		}
		$this->result->json($inv);
	}
	
	public function json_getprices()
	{
		$prize = (int)$this->input->get_post('prize_id');
		$currency_id = (int)$this->input->get_post('currency_id');
		$data=$this->prize_model->get_prices($prize,$currency_id);
		foreach($data as $i=>$row)
		{
			if(!$data[$i]['is_active'] )$data[$i]['is_active']='f';//if null field, defaulted to false
			//$data[$i]['size_qty'] = ($size['quantity'])?$size['quantity']:"0";//for this warehouse
           // $sizes[$i]['wh_qty']="0";//updates later
			//$data[$i]['size_insert'] = "0";
			//$data[$i]['is_updated'] = false;
			$data[$i]['price'] = number_format( ($row['price']) ? $row['price'] : "0.00", 2, ".", "");//null price to zero
			//$data[$i]['cur2'] = number_format(($row['cur2']) ? $row['cur2'] : "0.00", 2, ".", "");
			//$sizes[$i]['total'] = ($size['quantity'])?$size['quantity']:"0";//used to be size_qty// changed for warehouses
		}
		echo $this->result->json_pag($data);
	}

	
	/**
	* gets all prizes, or prizes by category, with default image
	* 
	*/
	public function json_getprizes()
	{
		//if current org is assoc we are ok
		$assocs_org_id=$this->permissions_model->get_active_org();
		 
		if($this->permissions_model->get_active_org_type() == ORG_TYPE_LEAGUE)
		{
			//otherwise we need parent org id
			$assocs_org_id=$this->org_model->get_parent_org_id($assocs_org_id);
		}
		 $max_name_len=12;
		$category_id=(int)$this->input->get_post('category_id');
		if(empty($category_id)||$category_id==-1)
			{$prizes = $this->prize_model->get_prizes($assocs_org_id);}
		else
			{$prizes = $this->prize_model->get_prizes_by_category($category_id);}
			
			
		 $this->result->json($this->_format_prizes($prizes));
	}
	private function _format_prizes($prizes)
	{
		$activeorg=$this->permissions_model->get_active_org();
		$default_logo = $this->org_model->get_org_logo($activeorg);
		$max_name_len=12;
		foreach($prizes as &$p)
		{
			$p['short_name'] = substr($p['name'],0,$max_name_len);
			$p['total_stock'] = (int)$p['total_stock'];//converts null to zero for us
			$p['avg_price']   = number_format($p['avg_price'],2,'.','');//converts null to zero for us
			$p['min_price']   = number_format($p['min_price'],2,'.','');//converts null to zero for us
			if(!$p['filepath']) $p['filepath'] = $default_logo;//default image if none found
			if(!$p['thumb_filepath']) $p['thumb_filepath'] = $default_logo;//default image if none found
		}
		return $prizes;
	}
	public function json_prizes_by_category()
    {
		$category_id = (int)$this->input->get_post('category_id');
		$prizes = $this->prize_model->get_prizes_by_category($category_id);
		
		 $this->result->json($this->_format_prizes($prizes));
    }
	public function json_prize_assets()
    {
		$prize_id=(int)$this->input->get_post('prize_id');
		
		$images=$this->prize_model->get_prize_assets($prize_id);
		$fancy="F j, Y";
		foreach($images as &$i)
		{
			if($i['description']==null) $i['description']='';
			$i['created_on']=date($fancy,strtotime($i['created_on']));
			$i['is_default_display'] = ($i['is_default']  ) ? 'Default' : 'Not Used';
		}
		$this->result->json($images);
    }
	

	public function json_getactiveorder()
	{
		$activeOrder = $this->prize_model->get_active_summary();
		
		//formatting
		foreach($activeOrder as &$item)
		{
			if($item['promo_qty']==0)
			{
				$item['promo_qty']   = "<span class='unavailable'>{$item['promo_qty']}</span>";
				$item['promo_price'] = "<span class='unavailable'>{$item['promo_price']}</span>";
				$item['promo_total'] = "<span class='unavailable'>{$item['promo_total']}</span>";
			}
			
			if($item['league_qty']==0)
			{
				$item['league_qty']   = "<span class='unavailable'>{$item['league_qty']}</span>";
				$item['league_price'] = "<span class='unavailable'>{$item['league_price']}</span>";
				$item['league_total'] = "<span class='unavailable'>{$item['league_total']}</span>";
			}
			//$item['qty'] = "<b>{$item['qty']}</b>";
			$total=0;
			$total +=(int)$item['promo_qty'] ;
			$total +=(int)$item['league_qty'] ;
			$item['qty'] = "<b>{$total}</b>";
		}
		
		$this->result->json($activeOrder);
	}
	
	public function json_getcategories()
	{
		
		//$org_id = (int)$this->input->post('org_id');
		$active_org = $this->permissions_model->get_active_org();
		
		if($this->permissions_model->get_active_org_type() == ORG_TYPE_LEAGUE)
		{
			//otherwise we need parent org id
			$assocs_org_id=$this->org_model->get_parent_org_id($active_org);
		}
		else
		{
			$assocs_org_id = $active_org;
			
		}
		
		
		
		$return = $this->prize_model->get_categories($assocs_org_id);
 		
 		if($this->input->get_post('combobox'))
			echo $this->result->json($return);//plain json
 		else
			echo $this->result->json_pag($return);//json for paginator
	}
	
	public function post_update_categories()
	{
		$user = $this->permissions_model->get_active_user();
		$category_id   = (int)        $this->input->get_post('category_id');
		$category_desc = rawurldecode($this->input->get_post('category_desc'));
		$category_name = rawurldecode($this->input->get_post('category_name'));

		echo $this->prize_model->update_category($user,$category_name,$category_desc,$category_id);
	}
	
	

    
    public function post_delete_category()
    {
    	$user=$this->permissions_model->get_active_user();
		$category_id = (int)$this->input->get_post('category_id');
		echo $this->prize_model->delete_category($category_id,$user);
    }
	public function post_delete_prize()
	{
    	$user=(int)$this->permissions_model->get_active_user();
		$prize_id = (int)$this->input->get_post('prize_id');
		echo $this->prize_model->delete_prize($prize_id,$user);
	}
	
	public function json_getprizesbycategory($category)
	{
		//get prizes
		$prizes = $this->prize_model->get_prizes_by_category($category);
		$sellable = array();
		//var_dump($prizes); return;
		$warehouses = $this->prize_model->get_warehouses();
		foreach($prizes as $k=>$prize)
		{
			
			$sizestring = array();
			$lowest = false;
			//examine quantity
			$qty = array(null,0,0,0,0,0,0,0,0,0,0);//available quantity
			$res = array(null,0,0,0,0,0,0,0,0,0,0);//find how many are reserved
			$reserved = $this->prize_model->get_reserved($prize['prize_id']);
			foreach($reserved as $row)
				$res[$row['size_id']] = $row['quantity'];
			//($res);
			
			foreach($warehouses as $wh)
			{
				//get total available over all warehouses
				$inventory = $this->prize_model->get_inventory($prize['prize_id'],$wh['warehouse_id']);
				foreach($inventory as $size)
				{
					//increase available
					  $qty[$size['size_id']] = $qty[$size['size_id']]+$size['quantity'] ;
				}				
			}//end warehouse loop
			//now we are done filling up the $qty array, so subtract those that are reserved
			foreach ($qty as $i=>$q)
				if($res[$i] != null)
					$qty[$i] = $qty[$i] - $res[$i];
			//next examine price, find lowest
			$prices = $this->prize_model->get_prices($prize['prize_id']);
			$available=0;
			foreach($prices as $size)
			{
				$forsale = array();
				if($size['cur1']>0) $forsale[] = (float)$size['cur1'];
				if($size['cur2']>0) $forsale[] = (float)$size['cur2'];
				
				if(count($forsale)>0)
				{
					$min = min($forsale);
					if(($lowest==false||$lowest>$min) and ($min!=0)) $lowest = $min;
				}
				//create sizestring
				if($size['is_active']=='t')
				{
					$sizestring[] = ($qty[$size['size_id']] > 0) ? "<span class='available'>{$size['size_abbr']}</span>" : "<span class='unavailable'>{$size['size_abbr']}</span>";
					$available+= $qty[$size['size_id']];//if they are active, they they are available
				}
								
			}//end prices loop
			$sizestring = implode(" | ", $sizestring);
			
			if($lowest!=false)
			{
				$sellable[] = array(
					'sku'		=>strtoupper($prize['sku']),
					'name'		=>$prize['name'],
					'sizestring'=>$sizestring,
					'available'	=>$available,
					'from'		=>'$'.money_format('%i',$lowest),
					'prize_id'	=>$prize['prize_id']
				);
			}
		}//end prizes loop
		
		$this->result->json($sellable);
		
	}
	
	

	public function json_getinventoryfororder($prize)
	{
		$return = array();
		
		$warehouses = $this->prize_model->get_warehouses();
		//we know there are 10 entries in lu_size table, so we start with zero available for each size
		//size id is in the range 1 to 10, not 0 to 9, so null in the first entry
		$qty = array(null,0,0,0,0,0,0,0,0,0,0);
		//$sizestring = array();
		//$lowest = false;
		//examine quantity
		foreach($warehouses as $wh)
		{
			//get total available over all warehouses
			$inventory = $this->prize_model->get_inventory($prize,$wh['warehouse_id']);
			foreach($inventory as $size)
			{	
				//find how many availble for each size , over all warehouses
				  $qty[$size['size_id']] += $size['quantity'];
			}				
		}//end warehouse loop
		
		$res = array(null,0,0,0,0,0,0,0,0,0,0);//find how many are reserved
		$reserved = $this->prize_model->get_reserved($prize);
		foreach($reserved as $row)
			$res[$row['size_id']] = $row['quantity'];
		foreach ($qty as $i=>$q)
				if($res[$i] != null)//and update availability accordingly
					$qty[$i] = $qty[$i] - $res[$i];
		
		//next examine price, 

		$prices = $this->prize_model->get_prices($prize);
		
		foreach($prices as $k=>$size)
		{
			if($size['is_active']!='t') continue;
			
			$class = ($qty[$size['size_id']]>0) ? 'available' : 'unavailable';
			
			$return[] = array(
				'size'		=>"<span class='$class'>{$size['size_abbr']}</span>",
				'quantity'	=>$qty[$size['size_id']],
				'cur1'		=>$size['cur1'],
				'cur2'		=>$size['cur2'],
				'size_id'	=>$size['size_id'],
				'order'		=>0,
				'prize_id'  => $size['prize_id'],
                'size_id'   =>$size['size_id']
			);
		}
		
		$this->result->json($return);
	}
	
	
	public function json_getsummarytotal()
	{
		$summary = $this->prize_model->get_active_summary_total();
		foreach($summary as &$item) $item['amount'] = money_format("%i", $item['amount']);	
		$this->result->json($summary);
	}

    public function json_getwarehouses()
    {
    	$org_id=$this->permissions_model->get_active_org();
        $result = $this->prize_model->get_warehouses($org_id);
        
        $this->result->json($result);
        
    }
    
    public function json_sizes()
    {
        $prize_id = (int)$this->input->get_post('prize_id');
        $sizes=$this->prize_model->get_sizes($prize_id);
		 $this->result->json_pag($sizes);
    }
    
    public function json_getwarehouseitems()
    {
        $warehouseid = $this->input->get_post('warehouse');
        $prizeid = $this->input->post('prize');
        $result = $this->prize_model->get_warehouseitems($warehouseid,$prizeid);
        
        $this->result->json($result);
    }
	
	// JSON-HTML /////////////////////////////////////////////////////////////////////////
	
	
	public function jsonhtml_categories()
	{
		$data['categories'] = $this->prize_model->get_categories();
		$json['status'] = 'SUCCESS';
		$json['html'] = $this->load->view('prize/order/categories',$data,true);
		$this->result->json($json);
	}
	
	
	public function jsonhtml_prizesbycategory()
	{
		$category = $this->input->get_post('category');
		$data['prizes'] = $this->prize_model->get_prizes_by_category($category);
		$json['status'] = 'SUCCESS';
		$json['html'] 	= $this->load->view('prize/order/category_prizes',$data,true);
		$this->result->json($json);
	}
	
	
	public function jsonhtml_prizedetails()
	{
		$prize = $this->input->post('prize');
		$data['prize'] = $this->prize_model->get_prize($prize);
		$json['status'] = 'SUCCESS';
		$json['html'] 	= $json['html'] = $this->load->view('prize/order/prize',$data,true);
		$this->result->json($json);
	}
	
	// FORM POST ////////////////////////////////////////////////////////////////////
	

	/**
	* insert prize
	* 
	*/
	public function post_prizedetails()
	{
		//var_dump($_FILES['prize_image']);return;
		$creator = $this->permissions_model->get_active_user();
		$owner   = $this->permissions_model->get_active_org();
		$prize_id = (int)$this->input->get_post('prize_id');
		$name= $this->input->post('name');
		$description= $this->input->get_post('description');
		$category_id= (int)$this->input->get_post('category_id');
		//if(!$category_id){$category_id=3;}//DEBUG ONLY
		$sku= $this->input->get_post('sku');
		$upc= $this->input->get_post('upc');
		$result['creator'] = $creator;
		//$details = array_values($this->input->post('details'));
		//list($prize_id,$category_id, $name, $description,  $sku, $upc) = $details;
		
		//var_dump($details); return $details;
		if(empty($category_id)||$category_id==-1)
		{
			$result['uploaded']='Category not found';
			$result['success']=false;
			
			$this->result->json($result);
			return;
		}
		if(empty($prize_id)||$prize_id==-1)
		{
			$insert=true;
			$prize_id = $this->prize_model->insert_prize_details(
					$name, $sku, $upc, $description, $creator, $category_id,$owner);
			$result['prize_id'] = $prize_id;
		//populate sizes tables with defaults
			$this->_default_sizes($prize_id);
			
		}
		else
		{
			//$result['uploaded']='prize found; TODO UDPATE';
			
			//$result['success']=false;
			$result['updated']=$this->prize_model->update_prize_details($prize_id, $name, $sku, $upc, $description, $creator,$category_id);
		}
		$form_image_location = 'prize_image';
    	//whether we are inserting or updating a prize, process uploaded image
		if($_FILES[$form_image_location]['size'])
		{
			
			//if(!eregi('image/', $_FILES['prize_image']['type'])) 
			if(!$this->images->type_is_valid_image($form_image_location) )
			{
     			 $result['uploaded'] ='The uploaded file is not an image, please upload a valide file!';
			}
			else
			{
				$uploaded = $this->prize_model->insert_prize_image($_FILES[$form_image_location], $prize_id, $creator,$owner);
				if($uploaded)
				{
					$this->prize_model->update_default_image($prize_id,$uploaded);//asset id
					$result['uploaded'] = "Image uploaded, confirmation: ".$uploaded;
				}
				else
				{
					$result['uploaded']='Error, ftp connection failed: '.$uploaded;
				}
			}
			
		}
		else
		{
			//no image was given, and thats ok
			$result['uploaded']='Prize saved without an image. ';
			//$result['raw']=$_FILES['prize_image'];
		}
		$result['success']=true;
		$this->result->json($result);
	}
	
	public function post_assign_default_image()
	{
		$prize_id = (int)$this->input->get_post('prize_id');
		$asset_id = (int)$this->input->get_post('asset_id');
		echo $this->prize_model->update_default_image($prize_id,$asset_id);
	}
	
	public function post_upload_image()
	{
		$creator = $this->permissions_model->get_active_user();
		$owner   = $this->permissions_model->get_active_org();
		$prize_id = (int)$this->input->get_post('prize_id');
		$thumb_type = $this->input->post('thumb_type');
		$file_id='filepath';//form field of file 
        $this->load->library('images');
		//
		if(!$this->images->type_is_valid_image($file_id) )
		{//invalid file
			$result['asset_id'] ='Invalid file, it is either too large, or it is not an image '
				.$_FILES[$file_id]['type']." , file: ".$_FILES[$file_id]['tmp_name'];
			$result['success']=false;
		}
		else
		{
			$valid=array('fill','crop');
			if(! in_array($thumb_type,$valid))
			{
				$result['asset_id']="Compression not supported: ".$thumb_type;
				$result['success']=false;
			}
			else
			{
				//upload insert , create thumbnail, all that good stuff
				$asset_id= $this->prize_model->insert_prize_image($_FILES[$file_id], $prize_id, $creator,$owner,$thumb_type);
				$result['asset_id']=$asset_id;
				$result['thumb_type']=$thumb_type;
				if(is_numeric($asset_id)&&$asset_id>0)
					{$result['success']=true;}
				else
					{$result['success']=false;}
			}
		}
		
		$this->result->json($result);
	}
	public function post_delete_asset()
	{
		$asset_id = (int)$this->input->get_post('asset_id');
		$user = $this->permissions_model->get_active_user();
		echo $this->prize_model->delete_asset($asset_id,$user);
	}
	/**
	* @author Sam
	* sets up initial sizes based on lu_size
	* for this prize
	* if no user/org passed in, uses active login
	* 
	* @param mixed $prize_id
	* @param mixed $user
	* @param mixed $org_id
	*/
	private function _default_sizes($prize_id,$user=false,$org_id=false)
	{
		if(!$user)$user  = $this->permissions_model->get_active_user();
		if(!$org_id)$org_id  = $this->permissions_model->get_active_org();
		
		$sizes=$this->prize_model->get_lu_sizes();
		foreach($sizes as $size)
		{
			//var_dump($size);
			//insert all default sizessize 
			$this->prize_model->insert_default_size($prize_id,$size['size_id'],$user,$org_id);
			
		}
	}
	
	/**
	* @deprecated
	* was custom method from YUI table  ->_oData
	* 
	*/
	public function post_updateqty()
	{
		$modifier = $this->permissions_model->get_active_user();
		$owner = $this->permissions_model->get_active_org();
		$prize = $this->input->post('prize');
        $warehouse=(int)$this->input->post('warehouse');//warehouse id
		$modifier = $this->permissions_model->get_active_user();
		$owner = $this->permissions_model->get_active_org();
		$prize = $this->input->post('prize');
        $warehouse=(int)$this->input->post('warehouse');//warehouse id
		$errors = array();
		$results = array();
		$all_records = json_decode($this->input->post('records'));
		foreach($all_records   as $i=>$record)
		{
			$r = $record->_oData;
			if($r->is_updated)
			{	
				//validate data
                //size_qty doesnt exist anymore: wh_qty used now
				if($r->size_insert < 0  and ($r->size_insert*-1)>$r->size_qty){ $errors[] = -100; continue; }
				if($r->cur1 < 0 or $r->cur2 < 0){ $errors[] = -200; continue; }
				
				//update active
				//$r->is_active = ($r->is_active=="YES") ? 't' : 'f';
				
				//update inventory
				$id = $this->prize_model->update_prize_inventory(
						$prize, $r->size_id, $r->size_insert, $modifier,$warehouse);
				if($id<0){ $errors[] = $id; continue; }
				
				//update prices
				//1 is cdn 2 is leaguedollars, from lu_currency_type
			//	$this->prize_model->update_prize_price($prize,$r->size_id, 1, $r->cur1, $modifier,$owner);
				//$this->prize_model->update_prize_price($prize,$r->size_id, 2, $r->cur2, $modifier,$owner);
                
               // if($id>0) $this->prize_model->update_warehouse_inventory($id,$warehouse,$r->size_insert);
			}
		}
		
		$errors = array_unique($errors);
		foreach($errors as $error)
		{
			switch($error)
			{
				case -100:
					$results[] = 'One or more of your inventory updates failed because you have removed more items from inventory than exist. Please enter a valid number and try again.';
					break;
				
				case -200:
					$results[] = 'Your price may not be below $0.00. ';
					break;
			}
		}
		
		$this->result->json($results);
	}
	
	
	public function post_updateprice()
	{
		$modifier = $this->permissions_model->get_active_user();
		$owner = $this->permissions_model->get_active_org();
		$prize = (int)$this->input->post('prize_id');
		$currency_id = (int)$this->input->post('currency_id');
		$size_id = (int)$this->input->post('size_id');
		$price = $this->input->post('price');
		$is_active = $this->input->post('is_active');
		//sometimes booleans are parsed to ints, so format for postgress
		
		if($is_active=='t' || $is_active===true ||$is_active===0)
			{$is_active='t';}
		else
			{$is_active='f';}
		
		if(!$price || !is_numeric($price))
		{
			echo -1;
			return;
		}
		$converted = number_format($price,2,'.','');
		echo $this->prize_model->update_prize_price($prize,$is_active,$size_id, $currency_id, $converted, $modifier,$owner);
       // $warehouse=(int)$this->input->post('warehouse');//warehouse id
       
       
       /*old methid based on specific YUI table structure is depreciated
		$errors = array();
		$results = array();
		foreach(json_decode($this->input->post('records')) as $i=>$record)
		{
			$r = $record->_oData;
			if($r->is_updated)
			{	
				//validate data
                //size_qty doesnt exist anymore: wh_qty used now
				//if($r->size_insert < 0 and ($r->size_insert*-1)>$r->wh_qty){ $errors[] = -100; continue; }
				//if($r->cur1 < 0 or $r->cur2 < 0){ $errors[] = -200; continue; }
				
				//update active
				$r->is_active = ($r->is_active =="YES") ? 't' : 'f';
				
				//update inventory
				//$id = $this->prize_model->update_prize_inventory(
				//		$prize, $r->size_id, $r->size_insert, $r->is_active, $modifier,$warehouse);
				//if($id<0){ $errors[] = $id; continue; }
				
				//update prices
				//1 is cdn 2 is leaguedollars, from lu_currency_type
				$this->prize_model->update_prize_price($prize,$r->is_active,$r->size_id, 1, $r->cur1, $modifier,$owner);
				$this->prize_model->update_prize_price($prize,$r->is_active,$r->size_id, 2, $r->cur2, $modifier,$owner);
                
               // if($id>0) $this->prize_model->update_warehouse_inventory($id,$warehouse,$r->size_insert);
			}
		}
		
		$errors = array_unique($errors);
		foreach($errors as $error)
		{
			switch($error)
			{
				case -100:
					$results[] = 'One or more of your inventory updates failed because you have removed more items from inventory than exist. Please enter a valid number and try again.';
					break;
				
				case -200:
					$results[] ='Your price may not be below $0.00. ';
					break;
			}
		}
		
		echo json_encode($results);*/
	}
	/**
	* DEPRECIATED: not used anymore: split into update_qty and update_price
	* inventory table no longer is linked to inventory_price, instead it is linked to warehouse.
	* and price table is linked to currency, size, and prize
	*/
	public function post_updateinventory()
	{//
		$user = $this->permissions_model->get_active_user();
		$owner = $this->permissions_model->get_active_org();
		$prize   = (int)$this->input->post('prize_id');
        $warehouse=(int)$this->input->post('warehouse_id');// id
        $quantity =(int)$this->input->post('quantity_change');// 
        //$inventory_id=(int)$this->input->post('inventory_id');// id
        $size_id=(int)$this->input->post('size_id');// id
		//$errors = array();
		//$results = array();
		
		echo $this->prize_model->update_inventory($prize, $size_id,$warehouse,$quantity,$user);
		
		
		
		/*old method: based on dumping a YUI table
		foreach(json_decode($this->input->post('records')) as $i=>$record)
		{
			$r = $record->_oData;
			if($r->is_updated)
			{	
				//validate data
                //size_qty doesnt exist anymore: wh_qty used now
				if($r->size_insert < 0 and ($r->size_insert*-1)>$r->wh_qty){ $errors[] = -100; continue; }
				if($r->cur1 < 0 or $r->cur2 < 0){ $errors[] = -200; continue; }
				
				//update active
				$r->is_active = ($r->is_active=="YES") ? 't' : 'f';
				
				//update inventory
				$id = $this->prize_model->update_prize_inventory(
						$prize, $r->size_id, $r->size_insert, $r->is_active, $modifier,$warehouse);
				if($id<0){ $errors[] = $id; continue; }
				
				//update prices
				//1 is cdn 2 is leaguedollars, from lu_currency_type
			//	$this->prize_model->update_prize_price($prize,$r->size_id, 1, $r->cur1, $modifier,$owner);
				//$this->prize_model->update_prize_price($prize,$r->size_id, 2, $r->cur2, $modifier,$owner);
                
               // if($id>0) $this->prize_model->update_warehouse_inventory($id,$warehouse,$r->size_insert);
			}
		}
		
		$errors = array_unique($errors);
		foreach($errors as $error)
		{
			switch($error)
			{
				case -100:
					$results[] = 'One or more of your inventory updates failed because you have removed more items from inventory than exist. Please enter a valid number and try again.';
					break;
				
				case -200:
					$results[] = 'Your currency may not be below $0.00. $0.00 is interpreted as not available.';
					break;
			}
		}
		*/
		//echo json_encode($results);
		
	}
	
	public function post_updateorder_details()
	{
		$order=(int)$this->input->post('order_id');
		$desc=rawurldecode($this->input->post('order_desc'));
		$date=rawurldecode($this->input->post('requested_date'));
		if(!$date){$date=null;}
		$user=$this->permissions_model->get_active_user();
		echo $this->prize_model->update_order($order,$desc,$date,$user);
		
	}
	/**
	* @author sam bassett
	* @access public
	* used by prize order window components. for leagues to order prizes. will optimize price for you
	* 
	*/
	public function post_updateorder()
	{
		//get session order array
		//$activeOrder = $this->prize_model->get_active_order();
		
		//get incoming order array
		$order_id = (int)$this->input->post('order_id');
		$prize_id = (int)$this->input->post('prize_id');
		$sizes = json_decode($this->input->post('order_sizes'),true);
		
		$user = $this->permissions_model->get_active_user();
		//get currencies
		$org=$this->permissions_model->get_active_org();
		$entity=$this->entity_model->get_entity_by_org($org);//assuming this is league
		$entity_id = $entity[0]['entity_id'];//
		$assoc_entity = $this->entity_model->get_entity_parent($entity_id);//but this is for assoc...
		$assoc_entity_id = $assoc_entity[0]['parent_id'];
		
		$real_cur=0;
		$fake_cur=0;
		$currencies = $this->finance_model->get_assigned_currencies($assoc_entity_id);
		
		if(isset($currencies[0])){$real_cur = $currencies[0]['type_id'];}
		if(isset($currencies[1])){$fake_cur = $currencies[1]['type_id'];}
		//
		//$WALLET = 500.25;//TODO get 
		
		$R_WALLET = $this->finance_model->get_org_wallet_balance($org,$real_cur);
		$F_WALLET = $this->finance_model->get_org_wallet_balance($org,$fake_cur);
		//echo "  WALLET $real_cur: ".$R_WALLET;
		//echo "  WALLET $fake_cur: ".$F_WALLET;
		//echo " \n <br/>";
		//ignoring this prize (since we are changing it) how much money has been used in this order so far, for each currency type
		$used_R_WALLET= $this->prize_model->get_used_order_currency($order_id,$real_cur,$prize_id);
		$used_F_WALLET= $this->prize_model->get_used_order_currency($order_id,$fake_cur,$prize_id);
		//
		
		
		//variables for greedy alg - simplex method would be better but this works too
		$cheap_pr =array();
		$cash_pr  =array();
		$cheap_qty=array();
		$cash_qty =array();
		
		$pr_ratio =array();
		
		$total_q=array();
		
		//$prize_id_fromsize=array();
		
		foreach($sizes as $size)
		{
			$size_id = $size['size_id'];
			$cash_qty[$size_id] =0;
			$cheap_qty[$size_id]=0;
			$qty = (int)$size['qty'];

			$rprice = $this->prize_model->get_size_prices_stock($size_id,$real_cur);
			$fprice = $this->prize_model->get_size_prices_stock($size_id,$fake_cur);
			
			if(!isset($rprice[0]) && !isset($rprice[0]) ) { continue; }
			$stock=0;
			if( isset($rprice[0]) ) 
			{ 
				$cash_pr[$size_id] = $rprice[0]['price'];	
				$stock = $rprice[0]['total_stock'];
				//$prize_id_fromsize[$size_id] = $rprice[0]['prize_id'];
			}
			else
			{
				$cash_pr[$size_id]='';
			}
				
			if( isset($fprice[0]) ) 
			{ 
				$cheap_pr[$size_id]  = $fprice[0]['price'];		
				$stock = $fprice[0]['total_stock'];//stock and prize id may be overdone, but better cover all our bases
				//$prize_id_fromsize[$size_id] = $fprice[0]['prize_id'];
			}
			else
			{
				$cheap_pr[$size_id]='';
			}
			if($qty < 0) 
				{ $qty=0; }
			if( $qty >  $stock ) //orderign between zero and available stock
				{ $qty = $stock;}	
				
			$total_q[$size_id] = 	$qty;
			if(!$qty){continue;}
			//echo "desired is #   ".$total_q[$size_id]. "       of   ".$size_id;


			//first find those that are only available in one price or the other and buy them
			//since these totals will not be entering phase 2 knapsack section, update them right away
			if(!$cash_pr[$size_id])
			{
				
				//so order all in nsa
				//but we cannot overspend the wallet, so the floor divide takes care of this
				$cheap_qty[$size_id] = min($total_q[$size_id],  floor($F_WALLET/$cheap_pr[$size_id]) );
				
				$total_q[$size_id] -= $cheap_qty[$size_id];//update how many stlil left to find
				
				$used_F_WALLET +=  $cheap_qty[$size_id] * $cheap_pr[$size_id];//
				//echo "blank CDN, spendNSA= ".$used_F_WALLET." still need".$total_q[$size_id]."<br/>";
				$this->prize_model->insert_orderitem($order_id,$prize_id,$size_id,
							$cheap_qty[$size_id],$cheap_pr[$size_id],$fake_cur,$user,$org);
			}
			
			if(!$cheap_pr[$size_id])
			{
				//order all with cash
				$cash_qty[$size_id] =$total_q[$size_id];
				$total_q[$size_id] -= $cash_qty[$size_id];//update how many stlil left to find
				
				$used_R_WALLET+=  $cash_qty[$size_id] * $cash_pr[$size_id];
				//echo "  blank NSA, spendcash=".$used_R_WALLET." still need".$total_q[$size_id]."<br/>";
				
			
				$this->prize_model->insert_orderitem($order_id,$prize_id,$size_id,
							$cash_qty[$size_id], $cash_pr[$size_id], $real_cur,$user,$org);
				
			}
			if($cash_pr[$size_id] && $cheap_pr[$size_id])
			{
				$pr_ratio[$size_id]	= $cheap_pr[$size_id] / $cash_pr[$size_id];
				//echo "calc ratio ".$pr_ratio[$size_id];
			}//otherwise ratio for this stays undefined, so will not be considered in phase 2 loop

			asort($pr_ratio);//DO NOT USE arsort, reverse will increase cost by hundreds of dollars
			
		}
		
		//now start phase2 loop		
		//http://en.wikipedia.org/wiki/Knapsack_problem#Greedy_approximation_algorithm
		foreach($pr_ratio as $size_id=>$r)
		{
			//echo 'ratio '.$pr_ratio[$size_id];
			
			//greedy knapsack algorithm:
			//firt pack in as many as we can of teh 'desired type'
			
			//but we cannot overspend the wallet, so the floor divide takes care of this
			$cheap_qty[$size_id] = min($total_q[$size_id],  floor(($F_WALLET-$used_F_WALLET)/$cheap_pr[$size_id]) );
			
			
			//echo "need ".$total_q[$size_id] ;
			$cash_qty[$size_id] =  $total_q[$size_id]- $cheap_qty[$size_id];
			$total_q[$size_id] = 0;
			
			$used_F_WALLET +=  $cheap_qty[$size_id] * $cheap_pr[$size_id];//
			$used_R_WALLET +=  $cash_qty[$size_id]  * $cash_pr[$size_id];
			
			
			
			//echo "  ordered  ".$cheap_qty[$size_id]. " of nsadol, ".$cash_qty[$size_id]." real <br/>";
			
			//echo "running totals are ".$used_F_WALLET." < ".$F_WALLET.' $'.$used_R_WALLET."<br/>";
			
			//final step is to update order quantities
			
			//$order_id  $user $org
			
			$this->prize_model->insert_orderitem($order_id,$prize_id,$size_id,$cheap_qty[$size_id],$cheap_pr[$size_id],$fake_cur,$user,$org);
			
			$this->prize_model->insert_orderitem($order_id,$prize_id,$size_id,$cash_qty[$size_id], $cash_pr[$size_id], $real_cur,$user,$org);
			
		}
		//echo "final totals are ".$used_F_WALLET." < ".$F_WALLET.' $'.$used_R_WALLET."<br/>";
		
		
		
		
		$used_R_WALLET= ($used_R_WALLET===null ) ? 0 : $used_R_WALLET; //convert null to zero
		$used_F_WALLET= ($used_F_WALLET===null ) ? 0 : $used_F_WALLET; //convert null to zero
		
		$_SESSION['temp_prize_order'] = array();
		$_SESSION['temp_prize_order']['CASH'] = $used_R_WALLET;
		$_SESSION['temp_prize_order']['FAKE'] = $used_F_WALLET;

		$this->result->json($_SESSION['temp_prize_order']);
		
		
		/*
		depreciated::
		foreach($order as $item)
		{
			$item->sku = $prize->sku;
			$item->name = $prize->name;
			$item->prize_id = $prize->prize_id;
			$item->size = strip_tags($item->size);
            
			$activeOrder[$item->prize_id][$item->size_id] = $item;
		}
		
		//save order
		
		$this->prize_model->set_active_order($activeOrder);
		$this->prize_model->set_active_summary(1680);*/
		
	}
    
    
    public function post_new_size()
	{
		$user = $this->permissions_model->get_active_user();
		$owner= $this->permissions_model->get_active_org();
		$org_id=$owner;//association id
		$name   = rawurldecode($this->input->post('size_name'));
		$abbr   = rawurldecode($this->input->post('size_abbr'));
		$prize_id   = (int)$this->input->post('prize_id');
		$size_id   = (int)$this->input->post('size_id');
		
		//if($name == '' || $name == 'null') $cdesc = null;
		
		//if($abbr == '' ||  $abbr == 'null' ||$abbr == null) {echo -1;return;}
		
		if($size_id && $size_id>0)
		{
			echo $this->prize_model->update_size($size_id,$user,$name,$abbr);
		}
		else
		{
			echo $this->prize_model->insert_size($prize_id,$user,$owner,$name,$abbr);
		}
		
		
	}
	
	
	public function post_delete_size()
	{
		$user = $this->permissions_model->get_active_user();
		//$owner= $this->permissions_model->get_active_org();
		$size_id    = (int)$this->input->post('size_id');
		$prize_id   = (int)$this->input->post('prize_id');
		
		
		echo $this->prize_model->delete_size($prize_id,$size_id,$user);
		
	}
    
    public function post_new_category()
	{
		echo "starting post_new_category..................";
		
		$creator = $this->permissions_model->get_active_user();
		$owner   = $this->permissions_model->get_active_org();
		$org_id=$owner;//association id
		$cname   = rawurldecode($this->input->post('category_name'));
		$cdesc   = rawurldecode($this->input->post('category_desc'));
		 
		if($cdesc == '' || $cdesc == 'null') $cdesc = null;
		
		if($cname == '' ||  $cname == 'null' ||$cname == null) {echo -1;return;}
		
		echo $this->prize_model->insert_category($creator,$owner,$cname,$cdesc,$org_id);
	}
    
	public function post_delete_warehouse()
	{
		$creator = $this->permissions_model->get_active_user();
		$wid   = (int)$this->input->post('warehouse_id');
		echo $this->prize_model->delete_warehouse($wid,$creator);
		
	}
	public function post_update_warehouse()
	{
		$creator = $this->permissions_model->get_active_user();
		$wname   = rawurldecode($this->input->post('warehouse_name'));
		$wdesc   = rawurldecode($this->input->post('warehouse_desc'));
		$wid   = (int)$this->input->post('warehouse_id');
		echo $this->prize_model->update_warehouse($wid,$wname,$wdesc,$creator);
	}
	
    public function post_new_warehouse()
    {
		$creator = (int)$this->permissions_model->get_active_user();
		$owner   = (int)$this->permissions_model->get_active_org();
		$wname   = rawurldecode($this->input->post('warehouse_name'));
		$wdesc   = rawurldecode($this->input->post('warehouse_desc'));
		//$wfav    = $this->input->post('wfav');
		
		if($wdesc == '' || $wdesc == 'null') $wdesc = null;
		
		if($wname == '' ||  $wname == 'null' ||$wname == null) return;
		
		echo $this->prize_model->insert_warehouse($creator,$owner,$wname,$wdesc);
		
		
    }
    /*
    public function json_getwarehouses()
    {
		
		
    }*/
    
    /**
    * create a new order with no items
    * 
    */
    public function post_create_order()
    {
		$desc = rawurldecode($this->input->post('order_desc'));
		$date = rawurldecode($this->input->post('requested_date'));
		if($date == '' || $date == "null") $date = null;
		if($desc == '' || $desc == "null") $date = null;
		
		$creator = (int)$this->permissions_model->get_active_user();
		$owner = (int)$this->permissions_model->get_active_org();
		
		$orderid = $this->prize_model->insert_order($creator,$owner,$date,$desc);
		
		
		echo $orderid;
    }
    
    public function post_delete_order()
    {
        $orderid = (int)$this->input->post('order_id');
		$user = (int)$this->permissions_model->get_active_user();
        
        echo $this->prize_model->delete_order($orderid,$user);
		
    }
    
    
    public function post_saveorder()
    {//save order with lowest # status, insert order and order items
        //get data from AJAX post function
        $items  = json_decode(rawurldecode($this->input->post('items')),true);
        $totals = json_decode(rawurldecode($this->input->post('totals')),true);
        $desc = rawurldecode($this->input->post('desc'));
        $date = rawurldecode($this->input->post('date'));
		if($date == '' || $date == "null") $date = null;
		if($desc == '' || $desc == "null") $date = null;
        //find who is logged in and doing this
        $creator = (int)$this->permissions_model->get_active_user();
		$owner = (int)$this->permissions_model->get_active_org();
        $orderid = -1;//assuming -1 is never a PK that gets returned ever
        $orderid = $this->prize_model->insert_order($creator,$owner,$date,$desc);
       // echo $orderid;
        if($orderid == -1)
        {            
        	echo -1;
       		return;
		}
        //loop on $items, insert each one
        foreach($items as $item)
        {
            //first remove html tags that are stored in the table: <b></b> or span, etc
            //so far only found in qty, and price fields
            /*
            $in = $item['qty'];
            $out = str_replace("<b>","",$in);
            $out = str_replace('</b>',"",$out);
            $item['qty']=(int)$out;
            */
            $in = $item['promo_qty'];
            $out = str_replace("<span class='unavailable'>","",$in);
            $out = str_replace("<span class='available'>","",$out);
            $out = str_replace('</span>',"",$out);
            $item['promo_qty']=(int)$out;
            
            $in = $item['promo_price'];
            $out = str_replace("<span class='unavailable'>","",$in);
            $out = str_replace("<span class='available'>","",$out);
            $out = str_replace('</span>',"",$out);
            $item['promo_price']=$out;
        
            $in = $item['promo_total'];
            $out = str_replace("<span class='unavailable'>","",$in);
            $out = str_replace("<span class='available'>","",$out);
            $out = str_replace('</span>',"",$out);
            $item['promo_total']=$out;
            
            $in = $item['league_qty'];
            $out = str_replace("<span class='unavailable'>","",$in);
            $out = str_replace("<span class='available'>","",$out);
            $out = str_replace('</span>',"",$out);
            $item['league_qty']=$out;
            
            $in = $item['league_price'];
            $out = str_replace("<span class='unavailable'>","",$in);
            $out = str_replace("<span class='available'>","",$out);
            $out = str_replace('</span>',"",$out);
            $item['league_price']=$out;
            
            //now insert this row into the order_item table
      		$item['prize_id']=(int)$item['prize_id'];
             $order_item_id = $this->prize_model->insert_order_item($item,$orderid,$creator,$owner);
             //it appears this id is not used
        }
        //now insert totals
        foreach($totals as $key=>$value)
        {
            //FROM lu_order_currency table:
            $sub=1;
            $tax=2;
            $ship=3;
            $ldu=4;
            $due=5;

            switch ($key)
            {
                case "sub":
                    $currency=$sub;
                break;
                case "tax":
                    $currency=$tax;
                break;
                case "ship":
                    $currency=$ship;
                break;
                case "ldu":
                    $currency=$ldu;
                break;
                case "due":
                    $currency=$due;
                break;
                default:
                $currency=-1;
                break;
            }
            if($currency === -1)
                return;
            $id = $this->prize_model->insert_order_currency($currency,$value,$orderid);
            //$id not used
        }//end totals foreach
        echo $id;
        //unset all
        $_SESSION['prize_order'] = null;
        unset($_SESSION['prize_order']);
        $_SESSION['prize_order_summary']=null;
        unset($_SESSION['prize_order_summary']);
        
    }//end fn
    
    
     
    public function json_getmyorders()
    {//only orders CRETAED BY the logged in user
    //for more general overfview the manage ordres is used
        
        $result = $this->prize_model->get_myorders($this->permissions_model->get_active_user());
        $this->result->json($result);
    }
    
    /**
    * this gets all orders in the system (that are available to the loggged in user)
    * : use json_getordersbystatus for most windows, and supply the array 
    */
    public function json_getorders()
    {        
        $assoc_org_id= $this->permissions_model->get_active_org();
        $org_type=$this->permissions_model->get_active_org_type();
        
        if($org_type == ORG_TYPE_LEAGUE)
        {
        	//active is league so get assoc org id
			$assoc_org_id = $this->org_model->get_parent_org_id($assoc_org_id);
        }
        
        $filter = $this->input->get_post('status_ids');
        
        if($filter)
        {
			$status_ids = json_decode($filter);
			$result=array();
			foreach($status_ids as $id)
			{
				$id=(int)$id;//addeded for IE
				$result = array_merge($result,$this->prize_model->get_orders_bystatus($assoc_org_id,$id));				
			}
        }
        else
        {
			$result = $this->prize_model->get_orders($assoc_org_id);        
        }
        
        $fancy="F j, Y";
        foreach($result as &$r)
        {
        	$d = $r['requested_date'];
        	if($d)//if date is null, do not format, or else we get unix epoch start date
				$r['requested_date'] = date($fancy,strtotime($d));
        }
        
        //echo json_encode($result);
         $this->result->json_pag($result);
    }
    
    /**
    * Get all orders with the one of the given status, from lu_order_status
    * input is an array of ids
    */
    public function json_getordersbystatus()
    {
    	$status_list = json_decode($this->input->post('status_list'));
    	$results = array();
    	foreach($status_list as $status)
    	{
    		$orders = $this->prize_model->get_orders_bystatus($status);
    		if($orders != null)//if orders is empty array, this is no problem
    	   		$results = array_merge(  $orders, $results);
		}
    	// ?? c=use array_unique(array_merge(a,b));
        $this->result->json($results);
    }
    
    
    ###{"Order":[1,1,1,0]}
    public function post_updateorderitems()
    {
        $items = json_decode(urldecode($this->input->post('items')));
        
		//todo: fix this. order item has league_qty and promo_qty only
        $user =  $this->permissions_model->get_active_user();
       // var_dump($items);
        foreach($items as $row)
        {
            $item = $row->_oData;
            if($item->old_qty != $item->qty && $item->qty <= $item->available)//if there is an valid change
            {	var_dump($item);
                echo $this->prize_model->update_orderitem($item->item_id,$item->qty,$user,$item->order_id);
			}
        }
       // echo $items;
    }
    /**
    * insert or update
    * 
    */
    public function post_order_item()
    {
		$currency_id = (int) $this->input->get_post('currency_id');
		$price       =       $this->input->get_post('price');
		$prize_id    = (int) $this->input->get_post('prize_id');
		$order_id    = (int) $this->input->get_post('order_id');
		$size_id     = (int) $this->input->get_post('size_id');
		$qty         = (int) $this->input->get_post('qty');
        $owner = (int)$this->permissions_model->get_active_org();
        $user = (int)$this->permissions_model->get_active_user();
		
		echo $this->prize_model->insert_orderitem($order_id,$prize_id,$size_id,$qty,$price,$currency_id,$user,$owner);
		

		
    }
    /*
    public function json_order_items()
    {
		
        $orderid = (int)$this->input->post('order_id');
        
        $result = $this->prize_model->get_orderitems($orderid);
        
        echo $this->result->json_pag($result);
    }*/
    public function json_getorderitems()
    {
       
        $orderid = (int)$this->input->get_post('order_id');
        $dec=2;
        $dp='.';
        $ts=',';
        $result = $this->prize_model->get_orderitems($orderid);
        //further reduce how many are available based on the reserved_item table:
        //also do not show if qty = zero
        $nonzero = array();
        foreach($result as &$item)
        {
        	if(!$item['qty']) { continue ;}
        	/* //instead this is now found within the query, stored as 'reserved' key seperately\
        	//the rest is UI choice
			$reserved = $this->prize_model->get_reserved($item['prize_id']);
			$res = array(null,0,0,0,0,0,0,0,0,0,0);//find how many are reserved
			foreach($reserved as $row)
				$res[$row['size_id']] = $row['quantity'];			
			$result[$i]['available'] = $result[$i]['available'] - $res[$item['size_id']];
			*/
			//$result[$i]['custom_group_by'] = $item['name']." SKU:".$item['sku']." UPC:".$item['upc'];
			$item['price'] = $item['html_character']." ".number_format($item['price'],$dec,$dp,$ts);
			$nonzero[]=$item;
			//$result[$i]['promo_price'] = '$ '.number_format($result[$i]['promo_price'],$dec,$dp,$ts);
        }

         
         $this->result->json_pag($nonzero);
    }
    
    /**
    * similar to get orderitems, except we include sizes/prices that havent been ordered yet.
    * good for a blank order so items can be added
    * 
    */
    public function json_prize_size_orderitems()
    {
    	$org=$this->permissions_model->get_active_org();
		$entity=$this->entity_model->get_entity_by_org($org);//assuming this is league
		$entity_id = $entity[0]['entity_id'];//
		$assoc_entity = $this->entity_model->get_entity_parent($entity_id);//but this is for assoc...
		$assoc_entity_id = $assoc_entity[0]['parent_id'];
		$currencies = $this->finance_model->get_assigned_currencies($assoc_entity_id);
		$type_ids=array();
		
		foreach ($currencies as $c)
		{
			$type_ids[]=$c['type_id'];
		}
        $order_id = (int)$this->input->get_post('order_id');
        $prize_id = (int)$this->input->get_post('prize_id');
        $sizes = $this->prize_model->get_prize_size_orderitems($order_id,$prize_id,$type_ids);
        //price formatting:
        $dec=2;
        $dp='.';
        $ts=',';
        //next job is to show only one record per size
        $index_by_size = array();
        foreach($sizes as &$size)
        {
        	$sid = $size['size_id'];
        	
        	
        	if($size['cur1_price'])
				{$size['cur1_price'] =/* '$ '.*/number_format($size['cur1_price'],$dec,$dp,$ts);}
			if($size['cur2_price'])
				{$size['cur2_price'] =/* '$ '.*/number_format($size['cur2_price'],$dec,$dp,$ts);}
			if($size['cur3_price'])
				{$size['cur3_price'] =/* '$ '.*/number_format($size['cur3_price'],$dec,$dp,$ts);}
			
			if($size['cur1_total'])
				{$size['cur1_total'] =/* '$ '.*/number_format($size['cur1_total'],$dec,$dp,$ts);}
			if($size['cur2_total'])
				{$size['cur2_total'] =/* '$ '.*/number_format($size['cur2_total'],$dec,$dp,$ts);}
			if($size['cur3_total'])
				{$size['cur3_total'] =/* '$ '.*/number_format($size['cur3_total'],$dec,$dp,$ts);}
			
			if(!$size['total_stock']) {$size['total_stock']=0;}//null means zero
			
			$size['qty']=0;//user input
        }
		$this->result->json($sizes);
    }
    
    public function json_getordercurrency()
    {
       
        $orderid = $this->input->post('orderid');
        
        $result = $this->prize_model->get_ordercurrency($orderid);
        $num_dec=2;
        $dec_pt=".";
        $thous_sep=",";
        foreach($result as &$row)
        {
			$row['value'] = number_format($row['value'],$num_dec,$dec_pt,$thous_sep);
			$row['value'] = '$ '.$row['value'];
        }
        
        
        $this->result->json($result);
    }
    
    public function post_update_orderstatus()
    {
        $orderid = $this->input->post('order_id');
        $status  = $this->input->post('status_id');
        if(!$status || !$orderid) { echo 'Invalid parameters';return;}
		$return = array();
        echo  $this->update_order_status($orderid,$status);
        $owner = (int)$this->permissions_model->get_active_org();
        $user = (int)$this->permissions_model->get_active_user();
        if($status == 2)
        {
        	//status chaned from 'created' to 'confirmed', by the order creator
			//then user has confirmed the order, not shipped but yet we must reserve all of these items
			
			//echo  $this->reserve_orderitems($orderid,true);
			//echo json_encode($return);
        }
        if($status == 4)
        {
        	//order changed from verified to picked by shipper. any qty changes have already been saved
        	//and reflected in the db
			// so we must update the inventory table by removing each item from warehouse
			
			echo $this->update_inventory_findwarehouses($orderid);
			
			//also removing from reserved_item
			//false means subtract
			//echo $this->reserve_orderitems($orderid,false);
			
        }
        if($status == 6 || $status == 5)  
        {//if new status is 'shipped' or 'ready for pickup'
			//TODO: create invoice for all of order without shipping
			
        	$invoice_to= $owner;
        	//$results = $this->prize_model->get_ordercurrency($orderid); 

        	//ids from lu_order_currency
        	//$shippingmin = 20.00;
			//$shippingmax = 100.00;

	        $results = $this->prize_model->get_orderitems($orderid);        
	       // $shippingamount = 0.00;
	        $totals=array();
	        foreach($results as $item)
			{
				$curr_id = (int)$item['currency_type_id'];
				if(!isset($totals[$curr_id])) {$totals[$curr_id]=0;}
				
				$price =  (float)$item['price'];
				$qty   =  (int)  $item['qty'];
				$totals[$curr_id]    += $price*$qty;

			}
			
			
		     
			return;//TODO: create invoice 
			//TODO -p 2 -o Bradley -c PRIZES: Change defualt shipping/tax rates based on user
			$taxrate = 0.12;
			//$shippingrate = 0.04;
			$taxes = $cdn * $taxrate;
			$desc="Total Due for Shipped Order";
        	$invoice_id= $this->prize_model->insert_invoice($user,$invoice_to,$desc,  $owner);        
        	
	        if($invoice_id <= 0 ) 	return;
			$this->prize_model->update_order_invoice($orderid,$invoice_id);
			//there will be only one invoice item for each chargeand hence only one entity charge
			//from lu_currency_type
			$cdn_type=1;
			$lgd_type=2;
			$order_type=4;//prize order: from lu_charge_type
			$tax_type=5;
			$league_org_type=3;//from lu_org_type
			//get entity id from $owner 

			
	       $entity=$this->entity_model->get_entity_by_org($owner);
	       $entity_id=(int)$entity[0]['entity_id'];
		   
		   
	       $desc = "Prize Order Cdn";
	       $ec_id = $this->prize_model->insert_entity_charge($entity_id,$league_org_type,$order_type,$cdn,$cdn_type);
	       $itemid=$this->prize_model->insert_invoice_item($invoice_id,$ec_id,$desc);
		   $desc = "Prize Order League Dollars";
	       $ec_id = $this->prize_model->insert_entity_charge($entity_id,$league_org_type,$order_type,$league_dollars,$lgd_type);
	       $itemid=$this->prize_model->insert_invoice_item($invoice_id,$ec_id,$desc);
	       $desc = "Prize Order Taxes";
	       $ec_id = $this->prize_model->insert_entity_charge($entity_id,$league_org_type,$tax_type,$taxes,$cdn_type);
	       $itemid=$this->prize_model->insert_invoice_item($invoice_id,$ec_id,$desc);
	       
        	echo $invoice_id;
        }  
    }
    
    private function reserve_orderitems($order_id,$increase)
    {
    	//either increase or decrease the quantity
    	if($increase) $m = 1;
    	else          $m = -1;
    	
    	
    	
		$results = '';
		$items = $this->prize_model->get_orderitems($order_id);
		
		foreach($items as $item)
		{
			$qty_change = $m*$item['qty'];
			//($qty_change);
			//if any result is < 0, then an error was found
			$results .= $this->prize_model->update_reserved_item($item['prize_id'],$item['size_id'],$qty_change  );
			
		}
		
		return $results;
		
    }
    
    
    private function update_inventory_findwarehouses($orderid)
    {
    	$order_id = $orderid;//just in case im using both??
		//assume shipper works for association not league
		$results = '';
		$user = $this->permissions_model->get_active_user();
		$org  = $this->permissions_model->get_active_org();
		$whs  = $this->prize_model->get_warehouses_forassoc($org);
		$items = $this->prize_model->get_orderitems($order_id);
		
		foreach($items as $item)
		{
			//$findDefault=true;
			$prize = $item['prize_id'];
			$size_id = $item['size_id'];
			$orderitem_qty = $item['qty'];
			//if($findDefault)
			foreach($whs as $wh)
			{
				
				$wid = $wh['warehouse_id'];
				//first find default wh,
				if($wh['is_default'] == 't')
				{
					//now, how many items are available at this location?
					
					
					$inv = $this->prize_model->get_quantity($prize,$size_id,$wid);
					$avail_qty = $inv[0]['quantity'];					
					if($avail_qty == 0) break;//nothing to do at this warehouse, move along
					
					if($orderitem_qty > $avail_qty  )
					{
						//then take ALL from this warehouse
						$results .= $this->prize_model->update_inventory(
							$prize,$size_id,$wid,(-1)*$avail_qty,$user);						
						$orderitem_qty = $orderitem_qty - $avail_qty;						
					}
					else
					{
						//this warehouse has enough for the entire item
						$results.=$this->prize_model->update_inventory(
							$prize,$size_id,$wid,(-1)*$orderitem_qty,$user);					
						$orderitem_qty = 0;						
					}
					break;//done with default wh, so end this look and go look into non_defaults
				}//end if default
				
			}//end first wh loop
			if($orderitem_qty > 0)//do we need to look in other warehouses
			foreach($whs as $wh)
			{
				$wid = $wh['warehouse_id'];
				//now do the same for every warehouse that is NOT  default, in any order
				if($wh['is_default'] != 't')
				{
					//now, how many items are available at this location?
					$inv = $this->prize_model->get_quantity($prize,$size_id,$wid);
					$avail_qty = $inv[0]['quantity'];					
					if($avail_qty == 0) break;//nothing to do at this warehouse, move along
					
					if( $orderitem_qty > $avail_qty  )
					{
						//then take ALL from this warehouse
						$results.=$this->prize_model->update_inventory(
							$prize,$size_id,$wid,(-1)*$avail_qty,$user);						
						$orderitem_qty = $orderitem_qty - $avail_qty;			
					}
					else
					{
						//this warehouse has enough for the entire item
						$results.=$this->prize_model->update_inventory(
							$prize,$size_id,$wid,(-1)*$orderitem_qty,$user);					
						$orderitem_qty = 0;
						break;//we are done with this item, stop hunting thru warehouses and process the next order item				
					}
					
				}//end if not default
			}//end second wh loop

		}//end main items loop
		return $results;
    }// end Fn
    
    private function update_order_status($orderid,$status)
    {
        //status must be a valid integer from prize.lu_order_status table
        $user =  $this->permissions_model->get_active_user();
        return $this->prize_model->update_orderstatus($orderid,$status,$user);
    }
    
    

    
    public function json_getinvoices()
    {
        $invoiceto = $this->input->post('invoiceto');
        
        $result  = $this->prize_model->get_invoices($invoiceto);
        /*
        foreach($result as &$row)
        {
			$row['invoice_amount'] = '$ '.$row['invoice_amount'];
			$row['amount_owing'] = '$ '.$row['amount_owing'];
			$row['amount_paid'] = '$ '.$row['amount_paid'];
			
			
        }*/
        
        $this->result->json($result);
        
    }
    public function json_getinvoiceitems()
    {
        $invoice = $this->input->post('invoice');
        $this->result->json($this->prize_model->get_invoiceitems($invoice));
        
    }
    
    
    public function post_shipinvoice()
    {
    	$owner = (int)$this->permissions_model->get_active_org();
        $user =  (int)$this->permissions_model->get_active_user();
        $orderid=$this->input->post('order_id');
        //$invoice_to= $this->input->post('invoiceto');
        //$createdby = $this->input->post('createdby');
        //first get shipping total for this order thru the model
        //TODO: do not use order currency here, recalculate raw shipping based 
        //on total league prices and such 
        
        $cdn = 0.00;
		$league_dollars = 0.00;
		//TODO -p 2 -o Bradley -c PRIZES: Change defualt shipping/tax rates based on user
		$taxrate = 0.12;
		$shippingrate = 0.04;
		
		$shippingmin = 20.00;
		$shippingmax = 100.00;
        
        $results = $this->prize_model->get_orderitems($orderid);        
        $shippingamount = 0.00;
        foreach($results as $item)
		{
			$price =  (float)$item['promo_price'];
			$qty   =  (int)$item['promo_qty'];
			$cdn    += $price*$qty;
			$price =  (float)$item['league_price'];
			$qty   =  (int)$item['league_qty'];
			$league_dollars    += $price*$qty;
		}
         /*      
        foreach($results as $curr)
        {
            if($curr["currency_id"] == 3) //id of shipping type from lu_
            {
                $shipping = (int) $curr["value"];                
            }                                
        }*/
        //if($shipping == 0) return;
        //now create an invoice for this amount
        //$total = $promo + $league;
		
		//calc taxes
		$taxes = $cdn * $taxrate;
		
		//calc shipping
		$shippingamount = $cdn * $shippingrate;
		if($shippingamount <= $shippingmin) $shippingamount = $shippingmin;
		if($shippingamount >= $shippingmax) $shippingamount = $shippingmax;
		
		//due
		//$due = $promo + $taxes + $shippingamount;
		//echo "ship amt ".$shippingamount;
        //now we have the invoice id, so insert invoice_item
        //assuming entity charge
        $desc="Shipping for Verified Prize Order";
        //$invoice_id= $this->prize_model->insert_invoice($user,$owner,$desc,$owner);        
        
        if($invoice_id || $invoice_id <= 0 ) 
        {
        	echo $invoice_id;//if error
			return;
		}
       //there will be only one invoice item and hence only one entity charge
       $cdn_type=1;//from lu_currency_type
       $ship_type=3;//shipping: from lu_charge_type
        $league_org_type=3;//from lu_org_type
       //get entity id from $owner 

       $entity=$this->entity_model->get_entity_by_org($owner);

       $entity_id=(int)$entity[0]['entity_id'];

       $ec_id = $this->prize_model->insert_entity_charge($entity_id,$league_org_type,$ship_type,$shippingamount,$cdn_type);
       $itemid=$this->prize_model->insert_invoice_item($invoice_id,$ec_id,$desc);
       $this->prize_model->update_order_invoice($orderid,$invoice_id);

        echo $invoice_id;
        
    }
    
    
    public function json_order_status()
    {
    	//get lookup table
		echo json_encode($this->prize_model->get_order_status());
    }
	public function json_role_order_status()
	{
		
    	$owner = (int)$this->permissions_model->get_active_org();
        $user  = (int)$this->permissions_model->get_active_user();
        $from  = (int)$this->input->get_post('status');
        $can = $this->prize_model->get_role_order_status($user,$owner,$from);
        
		$this->result->json($can);
	}
    
    
    private function _category_names($org_id)
    {
		$names=array();
    	$existing_categories = $this->prize_model->get_categories($org_id);
    	foreach($existing_categories as $c)
    	{
			$names[$c['category_id']]=$c['category_name'];
    	}
    	return $names;
    }
    private function _warehouse_names($org_id)
    {
		$names=array();
    	 
    	$existing_warehouses = $this->prize_model->get_warehouses_forassoc($org_id);
    	foreach($existing_warehouses as $w)
    	{
			$names[$w['warehouse_id']]=$w['warehouse_name'];
    	}
    	return $names;
    }
    private function _prize_names($org_id)
    {
    	$names=array();
    	$pr=$this->prize_model->get_prizes($org_id);
		foreach($pr as $p)
    	{
			$names[$p['prize_id']]=$p['name'];
    	}
    	return $names;
    }
    private function _curr_names($entity_id)
    {
    	$names=array();
		$curr=$this->finance_model->get_entity_and_parent_currencies($entity_id);    	 
		foreach($curr as $p)
    	{
			$names[$p['type_id']]=$p['currency_abbrev'];
    	}
    	return $names;
    }
    /**
    * @deprecated
    * white space has no issues
    * 
    * @param mixed $config
    * @param mixed $path
    * @param mixed $remotefile
    * @return mixed
    */
    private function _handle_ftp_whitespace($config,$path,$remotefile)
    {
    	return $remotefile;
    	//for some reason our json_decode did not handle this
    	if(!strstr($remotefile,'%20')) {return $remotefile;}
    	//echo "handle whitespace on : $remotefile";
    	$remotefile = str_replace('%20',' ',$remotefile);

		//try to rename
		$new=$remotefile;

		return $new;
    }
    
    
    private function _find_size_id($prize_id,$size_abbr)
    {
		$sizes=$this->prize_model->get_sizes($prize_id);
		foreach($sizes as $s)
		{
			if($s['size_abbr'] == $size_abbr)
				return $s['size_id'];
		}
 
		return -1;
    }
    
    /**
    * send prizes from another site, pre formatted, to be inserted here
    * 
    */
    public function curl_receive_external_prizes()
    {			 
		echo "start....";
		//get post data
        $user    = (int)$this->input->get_post('user_id');
        $org_id  = (int)$this->input->get_post('org_id');
        $assoc_entity_id = $this->org_model->get_entity_id_from_org($org_id);
		$prizes=json_decode( rawurldecode($this->input->get_post('prizes') ),true);
		$warehouses=json_decode(rawurldecode($this->input->get_post('warehouses') ),true);
		$currencies=json_decode(rawurldecode($this->input->get_post('currencies') ),true);
 
        //declare all variables
 
 		//NSA FTP
    	$config=array();
    	$config['hostname'] = 'ftp.nsacanada.ca';
		$config['username'] = 'sam@nsacanada.ca';
		$config['password'] = 'j1135#weep';
		//$config['debug'] = TRUE;//remove this line later
		
    	$this->load->library('ftp');
		$url_file_sep='/';
		$time = date('Ymdhis');
		$temp_name=$name = tempnam('/tmp','prize_image');
		$RESET_QTY=-99999;
		
		//get existing data for this org       
		
		//handle currencies
        $existing_currencies = $this->_curr_names($assoc_entity_id);
        foreach($currencies as $c)
        {
			if(!in_array($c['abbr'],$existing_currencies))
			{
				$cur_id= $this->finance_model->update_lu_currency(0,$c['name'],$c['abbr'],$c['desc']
					,$c['html'],$c['icon'],$assoc_entity_id);

			}
        }
        //get them again
        $existing_currencies = $this->_curr_names($assoc_entity_id);
 
        
        $currency_str_to_id = array_flip($existing_currencies);
        
        //handle warehouses
        $existing_warehouses = $this->_warehouse_names($org_id);
        $wh_str_to_id = array_flip($existing_warehouses);
        
 		
 		//first handle warehouses
 		
 		$default_wh=-1;
 		foreach($warehouses as $wh)
 		{
 				
			if(!in_array($wh['name'],$existing_warehouses))
			{
				$wh_id=$this->prize_model->insert_warehouse($user,$org_id,$wh['name'],$wh['desc'],$wh['default']);
			}
			else
			{
				$wh_id=$wh_str_to_id[$wh['name']];
			}
 			if($wh['default']=='t')$default_wh=$wh_id;
 
 		}
 		
 		
 		
        $existing_categories = $this->_category_names($org_id);
        $category_str_to_id = array_flip($existing_categories);
        
        $existing_prizes = $this->_prize_names($org_id);
        $prize_str_to_id = array_flip($existing_prizes);
        
		$new_prizes_created=0;
		foreach($prizes as $prize)
		{ 
			$is_new=false;
			//var_dump($prize);break;
			$prize_id=false;
			if(in_array($prize['prize_name'],$existing_prizes)) 
			{
				$prize_id=$prize_str_to_id[$prize['prize_name']];
			}
			
			
			$cat=$prize['category']; 
			if(!$cat) $cat='No Category';
			
			if(!in_array($cat,$existing_categories))
			{
				//create category if it doesnt exist
				 
				$cat_id=$this->prize_model->insert_category($user,$org_id,$cat,null,$org_id);
				$existing_categories[$cat_id]=$cat;
				$category_str_to_id[$cat]=$cat_id;
		 
			}
			else
			{
				//echo "category already exists ".$cat."<br/>";
				$cat_id=$category_str_to_id[$cat];
			}
			$prize['category_id']=$cat_id;
			
			if(!$prize_id)
			{//if prize is new, not existing, insert it now that we have category
				$prize_id=$this->prize_model->insert_prize_details(
					$prize['prize_name']
					,$prize['sku']
					,$prize['upc']
					,$prize['description']
					,$user
					,$prize['category_id']
					,$org_id);
				$new_prizes_created++;	
				$is_new=true;
				$this->_default_sizes($prize_id,$user,$org_id);	
			} 
			
			
			//loop on sizes array, to handle inventory levels
			//do this REGARDLESS of $is_new true or false.
			foreach($prize['sizes'] as $s)if($s['size'])//if not null
			{
				//first set qty to zero, otherwise it will += increase by given qty
				$size_id=$this->_find_size_id($prize_id,$s['size']);
				$qty=$s['qty'];
				if($qty)
				{
					//echo "assign quantity: $qty prize $prize_id size $size_id WH $default_wh ";
					$this->prize_model->update_inventory($prize_id,$size_id,$default_wh,$RESET_QTY,$user);
					 $this->prize_model->update_inventory($prize_id,$size_id,$default_wh,$qty,$user);
					// "<br/>";
				}
				//now do price
				foreach($existing_currencies as $cur_id=>$cur_abr)
				{
					if(isset($s[$cur_abr]))
					{
						$price=$s[$cur_abr];
						$this->prize_model->update_prize_price($prize_id,'t',$size_id,$cur_id,$price,$user,$org_id);
					}
				}
 
			}
 
			if($is_new)
			{
			//loop to download all images for this prize
			 //if(!count($prize['images'] )) {echo "Image found with zero "}
			foreach($prize['images'] as $img_array)
			{
				if(!isset($img_array['full']) || $img_array['full']==null||$img_array['full']=='/') continue;//if nothing
				
				$full_url =$img_array['full'];
				
				
				$full_array=explode($url_file_sep,$full_url);
				
				$remote_filename = array_pop($full_array);
				$remote_path = implode($url_file_sep,$full_array).$url_file_sep;
				 
				//$remote_filename=$this->_handle_ftp_whitespace($config,$remote_path,$remote_filename);
				 
				$full_name='uploaded/prize-assets/'.$time.'-'.$remote_filename;
				
				$full_url = $remote_path.$remote_filename;
				
				$this->ftp->connect($config); 
				
				
				if($this->ftp->download($full_url,$temp_name)==false)
				{
					echo "Failed to download $full_url <br/>";
					$this->ftp->close();
				}
				else
				{
					$this->ftp->close();
					//now upload teh TMP file
					if( $this->ftp->upload($temp_name,$full_name)==false)
						echo "Failed local save-upload: $full_name <br/>";
				}
				//else echo "SUcCESS download $remote_filename <br/>";
				 
				
				if(isset($img_array['thumb']) && $img_array['thumb']!=null && $img_array['thumb'] != '/' )
				{
					$thumb_url=$img_array['thumb'];
 
					$t_array=explode($url_file_sep,$thumb_url);
					// t for thumbnail
					$remote_filename = array_pop($t_array);
					$remote_path = implode($url_file_sep,$t_array).$url_file_sep;
				 
				 	//$//remote_filename=$this->_handle_ftp_whitespace($config,$remote_path,$remote_filename);
				 	
					$t_name='uploaded/prize-assets/'.'t'.$time.'-'.$remote_filename;
					
					$thumb_url = $remote_path.$remote_filename;
					$this->ftp->connect($config); 
					//if($this->ftp->download($thumb_url,$t_name)==false)
					if($this->ftp->download($thumb_url,$temp_name)==false)
					{
						echo "Failed to download $thumb_url <br/>";
						$this->ftp->close();
						
					}
					else
					{
						$this->ftp->close();
						if( $this->ftp->upload($temp_name,$t_name)==false) 
							echo "Failed local save-upload: $t_name";
						
					}
					//else	echo "SUCCESS to download $t_name <br/>";
				}
				$asset_id=$this->prize_model->insert_asset_noupload($prize_id,$full_name,$user,$org_id,$t_name);
				$this->prize_model->update_default_image($prize_id,$asset_id);

			}
			
			}
			
		}
		
		echo "<br/>".$new_prizes_created." new prizes created";
    }
    
    
    
}

?>
