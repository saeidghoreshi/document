<?php
require_once('./endeavor/models/endeavor_model.php');
class Prize_model extends Endeavor_model
{
	public function get_myorders($user)
    {
        $sql = "SELECT      s.status_name, x.created_by, x.order_desc, p.person_fname||' '||p.person_lname AS created_name ,
                            x.created_on, x.order_id, x.order_status_id , u.person_id , x.requested_date , o.org_name
                FROM        prize.order x 
                INNER JOIN  permissions.user u            ON          u.user_id = x.created_by   
                INNER JOIN  public.entity_person p        ON           p.person_id = u.person_id
                INNER JOIN  prize.lu_order_status s       ON       s.status_id = x.order_status_id 
                INNER JOIN  entity_org o                  ON       x.owned_by = o.org_id
                AND         x.created_by = ? 
                AND         x.deleted_flag = 'f'  ";
        $query = $this->db->query($sql,$user);
        return $query->result_array();                
    }
    
    public function get_orders($assoc_org_id)
    {
    	$params=func_get_args();
        $sql = "SELECT      s.status_name, x.created_by, x.order_desc, p.person_fname||' '||p.person_lname AS created_name ,
                            x.created_on, x.order_id, x.order_status_id , u.person_id , x.requested_date , o.org_name , o.org_id 
                FROM        prize.order x 
                INNER JOIN  permissions.user u            ON          u.user_id = x.created_by   
                INNER JOIN  public.entity_person p        ON           p.person_id = u.person_id
                INNER JOIN  prize.lu_order_status s       ON       s.status_id = x.order_status_id 
                INNER JOIN  entity_org o                  ON       x.owned_by = o.org_id 
                INNER JOIN public.entity_relationship er ON er.child_id= o.entity_id 
				INNER JOIN public.entity_org po ON po.entity_id = er.parent_id AND po.org_id=?
                AND         x.deleted_flag = 'f'    ";
        $query = $this->db->query($sql,$params);
        return $query->result_array();                
    }
    
    public function get_orders_bystatus($assoc_org_id,$status)
    {
    	$params=func_get_args();
		$sql = "SELECT      s.status_name, x.created_by, x.order_desc, p.person_fname||' '||p.person_lname AS created_name ,
                            x.created_on, x.order_id, x.order_status_id , u.person_id , x.requested_date , o.org_name , o.org_id 
                FROM        prize.order x 
                INNER JOIN  permissions.user u            ON          u.user_id = x.created_by   
                INNER JOIN  public.entity_person p        ON           p.person_id = u.person_id
                INNER JOIN  prize.lu_order_status s       ON       s.status_id = x.order_status_id  
                INNER JOIN  entity_org o                  ON       x.owned_by = o.org_id   
                INNER JOIN public.entity_relationship er ON er.child_id= o.entity_id 
				INNER JOIN public.entity_org po ON po.entity_id = er.parent_id AND po.org_id=?
                										  AND   x.order_status_id = ?            
                AND         x.deleted_flag = 'f'   ";
        $query = $this->db->query($sql,$params);
        return $query->result_array(); 	
    }
    
    public function get_order_details($orderid)
    {
    	$sql = "SELECT      s.status_name, x.created_by, x.order_desc, p.person_fname||' '||p.person_lname AS created_name ,
                            x.created_on, x.order_id, x.order_status_id , u.person_id , x.requested_date , o.org_name , o.org_id 
                FROM        prize.order x 
                INNER JOIN  permissions.user u            ON          u.user_id = x.created_by   AND x.order_id=? 
                INNER JOIN  public.entity_person p        ON           p.person_id = u.person_id
                INNER JOIN  prize.lu_order_status s       ON       s.status_id = x.order_status_id 
                INNER JOIN  entity_org o                  ON       x.owned_by = o.org_id              
                 LIMIT 1  ";
		$query = $this->db->query($sql , $orderid);
        return $query->result_array(); 
    }
    
    public function get_orderitems($orderid)
    {
        $sql ="SELECT    p.name, i.qty,i.price, s.size_name,s.size_abbr,i.order_id ,
						 i.prize_id , i.item_id, i.size_id  ,p.sku ,p.upc
						,i.currency_type_id , f.currency_abbrev ,f.html_character, 
                        c.category_name, c.category_id , 
                        (SELECT SUM(quantity) FROM prize.inventory inv 
                          WHERE inv.size_id = i.size_id   AND inv.prize_id = i.prize_id ) AS available ,
 						  (SELECT  r.quantity FROM prize.reserved_item r WHERE r.size_id = i.size_id AND r.prize_id = i.prize_id)
 						  	AS reserved
                FROM prize.order_item i 
                INNER JOIN prize.prize p                ON i.prize_id = p.prize_id  AND i.deleted_flag = 'f' AND i.order_id = ? 
                INNER JOIN prize.category c  			ON c.category_id = p.category_id 
                INNER JOIN prize.size s 				ON i.size_id = s.size_id 
                INNER JOIN finance.lu_currency_type f   ON f.type_id = i.currency_type_id  
                ORDER BY p.name, s.lu_size_id, f.currency_abbrev
  ";
  /*    removed because we may need to see past history orders even for currently deleted prizes
                
                AND p.deleted_flag = 'f'  */
        
        $query = $this->db->query($sql , $orderid);
        return $query->result_array(); 
    }
    /**
    * gets all sizes for this prize, and also finds out how many, if any, of that size have been ordered on this order 
    * this will list each size multiple times, once for each currency.  sort out the minimum price, or the required currency type 
    * or whatever on the controller side.  
    * this WILL list items with nothing in stock, and stock is over all warehouse-inventories for the size
    *
    * 
    * @param mixed $order_id
    * @param mixed $prize_id
    * @param mixed $array_cur_types
    */
    public function get_prize_size_orderitems($order_id,$prize_id,$array_cur_types)
    {
    	$cur1 = (isset($array_cur_types[0]) ? $array_cur_types[0] : -1 ); 
    	$cur2 = (isset($array_cur_types[1]) ? $array_cur_types[1] : -1 ); 
    	$cur3 = (isset($array_cur_types[2]) ? $array_cur_types[2] : -1 ); 

    	$params = array($cur1,$order_id,$cur1 
    			  	   ,$cur2,$order_id,$cur2 
    				   ,$cur3,$order_id,$cur3,    $prize_id); 
		//  is the second most beautiful query i have ever written -SB
    	$sql="SELECT s.size_id, s.prize_id, s.size_abbr 
					,(SELECT SUM(inv.quantity) FROM prize.inventory inv 
					  WHERE inv.size_id = s.size_id  ) 
					  AS total_stock 
					,pc1.price AS cur1_price ,  oi1.qty AS cur1_qty , oi1.qty*pc1.price AS cur1_total 
					,pc2.price AS cur2_price ,  oi2.qty AS cur2_qty , oi2.qty*pc2.price AS cur2_total 
					,pc3.price AS cur3_price ,  oi3.qty AS cur3_qty , oi3.qty*pc3.price AS cur3_total 
			FROM prize.size s 
			LEFT OUTER JOIN prize.price pc1 
				ON  pc1.size_id     = s.size_id 
				AND pc1.currency_id = ? AND pc1.deleted_flag='f' AND pc1.is_active='t' 
			LEFT OUTER JOIN prize.order_item oi1 
				ON  oi1.size_id  = s.size_id 
				AND oi1.order_id = ? 
				AND oi1.currency_type_id = ? AND oi1.deleted_flag='f'

			LEFT OUTER JOIN prize.price pc2 
				ON  pc2.size_id     = s.size_id 
				AND pc2.currency_id = ? AND pc2.deleted_flag='f' AND pc2.is_active='t' 
			LEFT OUTER JOIN prize.order_item oi2  
				ON  oi2.size_id  = s.size_id 
				AND oi2.order_id = ? 
				AND oi2.currency_type_id = ? AND oi2.deleted_flag='f'

			LEFT OUTER JOIN prize.price pc3 
				ON  pc3.size_id     = s.size_id 
				AND pc3.currency_id = ? AND pc2.deleted_flag='f' AND pc2.is_active='t'	 	
			LEFT OUTER JOIN prize.order_item oi3  
				ON  oi3.size_id  = s.size_id 
				AND oi3.order_id = ?  
				AND oi3.currency_type_id = ? AND oi3.deleted_flag='f' 

			WHERE s.prize_id = ? 
			ORDER BY s.lu_size_id     ";
    	
    	/*    	//$params=array($order_id,$prize_id,$cur1,$cur2,$cur3);
    	//the second subquery could probably be a left outer join...  
		$sql="SELECT s.size_id, s.prize_id, s.size_abbr
					,(SELECT SUM(inv.quantity) FROM prize.inventory inv 
					  WHERE inv.size_id = s.size_id  ) 
					  AS total_stock 
					,(SELECT i.qty FROM prize.order_item i  
					  WHERE  i.order_id = ? AND i.deleted_flag='f' AND i.size_id=s.size_id AND i.currency_type_id=?  ) 
					  AS qty 
			FROM prize.size s 
			INNER JOIN prize.price r ON r.prize_id = s.prize_id AND r.size_id = s.size_id  
									AND r.is_active='t' AND r.deleted_flag='f' AND s.prize_id = ? AND s.deleted_flag='f'  
			INNER JOIN finance.lu_currency_type c ON c.type_id = r.currency_id 
			ORDER BY s.lu_size_id";//lu_size_id may be null, meaning custom sizes go a the bottom
			
			*/
		return $this->db->query($sql , $params)->result_array();
    }
    public function get_ordercurrency($order)
    {
        $sql = "  SELECT    (SELECT lu.currency_name FROM prize.lu_order_currency lu 
                             WHERE lu.currency_id = x.currency_id) AS currency_type, 
                            x.value, x.currency_id, x.order_currency_id , x.order_id
                  FROM      prize.order_currency x 
                  WHERE     x.order_id = ?";
                  
        $query = $this->db->query($sql , (int)$order);
        return $query->result_array();         
    }
    
    
    /**
    * by checking roles assigned to this user, in this org, we can see
    * exactly how they are allowed to update the status of an order
    * 
    * @param mixed $user_id
    * @param mixed $org_id
    */
    public function get_role_order_status($user_id,$org_id, $from)
	{
		$params=array($from,$user_id,$org_id);
		$sql="SELECT  ros.to_status_id  AS status_id
			  ,( SELECT os.status_name FROM prize.lu_order_status os WHERE os.status_id = ros.to_status_id ) AS status_name
			  FROM prize.lu_role_order_status ros 
			  WHERE ros.from_status_id = ? 
			  AND      ros.role_id IN 
			  (SELECT a.role_id FROM permissions.assignment a WHERE a.user_id = ? AND a.org_id = ?) 
			  GROUP BY status_id, status_name
			  ";
		return $this->db->query($sql , $params)->result_array();
	}
    /**
    * get the lookup table
    * 
    */
    public function get_order_status()
	{

		$sql="SELECT status_id, status_name, status_desc FROM prize.lu_order_status ORDER BY status_id ASC";
		return $this->db->query($sql )->result_array();
	}
    
    
    
    public function get_invoices($invoiceto)
    {
        $filter="";
        if($invoiceto != '')
            $filter = " AND x.invoice_to = ? ";
        $params = array($invoiceto);
        $sql="  SELECT  x.invoice_desc, 
                        x.date_issued, x.date_due, 
                        x.invoice_number, x.invoice_id, 
                        (SELECT eo.org_name FROM public.entity_org eo 
                         WHERE eo.org_id = x.invoice_to LIMIT 1) as entity_to, 
                         x.invoice_to ,
                         (SELECT COUNT(*) FROM finance.invoice_item it WHERE it.invoice_id = x.invoice_id) AS count 
                FROM    finance.invoice x 
                WHERE   x.deleted_flag = 'f'  ". $filter;//filter may be empty

        return $this->db->query($sql,$params)->result_array();
    }
    public function get_invoiceitems($id)
    {
        $sql="  SELECT       eo.org_name ,     
                              lu.type_descr, 
                              ct.type_code, 
                            x.invoice_item_id , 
                            x.entity_charge_id ,
                             ec.charge_price    ,
                             x.item_descr         
                FROM        finance.invoice_item x 
                INNER JOIN  finance.entity_charge ec ON          ec.entity_charge_id = x.entity_charge_id 
                INNER JOIN  finance.lu_currency_type ct                ON ct.type_id = ec.currency_type_id
                INNER JOIN  finance.lu_charge_type lu ON    lu.type_id = ec.charge_type_id 
                INNER JOIN  public.entity_org eo ON eo.entity_id = ec.charging_entity_id 
                WHERE       x.invoice_id = ?  ";
        return $this->db->query($sql,$id)->result_array();
        
    }
    
    public function update_orderstatus($orderid,$status,$user)
    {
        $params = array($orderid,$status,$user);
        $sql = 'SELECT prize.update_orderstatus(?,?,?)';//
        $query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->update_orderstatus;//
        
    }
    public function update_order($orderid,$desc,$date,$user)
    {
        $params = array($orderid,$desc,$date,$user);
        $sql = 'SELECT prize.update_order(?,?,?,?)';//
        $query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->update_order;//
        
    }
    
    public function delete_order($orderid,$user)
    {
		$params = array($orderid,$user);
        $sql = 'SELECT prize.delete_order(?,?)';//
        $query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->delete_order;//
    }
    /**
    * add invoice_id to an order
    * 
    * @param mixed $orderid
    * @param mixed $invoice_id
    */
    public function update_order_invoice($orderid,$invoice_id)
    {//
		$params = array($orderid,$invoice_id);
        $sql = 'SELECT prize.update_order_invoice(?,?)';//
        $query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->update_order_invoice;
		
    }
    
    /**
    * this is if we know the item_id in the order_item table
    * 
    * @param mixed $id
    * @param mixed $qty
    * @param mixed $user
    * @param mixed $order_id
    */
    public function update_orderitem($id,$qty,$user,$order_id)
    {
        $params = array($id,$qty,$user,$order_id);
        $sql = 'SELECT prize.update_orderitem(?,?,?,?)';//
        $query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->update_orderitem;//not actually needed, but ok
        
    }
    /**
    * adds an item to the order, or updates an existing item if one matches the 
    * ids: prize size currency etc (uniquecheck)
    * 
    * @param mixed $order_id
    * @param mixed $prize_id
    * @param mixed $size_id
    * @param mixed $qty
    * @param mixed $price
    * @param mixed $currency_id
    */
    public function insert_orderitem($order_id,$prize_id,$size_id,$qty,$price,$currency_id,$user,$owner)
    {
		$params=array($order_id,$prize_id,$size_id,$qty,$price,$currency_id,$user,$owner);
		$sql = 'SELECT prize.insert_orderitem(?,?,?,?,?,?,?,?)';//
        $query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->insert_orderitem;//not actually needed, but ok
		
    }
	public function get_prize($prize)
	{
		$sql = "SELECT 		p.prize_id, name, description, sku, upc, category_name, pc.category_id, 
							(SELECT filepath FROM prize.asset a WHERE a.prize_id = p.prize_id AND asset_type_id = 1 LIMIT 1) as image
				FROM 		prize.prize p
				INNER JOIN	prize.category pc ON p.category_id = pc.category_id
				WHERE		p.prize_id = ?
				ORDER BY 	name";
		$query = $this->db->query($sql, array($prize));
		return $query->first_row('array');
	}
	
	public function get_prizes($ownedby_org_id)
	{
		$params=func_get_args();
		$sql = "SELECT 		x.prize_id,
							x.name, 
							x.description,
							x.sku,
							x.upc, 
							pc.category_name, pc.category_id,
							a.filepath,
							a.thumb_filepath,
							a.asset_id
							,(SELECT SUM(inv.quantity) FROM prize.inventory inv WHERE inv.prize_id = x.prize_id ) AS total_stock
					  		,(SELECT AVG(price) FROM prize.price pri WHERE pri.prize_id=x.prize_id AND pri.deleted_flag='f' AND pri.is_active=TRUE ) AS avg_price
					  		,(SELECT MIN(price) FROM prize.price prm WHERE prm.prize_id=x.prize_id AND prm.deleted_flag='f' AND prm.is_active=TRUE ) AS min_price
				FROM 		prize.prize x
				INNER JOIN	prize.category pc ON x.category_id = pc.category_id AND x.deleted_flag = 'f' 
				LEFT OUTER JOIN prize.prize_default_image i ON i.prize_id = x.prize_id 
				LEFT OUTER JOIN prize.asset a ON i.asset_id = a.asset_id AND a.deleted_flag='f' 
				WHERE x.owned_by = ?  
				ORDER BY 	name";
		$query = $this->db->query($sql,$params);
		return $query->result_array();
	}
	
	
	public function get_prizes_by_category($category)
	{
		
		$sql = "SELECT 		x.prize_id,
							x.name, 
							x.description,
							x.sku,
							x.upc, 
							pc.category_name, pc.category_id,
							a.filepath,a.thumb_filepath,a.asset_id
							,(SELECT SUM(inv.quantity) FROM prize.inventory inv WHERE inv.prize_id = x.prize_id ) AS total_stock
					 		,(SELECT AVG(price) FROM prize.price pri WHERE pri.prize_id=x.prize_id AND pri.deleted_flag='f' AND pri.is_active=TRUE ) AS avg_price 
					 		,(SELECT MIN(price) FROM prize.price prm WHERE prm.prize_id=x.prize_id AND prm.deleted_flag='f' AND prm.is_active=TRUE ) AS min_price
				FROM 		prize.prize x
				INNER JOIN	prize.category pc ON x.category_id = pc.category_id AND x.deleted_flag = 'f' AND pc.category_id = ? AND x.deleted_flag = 'f'
				LEFT OUTER JOIN prize.prize_default_image i ON i.prize_id = x.prize_id 
				LEFT OUTER JOIN prize.asset a ON i.asset_id = a.asset_id AND a.deleted_flag='f' 
				ORDER BY 	name";
		$query = $this->db->query($sql, array($category));//TODO: fix USER_CAN_ACCESS
		return $query->result_array();
	}
	/**
	* $asset_type==1 is default: for images. check lu_asset_type table for others
	* 
	* @param int $prize_id
	* @param int $asset_type
	*/
	public function get_prize_assets($prize_id,$asset_type=1)
	{
		$params=array($prize_id,$asset_type);
		$sql="SELECT a.asset_id ,a.thumb_filepath, a.filepath , a.description ,a.prize_id ,a.created_on 
			 ,(SELECT COUNT(*) FROM  prize.prize_default_image d WHERE d.asset_id = a.asset_id )  AS is_default
			FROM prize.asset a 
			INNER JOIN prize.lu_asset_type lu ON lu.type_id = a.asset_type_id 
			AND a.prize_id =? AND a.deleted_flag='f' AND a.asset_type_id=?";
		return $this->db->query($sql,$params)->result_array();
	}
	public function get_categories($org_id)
	{
		$sql = "SELECT 		c.category_id, 
							c.category_name, 
							c.category_desc,
							(SELECT COUNT(*) FROM prize.prize p WHERE p.category_id = c.category_id AND p.deleted_flag = 'f') 
							AS prize_count
				FROM 		prize.category c 
				WHERE       c.org_id=? AND c.deleted_flag='f' 
				ORDER BY 	c.category_name";
		$query = $this->db->query($sql,$org_id);
		return $query->result_array();
	}
	
	
	
	public function delete_category($c,$user)
	{
		$params=array($c,$user);
		$sql 	 = 'SELECT prize.delete_category_cascade(?,?)';
		return $this->db->query($sql, $params)->first_row()->delete_category_cascade;
		
		
	}
	
	public function get_sizes($prize_id)
	{
		$sql="SELECT prize_id,size_id,size_name,size_abbr,lu_size_id 
		FROM prize.size s WHERE s.prize_id=? AND s.deleted_flag='f' ";
		return $this->db->query($sql,$prize_id)->result_array();
	}
	
	public function delete_prize($prize_id,$user)
	{
		$params=array($prize_id,$user);
		$sql 	 = 'SELECT prize.delete_prize_cascade(?,?)';
		return $this->db->query($sql, $params)->first_row()->delete_prize_cascade;
		
	}
	public function delete_size($prize_id,$size_id,$user)
	{
		$params=array($prize_id,$size_id,$user);
		$sql 	 = 'SELECT prize.delete_size(?,?,?)';
		return $this->db->query($sql, $params)->first_row()->delete_size;
		
	}
	public function update_size($size_id,$user,$name,$abbr)
	{
		$params=array($size_id,$user,$name,$abbr);
		$sql 	 = 'SELECT prize.update_size(?,?,?,?)';
		return $this->db->query($sql, $params)->first_row()->update_size;
		
	}
	public function insert_size($prize_id,$user,$owner,$name,$abbr)
	{
		$params=array($prize_id,$user,$owner,$name,$abbr);
		$sql 	 = 'SELECT prize.insert_size(?,?,?,?,?)';
		return $this->db->query($sql, $params)->first_row()->insert_size;
			
	}
	public function insert_default_size($prize_id,$lu_size_id,$user,$owner)
	{
		$params=array($prize_id,$lu_size_id,$user,$owner);
		$sql 	 = 'SELECT prize.insert_default_size(?,?,?,?)';
		return $this->db->query($sql, $params)->first_row()->insert_default_size;
	}
	public function get_lu_sizes()
	{
		$sql = "SELECT size_id, size_abbr, size_name FROM prize.lu_size ORDER BY size_seq";
		return $this->db->query($sql)->result_array();
	}
	/**
	* get information and quantities for the given prize/warehouse ids
	* list out for each size. this ignores prices
	* @param mixed $prize
	* @param mixed $warehouse. 
	*/
	public function get_inventory($prize,$warehouse)
	{
		$sql = 'SELECT ps.size_id, ps.size_abbr, ps.size_name, pi.quantity, 
                                                 pi.inventory_id ,ps.prize_id 
				FROM prize.size ps 
				LEFT OUTER JOIN	prize.inventory pi 
					ON (pi.size_id = ps.size_id 
					AND pi.warehouse_id = ?) 
					WHERE ps.size_id IS NOT NULL 
					AND  ps.prize_id = ? '; 
				//yes the order is reversed here in compared to function input
		return  $this->db->query($sql, array((int)$warehouse,(int)$prize))->result_array();
	}
	/**
	* get only quantity for the desired inv row
	* 
	* @param mixed $prize
	* @param mixed $size
	* @param mixed $wh
	*/
	public function get_quantity($prize,$size,$wh)
	{
		$sql = "SELECT i.quantity FROM prize.inventory i WHERE 
			i.prize_id = ? AND i.size_id = ? AND i.warehouse_id = ?";
		return  $this->db->query($sql, array((int)$prize,(int)$size,(int)$wh))->result_array();
	}

	/**
	* depends on currency id
	* 
	* @param mixed $prize
	*/
	public function get_prices($prize,$currency_id)
	{
		$params=array($prize,$currency_id);
		$sql = 'SELECT ps.size_id, ps.size_abbr, ps.size_name , pr.price ,pr.is_active
				FROM prize.size ps 
				INNER JOIN prize.prize p ON p.prize_id = ps.prize_id AND p.prize_id=? 
				LEFT OUTER JOIN	prize.price pr 
					ON pr.size_id = ps.size_id 
					AND pr.currency_id = ? 
					AND pr.deleted_flag = FALSE '; 
		return $this->db->query($sql, $params)->result_array();

	}
	
	public function get_size_prices_stock($size_id,$currency_id)
	{
		$params=array($size_id,$currency_id);
		$sql = 'SELECT ps.size_id, ps.size_abbr, ps.size_name , pr.price , ps.prize_id 
				,(SELECT SUM(inv.quantity) 
					FROM prize.inventory inv 
					WHERE inv.size_id = ps.size_id  ) AS total_stock 
				FROM prize.size ps 
				INNER JOIN	prize.price pr 
					ON ps.size_id = ? AND ps.size_id = pr.size_id 
					AND pr.currency_id = ? 
					AND pr.deleted_flag = FALSE AND pr.is_active = TRUE LIMIT 1'; 
		return $this->db->query($sql, $params)->result_array();

	}
	
	/**
	* get the total $ used over all items in this order for this currency
	* if prize id given, sum will exclude this prize
	* 
	* @param mixed $order_id
	* @param mixed $prize_id
	*/
	public function get_used_order_currency($order_id,$currency_id,$prize_id=-1)
	{
		$params=array($order_id,$currency_id,$prize_id);
		$sql="SELECT SUM ( i.qty * i.price ) AS total 
			  FROM prize.order_item i 
			  WHERE i.order_id = ? 
			  AND   i.currency_type_id = ?
			  AND   i.prize_id <> ?";
		return $this->db->query($sql, $params)->first_row()->total;
		
	}
	
	/*
	public function get_size_inventory($size_id)
	{
		$sql="  (SELECT SUM(inv.quantity) AS total_stock 
				FROM prize.inventory inv 
				WHERE inv.size_id = ? ) ";
					  
		return $this->db->query($sql, $size_id)->result_array();
	}
	*/
	/**
	* depreciated
	* 
	* @param mixed $prize
    public function get_inventory($prize,$warehouse)
	{
		$sql = 'SELECT ps.size_id, ps.size_abbr, ps.size_name, pi.quantity, is_active, pip1.price AS cur1, 
                                                pip2.price AS cur2, pi.inventory_id
				FROM prize.lu_size ps 
				LEFT OUTER JOIN	prize.inventory pi 
					ON (pi.size_id = ps.size_id 
					AND pi.prize_id = ? 
					AND pi.warehouse_id = ?)
				LEFT OUTER JOIN	prize.price pip1 
					ON (pip1.inventory_id = pi.inventory_id 
					AND pip1.currency_id = 1 
					AND pip1.deleted_flag = FALSE)
				LEFT OUTER JOIN	prize.price pip2 
					ON (pip2.inventory_id = pi.inventory_id 
					AND pip2.currency_id = 2 
					AND pip2.deleted_flag = FALSE)
				ORDER BY ps.size_seq'; 
		$result = $this->db->query($sql, array((int)$prize,(int)$warehouse))->result_array();
		return $result;
	}
	public function get_total_inventory($prize)
	{
		$sql = 'SELECT ps.size_id, ps.size_abbr, ps.size_name, SUM(pi.quantity) AS quantity, is_active, pip1.price AS cur1, 
                                                pip2.price AS cur2, pi.inventory_id
				FROM prize.lu_size ps 
				LEFT OUTER JOIN	prize.inventory pi 
					ON (pi.size_id = ps.size_id 
					AND pi.prize_id = ? )
				LEFT OUTER JOIN	prize.inventory_price pip1 
					ON (pip1.inventory_id = pi.inventory_id 
					AND pip1.currency_id = 1 
					AND pip1.deleted_flag = FALSE)
				LEFT OUTER JOIN	prize.inventory_price pip2 
					ON (pip2.inventory_id = pi.inventory_id 
					AND pip2.currency_id = 2 
					AND pip2.deleted_flag = FALSE)
				ORDER BY ps.size_seq 
				GROUP BY pi.warehouse_id '; 
		$result = $this->db->query($sql, array((int)$prize))->result_array();
		return $result;
		
	}
	*/
	
	/**
	* Inserts a new prize with details
	* 
	* @param string $name
	* @param string $sku
	* @param string $upc
	* @param string $description
	* @param int $creator
	*/
	public function insert_prize_details($name, $sku, $upc, $description, $creator, $category_id,$owner)
	{
		$params = array($name, $sku, $upc, $description, $creator, $category_id,$owner);
		$sql 	 = 'SELECT prize.insert(?,?,?,?,?,?,?)';
		return $this->db->query($sql, $params)->first_row()->insert;
	}

	public function update_prize_details($id, $name,  $sku, $upc, $description, $modifier,$category_id)
	{
		$params = array($id, $name, $sku, $upc, $description, $modifier, $category_id);
		$sql 	 = 'SELECT prize.update(?,?,?,?,?,?,?)';
		return $this->db->query($sql, $params)->first_row()->update;
	}
	/**
	* the invoice_id of the order starts null, until invoice is created
	* returns order id
	* 
	* @param mixed $creator
	* @param mixed $owner
	* @param mixed $date
	* @param mixed $desc
	*/
    public function insert_order($creator,$owner,$date,$desc)
    {//
        $sql = 'SELECT prize.insert_order(?,?,?,?)';
        $query=$this->db->query($sql,array($creator,$owner,$date,$desc));
        $result  = $query->first_row();
        return $result->insert_order;
    }
    /**
    * DEPRECIATED
    * use finance model
    * 
    * @param mixed $user
    * @param mixed $to
    * @param mixed $descr
    * @param mixed $owner
    */
    public function insert_invoice($user,$to,$descr,$owner)
    {
        $params =array($user,$to,$descr,$owner);
        $sql = 'SELECT finance.insert_invoice(?,?,?,?)';
        $query = $this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->insert_invoice;
    }
    
    public function update_default_image($prize,$asset)
	{
		$params		= array($prize,$asset);
		$sql 		= 'SELECT prize.update_default_image (?,?)';
		$result 	= $this->db->query($sql, $params);
		return $result->first_row()->update_default_image;
	}
	
    /**
    * new item for order
    * 
    * @param mixed $item
    * @param mixed $orderid
    * @param mixed $creator
    * @param mixed $owner
  
    public function insert_order_item($item,$orderid,$creator,$owner)
    {
        $details = array($creator,$orderid,$item['prize_id'],$item['league_qty'],$item['league_price'],
                                    $item['promo_qty'],$item['promo_price'],$item['size_id'],$owner);
        
        $sql = 'SELECT prize.insert_order_item(?,?,?,?,?,?,?,?,?)';
        $query=$this->db->query($sql,$details);
        $result  = $query->first_row();
        return $result->insert_order_item;//not actually needed, but ok
        
    }
	*/
	
	
	public function update_inventory($prize, $size_id,$warehouse,$quantity,$user)
	{
		$params 	= array($prize, $size_id,$warehouse,$quantity,$user);
		$sql 		= 'SELECT prize.update_inventory (?,?,?,?,?)';
		$result 	= $this->db->query($sql, $params);
		return $result->first_row()->update_inventory;
	}
	
	public function update_prize_price($prize_id,$is_active,$size_id, $currency_id, $price, $modifier,$owner)
	{
		$params		= array((int)$prize_id, $is_active,(int)$size_id, (int)$currency_id,(float)$price, (int)$modifier,$owner);
		$sql 		= 'SELECT prize.update_price (?,?,?,?,?,?,?)';
		$result 	= $this->db->query($sql, $params);
		return $result->first_row()->update_price;
	}

	public function insert_prize_image($file, $prize_id, $creator,$owner,$thumb_type='crop')
	{
 
		$this->load->library('ftp');   
 
		//if($this->ftp->connect($config) )
		//{
			//if we connected
			$today=date('YmdHis');
 
			$path = "uploaded/prize-assets/".$today.'-'.$prize_id.'-'.$file['name'];//example:  /20110609-94-softball.png
 
			$this->ftp->upload($file["tmp_name"], $path/*, $encoding, $chmod*/);
			 
			//crop thumbnail 
			$this->load->library('images'); 
			
			//create thumbnail and save it to the temp directory
			$oImage=$this->images->path_to_image($path);
			if($thumb_type=='crop') 
			{
				
				$thumb =$this->images->stretch($oImage);
				$thumb =$this->images->crop($thumb);
			}
			else if($thumb_type=='fill')
			{
				$thumb =$this->images->proportionate($oImage);
				$thumb =$this->images->fill($thumb);
				
			}
			else
			{
				//if other unsupported type
				return false;
			}
			//also create a thumbnail
			$thumb_filename="thumb-".$today.'-'.$prize_id.'-'.$file['name'];//example:  /thumb-20110609-94-softball.png
			$thumb_path=/*$root.*/"uploaded/prize-assets/";
			$temp=sys_get_temp_dir()."/".$thumb_filename;
			$thumb_final = $thumb_path.$thumb_filename;
 
			$this->images->image_to_path($thumb,$temp);
			//free up the memory
			imagedestroy($thumb);
			imagedestroy($oImage);
			//copy with ftp from temp to dest
 
			$this->ftp->upload($temp,$thumb_final );
			
		//	$this->ftp->close();
			//insert path information into database, along with thumbnail info
			//returns asset id
			return $this->insert_asset_noupload($prize_id, $path, $creator,$owner,$thumb_final);
			/*
			$sql 		= 'SELECT prize.insert_prize_image (?,?,?,?,?)';
			$params=array((int)$prize_id, (string)$path, (int)$creator,(int)$owner,$thumb_final);
			$result 	= $this->db->query($sql,$params  );
			return $result->first_row()->insert_prize_image;*///
		//}
		//else return false;
		
	}
	
	/**
	* returns asset id
	* 
	* @param mixed $prize_id
	* @param mixed $filename
	* @param mixed $creator
	* @param mixed $owner
	* @param mixed $thumb
	*/
	public function insert_asset_noupload($prize_id, $filename, $creator,$owner,$thumb)
	{
		$params=array((int)$prize_id, (string)$filename, (int)$creator,(int)$owner,$thumb);
		$sql 		= 'SELECT prize.insert_prize_image (?,?,?,?,?)';
		$result 	= $this->db->query($sql,$params  );
		return $result->first_row()->insert_prize_image;//returns asset id
	}
	public function delete_asset($asset_id,$user)
	{
		$params=array($asset_id,$user);
		$sql='SELECT prize.delete_asset(?,?)';
		return $this->db->query($sql,$params)->first_row()->delete_asset;		
	}
	public function set_active_order($activeOrder)
	{
		//loop orders looking for orders of 0 and remove
		foreach($activeOrder as $x=>$prize)
		{
			foreach($prize as $k=>$inventory)
			{
				if($inventory->order==0) unset($activeOrder[$x][$k]);
			}
		}
		
		$_SESSION['prize_order'] = $activeOrder;
	}
	
	public function get_active_order()
	{
		return (array_key_exists('prize_order',$_SESSION)) ? $_SESSION['prize_order'] : array();
	}
	
	public function set_active_summary($curLeagueDollars)
	{
		$activeOrder = $this->get_active_order();
		if(empty($activeOrder)) return;
		//var_dump($activeOrder);
		$items = array();
		$sorts = array();
		foreach($activeOrder as $prize)
		{
			$item = array();
			foreach($prize as $inventory)
			{
                
				$item['sku'] = $inventory->sku;
				$item['name'] = substr($inventory->name,0,25).((strlen($inventory->name)>25)?"...":"");
				$item['size'] = $inventory->size;
                $item['size_id'] = $inventory->size_id;
				$item['qty'] = $inventory->order;
				$item['promo_price'] = money_format("%i",$inventory->cur1);
				$item['league_price'] = money_format("%i",$inventory->cur2);
				$item['prize_id']=$inventory->prize_id;
				$item['promo_qty'] = "";
				$item['promo_total'] = "";
				$item['league_qty'] = "";
				$item['league_total'] = "";
				
				$item['diff'] = $item['league_price'] - $item['promo_price'];
				
				$sorts[$item['diff']][] = count($items);
				$items[] = $item;
                //var_dump($item);
			}
		}
		
		//sort by diff
		krsort($sorts,SORT_NUMERIC);
		$unsorted = $items;
		$items = array();
		foreach($sorts as $group) foreach($group as $key) $items[] = $unsorted[$key];
		
		//exhaust league dollars
		foreach($items as &$item)
		{
			$x = $item['qty'] * $item['league_price'];
			
			if($item['league_price']>0)
			{
				$item['league_qty'] = ($x > $curLeagueDollars) ? floor($curLeagueDollars/$item['league_price']) : $item['qty'];
				$item['league_total'] = $item['league_qty'] * $item['league_price'];
			}
			else
			{
				$item['league_qty'] = 0;
				$item['league_total'] = 0;
			}
			
			$item['promo_qty'] 	= $item['qty'] - $item['league_qty'];
			$item['promo_total'] = $item['promo_qty'] * $item['promo_price'];
			
			$curLeagueDollars -= $item['league_total'];
			
			//format
			$item['promo_total'] = money_format("%i",$item['promo_total']);
			$item['league_total'] = money_format("%i",$item['league_total']);
		}
		
		$_SESSION['prize_order_summary'] = $items;
	}
	
	public function get_active_summary()
	{
		return (array_key_exists('prize_order_summary',$_SESSION)) ? $_SESSION['prize_order_summary'] : array();
	}
	
	//calculating prize total
	public function get_active_summary_total()
	{
		$summary = $this->get_active_summary();
		$promo = 0.00;
		$league = 0.00;
		//TODO -p 2 -o Bradley -c PRIZES: Change defualt shipping/tax rates based on user
		$taxrate = 0.12;
		$shippingrate = 0.04;
		$shippingamount = 0.00;
		$shippingmin = 20.00;
		$shippingmax = 100.00;
		
		//get promo & league totals
		foreach($summary as $item)
		{
			$promo += $item['promo_total'];
			$league += $item['league_total'];
		}
		$total = $promo + $league;
		
		//calc taxes
		$taxes = $promo * $taxrate;
		
		//calc shipping
		$shippingamount = $total * $shippingrate;
		if($shippingamount <= $shippingmin) $shippingamount = $shippingmin;
		if($shippingamount >= $shippingmax) $shippingamount = $shippingmax;
		
		//due
		$due = $promo + $taxes + $shippingamount;
		
		//create items
		$items = array(
			array("item"=>"Subtotal",			"amount"=>$promo),
			array("item"=>"Taxes",				"amount"=>$taxes),
			array("item"=>"Shipping",			"amount"=>$shippingamount),
			array("item"=>"League Dollars Used","amount"=>$league),
			array("item"=>"Amount Due",			"amount"=>$due)
		);
		
		return $items;
	}
	
	public function insert_category($creator,$owner,$cname,$cdesc,$org_id)
	{
		$params = func_get_args();
		$sql = "SELECT prize.insert_category(?,?,?,?,?)";
		return $this->db->query($sql,$params)->first_row()->insert_category;
	}
	public function update_category($mod,$cname,$cdesc,$cid)
	{
		$params = func_get_args();
		$sql = "SELECT prize.update_category(?,?,?,?)";
		return $this->db->query($sql,$params)->first_row()->update_category;
	}
	/**
	* assumes entity_org id of the warehouse is the smae as $owned_by
	* 
	* @param mixed $creator
	* @param mixed $owner
	* @param mixed $wname
	* @param mixed $wdesc
	* @param mixed $wfav
	*/
	public function insert_warehouse($creator,$owner,$wname,$wdesc,$wfav='f')
	{
		$params = func_get_args();
		$sql = "SELECT prize.insert_warehouse(?,?,?,?,?)";
		return $this->db->query($sql,$params)->first_row()->insert_warehouse;
 
	}
	/**
	* ??assumes entity_org id of the warehouse is the smae as $owned_by
	* 
	* @param mixed $id
	* @param mixed $owner
	*/
	public function delete_warehouse($id,$owner)
	{//
		$params = array($id,$owner);
		$sql = "SELECT prize.delete_warehouse(?,?)";
		$query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->delete_warehouse;
	}
	public function update_warehouse($id,$name,$desc,$user)
	{
		$params = array($id,$name,$desc,$user);
		$sql = "SELECT prize.update_warehouse(?,?,?,?)";
		$query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->update_warehouse;
	}

	
	
	/**
	* depreciated: use get_warehouses_forassoc instead
	* nobody should see a warehouse for a different association
	* @deprecated
	* 
	*/
	public function get_warehouses($org_id)
	{
		$sql = "SELECT      w.warehouse_name, w.warehouse_id, w.warehouse_desc, o.org_name, 
							o.org_id , w.is_default 
				FROM 		prize.warehouse w 
				INNER JOIN	public.entity_org o             ON o.org_id = w.entity_org_id AND o.org_id=?
						AND o.deleted_flag = 'f'  AND w.deleted_flag = 'f'";
		return $this->db->query($sql,$org_id)->result_array();
		
	}
	public function get_warehouses_forassoc($org_id)
	{//given the entity_org_id for teh association, we only want whs for this org
		$sql = "SELECT      w.warehouse_name, w.warehouse_id, w.warehouse_desc, o.org_name, 
							o.org_id , w.is_default 
				FROM 		prize.warehouse w 
				INNER JOIN	public.entity_org o             ON o.org_id = w.entity_org_id 
						AND o.deleted_flag = 'f'  AND w.deleted_flag = 'f' AND w.entity_org_id = ?";
		return $this->db->query($sql,$org_id)->result_array();		
	}
	
	
	public function update_reserved_item($prize_id,$size_id,$qty)
	{
		$params = array($prize_id,$size_id,$qty);
		$sql = "SELECT prize.update_reserved_item(?,?,?)";
		$query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->update_reserved_item;
	}
	
	public function get_reserved($prize_id)
	{
		$sql="SELECT r.size_id, r.quantity FROM prize.reserved_item r WHERE r.prize_id = ? ";
		
		return $this->db->query($sql,$prize_id)->result_array();
	}
	
	
	public function insert_entity_charge($entity_id,$org_type,$charge_type,$price,$currency_type)
	{
		$params=array($entity_id,$org_type,$charge_type,$price,$currency_type);
		$sql = "SELECT finance.insert_entity_charge(?,?,?,?,?)";
		$query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->insert_entity_charge;
	}
	
	public function insert_invoice_item($invoice_id,$ec_id,$desc)
	{
		$params=array($invoice_id,$ec_id,$desc);
		$sql = "SELECT finance.insert_invoice_item(?,?,?)";
		$query=$this->db->query($sql,$params);
        $result  = $query->first_row();
        return $result->insert_invoice_item;
	}
	
}

?>
