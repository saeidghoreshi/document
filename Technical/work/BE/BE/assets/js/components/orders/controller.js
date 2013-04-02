var c_orders= function(){this.construct();};
c_orders.prototype=
{
	construct:function()
	{
         Ext.QuickTips.init();                                                                                 
	},  

	//group all functions and grids in sub-objects
	type_league:3,
	type_assoc :2,
	type_team  :6,
	org_type:null,
	orders:
	{

		grid_id:'manage_l_orders_grid',
		get:function()
		{
			var g=Ext.getCmp(o_orders.orders.grid_id);
			g.refresh();	
		},
		grid:function()
		{
			o_orders.org_type=Ext.get('po_active_org_type').dom.value;
			var bbar_buttons =
			[	
				{tooltip:'Add Prizes to order',iconCls:'cart_put',id:'btn_prizeorder',scope:this,handler:function()
		        {
		        	var g=Ext.getCmp(o_orders.orders.grid_id);
		        	var rows=g.getSelectionModel().getSelection();
		        	
		        	if(!rows.length){return;}
		        	var row=rows[0];
		        	var oid=row.get('order_id');
		        	o_orders.orders.hide();
		        	
		        	o_orders.prizes.order_id=oid;
		        	
		        	o_orders.prizes.show();
		        	o_orders.prizes.get();
		   			     	
				}}
				,{tooltip:'View Cart',iconCls:'cart',id:'btn_cart',scope:this,handler:function()
		        {
		        	
					var g=Ext.getCmp(o_orders.orders.grid_id);
		        	var rows=g.getSelectionModel().getSelection();
		        	
		        	if(!rows.length){return;}
		        	var row=rows[0];
		        	var oid=row.get('order_id');
		    
		        	o_orders.order_items.order_id=oid;
		        	o_orders.orders.hide();
		        	o_orders.order_items.show();
		        	o_orders.order_items.get();
				}}
				,{tooltip:'Process Order',iconCls:'package_go',id:'confirm_send',handler:function()
		        {
		        	var g=Ext.getCmp(o_orders.orders.grid_id);
		        	var rows=g.getSelectionModel().getSelection();
		        	if(!rows.length){return;}
		        	var status=rows[0].get('order_status_id');
		        	YAHOO.util.Connect.asyncRequest('POST', 'index.php/prize/json_role_order_status/'+App.TOKEN
		        	,{success:function(o)
		        	{
						var s=YAHOO.lang.JSON.parse(o.responseText);
						if(!s.length) 
						{
							Ext.MessageBox.alert('Cannot process','You cannot upgrade this order any further');
							return;
						}
						else
						{
							var f=Ext.create('Spectrum.forms.order_upgrade',{status:status});
		        			f.loadRecord(rows[0]);
		        			var w=Ext.create('Spectrum.windows.order_upgrade',{items:f});
		        			w.show();
		        			w.on('hide',function()
		        			{
								o_orders.orders.get();
		        			})

						}
		        	}},'status='+status)

		        	
				}}
				,{tooltip:'View Order Invoice',iconCls:'money',id:'btn_orderinvoice',scope:this,hidden:true,handler:function()
		        {
		        	alert('TODO: invoice module incomplete');
				} 
				
				}
				,{tooltip:'Printable picklist',iconCls:'page_green',  handler:function()
				{
					var g=Ext.getCmp(o_orders.orders.grid_id);
		        	var rows=g.getSelectionModel().getSelection();
		        	if(!rows.length){return;}
					var post='order_id='+rows[0].get('order_id');
		        	YAHOO.util.Connect.asyncRequest('POST','index.php/prize/html_picklist/'+App.TOKEN,{failure:App.error.xhr,success:function(o)
		        	{
						var newWin = window.open();
						newWin.document.write(o.responseText);
						
						newWin.document.close();
		        	}},post); 

				}}
				,{tooltip:'PDF picklist',iconCls:'page_white_acrobat', hidden:true, handler:function()
				{
					//: first post order id to session data
					var g=Ext.getCmp(o_orders.orders.grid_id);
		        	var rows=g.getSelectionModel().getSelection();
		        	if(!rows.length){return;}
		        	
		        	var post='order_id='+rows[0].get('order_id');
		        	YAHOO.util.Connect.asyncRequest('POST','index.php/prize/post_order_id_pdfsession/'+App.TOKEN,{success:function(o)
		        	{
						var url = 'index.php/prize/pdf_picklist/'+App.TOKEN;//+post;
						var newWin = window.open();
						newWin.location.href=url;   
						newWin.document.close();
		        	}},post);
				}}
			];
					
					
			var g=Ext.create('Spectrum.grids.orders',
			{
				renderTo:'dg-myorders',
				org_type:o_orders.org_type,
				id:o_orders.orders.grid_id,
				bbar:bbar_buttons//these are displayed as top->bottom meaning left->right
			});

			g.on('selectionchange',function(sm,recs)
			{
				if(!recs.length)return;
				var row=recs[0];

				var status = row.get('order_status_id');
				//.log("org type is",o_orders.org_type);
				var locked=true;//disable is default
				if(status == 1 && o_orders.org_type==3)//if created and league
				{
					//enable
					locked=false;
				}
				if(status == 2 || status==3 || status==4)
				if(o_orders.org_type==2)//if association
				{
					//enable
					locked=false;
				}
				
					
				o_orders.orders.disableBtns(locked);

			});
			
			
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
				o_orders.prizes.hide();
				o_orders.order_items.hide();
			});

		},
		disableBtns:function(bool)
		{
			var btns = ['btn_prizeorder','btn_cart'];// 'btn_orderinvoice' , 'confirm_send'
			for(i in btns)
			{
				var btn=btns[i];
				if(Ext.getCmp(btn))
				{
					Ext.getCmp(btn).setDisabled(bool);
				}
			}
		}
		,hide:function()
		{
			
			Ext.getCmp(o_orders.orders.grid_id).collapse();
			//Ext.get('ctr-roster-grid').addCls('dghidden'); 	
		}
		
		,show:function()
		{
			//if it exists 
			//if(Ext.getCmp(o_orders.orders.grid_id))
			//	{Ext.getCmp(o_orders.orders.grid_id).expand();}
				
			var g=Ext.getCmp(o_orders.orders.grid_id);
			if(g)
			{
				g.expand();
				//g.show();
			}	
			//Ext.get('ctr-myorders').removeCls('dghidden'); //not needed
			
		}			
		
	}
	
	
	,order_items:
	{
		order_id:0,
		grid_id:'manage_l_orderitems_grid',
		get:function()
		{
			var g=Ext.getCmp(o_orders.order_items.grid_id);
			//update order id
			g.setTitle('Cart for Order # '+o_orders.order_items.order_id);
			g.store.proxy.extraParams.order_id =o_orders.order_items.order_id;
			//g.store.proxy.extraParams.season_id = o_teams.roster.season_id;
			g.store.load();
			
		},
		grid:function()
		{

			var g=Ext.create('Spectrum.grids.order_items',
			{
				renderTo:'dg-orderitems',
				id:o_orders.order_items.grid_id
			});
			g.on('collapse',function()
			{
				//collapse items so show orders 
				o_orders.orders.show();
				o_orders.order_items.hide();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		},
		hide:function()
		{
			Ext.get('ctr-orderitems').addCls('dghidden'); 	
			var g=Ext.getCmp(o_orders.order_items.grid_id);
			if(g)
			{
				g.hide(); 
			}
		},
		
		show:function()
		{
			//if it exists 
			var g=Ext.getCmp(o_orders.order_items.grid_id);
			if(g)
			{
				g.expand();
				g.show();
			}
				
			Ext.get('ctr-orderitems').removeCls('dghidden'); 
		}		
		
	}
	
	,prizes:
	{
		order_id:0,//prize itself does not depend on order, but this will be needed for add etc
		grid_id:'manage_l_orderprizes_grid',
		get:function()
		{

			var g=Ext.getCmp(o_orders.prizes.grid_id);
			g.order_id=o_orders.prizes.order_id;//update, not for refresh but for the forms to use
			
			g.refresh();
			
		},
		grid:function()
		{
			var bbar=
			[
				{xtype:'button',iconCls:'cart_edit',handler:function()
				{
					
					var g=Ext.getCmp(o_orders.prizes.grid_id);
					//var rows=g.getSelectionModel().getSelection();
					
					var rows=Ext.getCmp('id_for_resize_hack_scrollbars').getSelectionModel().getSelection();
					if(!rows.length){return;}
					var row=rows[0];
					
					var prize_id = row.get('prize_id');

					
					var f=Ext.create('Spectrum.forms.order_prizes',
					{
						prize_id:prize_id,
						order_id:g.order_id,
						prize_name:row.get('name')
					});
					
					var w=Ext.create('Spectrum.windows.order_prizes',{items:f});
					w.show();
				}}
			];//TODO: put a button here for 'add selected prize-> for form'
			var g=Ext.create('Spectrum.grids.prizes',
			{
				bbar:bbar,
				renderTo:'dg-o-prizes',
				id:o_orders.prizes.grid_id,
				//order_id:o_orders.prizes.order_id,
				//one of my custom params: overwrite trhe false: means we cannot create prizes
				hide_modify:true
			});
			
			g.on('collapse',function()
			{
				o_orders.prizes.hide();
				o_orders.orders.show();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
			
		},
		hide:function()
		{
			Ext.get('ctr-o-prizes').addCls('dghidden'); 
			var g=Ext.getCmp(o_orders.prizes.grid_id);
			if(g)
			{
				g.hide(); 
			}
		},
		
		show:function()
		{
			//if it exists 
			var g=Ext.getCmp(o_orders.prizes.grid_id);
			if(g)
			{
				g.expand();
				g.show();
			}
				
			Ext.get('ctr-o-prizes').removeCls('dghidden'); 
			
		}		
		
	}
	
	
	
}


var o_orders=new c_orders();
//now create grids
o_orders.orders.grid();
o_orders.order_items.grid();
o_orders.prizes.grid();