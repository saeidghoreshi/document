var c_prizes= function(){this.construct();};
c_prizes.prototype=
{
	construct:function()
     {
         Ext.QuickTips.init();
     },	

	
	prizes:
	{
		grid_id:'prizes_images_dataview_grid',//some stupid fucker at SENCHA made all his css attached to this id, instead of using classes. check DataView.css
		get:function()
		{
			
			
			Ext.getCmp(o_prizes.prizes.grid_id).refresh();
		},
		//this is only needed since it is not really a grid, but a panel with a view, other messed up things
		getSelected:function()
		{
			return Ext.getCmp('id_for_resize_hack_scrollbars').getSelectionModel().getSelection();
		},
		grid:function()
		{
			var bbar=
			[
				{tooltip:'Price',iconCls :'money',id:'btn_prizes_price',scope:this, handler:function()
				{
					var rows=o_prizes.prizes.getSelected();
					//var g=Ext.getCmp(o_prizes.prizes.grid_id);
					//var rows=g.store.getSelectionModel().getSelection();
					if(!rows.length){return;}
					o_prizes.prizes.collapse();
					o_prizes.prices.prize_id=rows[0].get('prize_id');
					o_prizes.prices.get();
					o_prizes.prices.show();
				}},
				{tooltip:'Inventory',iconCls :'package',id:'btn_prizes_inventory',scope:this, handler:function()
	            {
					var rows=o_prizes.prizes.getSelected();
					//var g=Ext.getCmp(o_prizes.prizes.grid_id);
					//var rows=g.store.getSelectionModel().getSelection();
					if(!rows.length){return;}
					o_prizes.prizes.collapse();
					o_prizes.inventory.prize_id=rows[0].get('prize_id');
					o_prizes.inventory.get();
					o_prizes.inventory.show();
	            }}
				,{tooltip:'Sizes',iconCls :'chart_bar',id:'btn_prizes_sizes',scope:this, handler:function()
	            {
					var rows=o_prizes.prizes.getSelected();
					//var g=Ext.getCmp(o_prizes.prizes.grid_id);
					//var rows=g.store.getSelectionModel().getSelection();
					if(!rows.length){return;}
					o_prizes.prizes.collapse();
					o_prizes.sizes.prize_id=rows[0].get('prize_id');
					o_prizes.sizes.get();
					o_prizes.sizes.show();
	            }}
				,{tooltip:'Manage Images and Assets',iconCls:'pictures',scope:this,handler:function()
		    	{
		    		var rows=o_prizes.prizes.getSelected();
					if(!rows.length){return;}
		            var row=rows[0];
		            o_prizes.prizes.collapse();
					o_prizes.assets.prize_id=rows[0].get('prize_id');
					o_prizes.assets.get();
					o_prizes.assets.show();
		    		//var assets_grid=Ext.create('Spectrum.grids.prize.assets',{prize_id:prize_id,renderTo:'dg-manage-assets'});
		    	}}
			];
			var g=Ext.create('Spectrum.grids.prizes',{bbar:bbar,renderTo:'dg-manage-prizes',id:o_prizes.prizes.grid_id});
			g.on('expand',function()
			{
				o_prizes.sizes.hide();
				o_prizes.prices.hide();
				o_prizes.inventory.hide();
				o_prizes.assets.hide();
				o_prizes.prizes.get();
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		},
		show:function()
		{
			Ext.get('ctr-dg-manage-prizes').removeCls('dghidden');
			var g=Ext.getCmp(o_prizes.prizes.grid_id);
			if(g)
			{
				g.buildCategoryFilter();
				g.show();
			}
		},
		hide:function()
		{
			Ext.get('ctr-dg-manage-prizes').addCls('dghidden');
			var g=Ext.getCmp(o_prizes.prizes.grid_id);
			if(g)
			{
				g.hide();	
			}
		}
		,collapse:function()
		{			
			Ext.getCmp(o_prizes.prizes.grid_id).collapse();
		}
		,expand:function()
		{
			Ext.getCmp(o_prizes.prizes.grid_id).expand();
		}
	},
	
	warehouses:
	{
		grid_id:'dg-manage-warehouses-id',
		get:function()
		{
			Ext.getCmp(o_prizes.warehouses.grid_id).refresh();
		},
		grid:function()
		{
			var g=Ext.create('Spectrum.grids.warehouses',{renderTo:'dg-manage-warehouses',id:o_prizes.warehouses.grid_id});
		},
		show:function()
		{
			Ext.get('ctr-dg-manage-warehouses').removeCls('dghidden');
			Ext.getCmp(o_prizes.warehouses.grid_id).show();
			//Ext.getCmp(o_prizes.warehouses.grid_id).expand();
		},
		hide:function()
		{
			Ext.get('ctr-dg-manage-warehouses').addCls('dghidden');
			Ext.getCmp(o_prizes.warehouses.grid_id).hide();
		}
	},
	categories:
	{
		grid_id:'prize_cat_grid',
		get:function()
		{
			Ext.getCmp(o_prizes.categories.grid_id).refresh();
		},
		grid:function()
		{
			var g=Ext.create('Spectrum.grids.categories',{renderTo:'dg-manage-categories',id:o_prizes.categories.grid_id});
			/*g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			})*/
		},
		show:function()
		{
			Ext.get('ctr-dg-manage-categories').removeCls('dghidden');
			Ext.getCmp(o_prizes.categories.grid_id).show();
		},
		hide:function()
		{
			Ext.get('ctr-dg-manage-categories').addCls('dghidden');
			Ext.getCmp(o_prizes.categories.grid_id).hide();
		}
		
	}
	,inventory:
	{
		grid_id:'prize_inv_grid',
		warehouse_id:null,
		prize_id:null,
		get:function()
		{
			var g=Ext.getCmp(o_prizes.inventory.grid_id)  ;
			//g.warehouse_id=o_prizes.inventory.warehouse_id;
			g.prize_id=o_prizes.inventory.prize_id;
			g.refresh();
		},
		grid:function()
		{
			var g=Ext.create('Spectrum.grids.prize.inventory',{renderTo:'dg-manage-inventory',id:o_prizes.inventory.grid_id});
			g.on('collapse',function()
			{
				o_prizes.inventory.hide();
				o_prizes.prizes.expand();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		},//
		show:function()
		{
			
			
			Ext.get('ctr-dg-manage-inventory').removeCls('dghidden');
			Ext.getCmp(o_prizes.inventory.grid_id).buildWarehouseFilter();//evry time we show, refresh this menu
			Ext.getCmp(o_prizes.inventory.grid_id).expand();
			Ext.getCmp(o_prizes.inventory.grid_id).show();
		},
		hide:function()
		{
			Ext.get('ctr-dg-manage-inventory').addCls('dghidden');
			Ext.getCmp(o_prizes.categories.grid_id).hide();
		}
		
	}
	,sizes:
	{
		grid_id:'prize_sizes_grid',
		prize_id:null,
		get:function()
		{
			var g=Ext.getCmp(o_prizes.sizes.grid_id);
			
			g.prize_id=o_prizes.sizes.prize_id;
			g.refresh();
		},
		grid:function()
		{
			////);
			var g=Ext.create('Spectrum.grids.prize.sizes',{renderTo:'dg-manage-sizes',id:o_prizes.sizes.grid_id});
			g.on('collapse',function()
			{
				o_prizes.sizes.hide();
				o_prizes.prizes.expand();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		},
		show:function()
		{
			Ext.get('ctr-dg-manage-sizes').removeCls('dghidden');
			Ext.getCmp(o_prizes.categories.grid_id).show();
		},
		hide:function()
		{
			Ext.get('ctr-dg-manage-sizes').addCls('dghidden');
			Ext.getCmp(o_prizes.categories.grid_id).hide();
		}
		
	}
	,prices:
	{
		grid_id:'prize_prices_grid',
		prize_id:null,
		get:function()
		{
			var g=Ext.getCmp(o_prizes.prices.grid_id);
			g.prize_id=o_prizes.prices.prize_id;
			g.refresh();
		},
		grid:function()
		{
			var g=Ext.create('Spectrum.grids.prize.prices',{renderTo:'dg-manage-prices',id:o_prizes.prices.grid_id});
			g.on('collapse',function()
			{
				o_prizes.prices.hide();
				o_prizes.prizes.expand();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		},//
		show:function()
		{
			Ext.get('ctr-dg-manage-prices').removeCls('dghidden');
			Ext.getCmp(o_prizes.categories.grid_id).show();
		},
		hide:function()
		{
			
			Ext.get('ctr-dg-manage-prices').addCls('dghidden');
			Ext.getCmp(o_prizes.categories.grid_id).hide();
		}
		
	}
	
	
	
	,assets:
	{
		prize_id:null,
		grid_id:'prize_assets_grid_',
		grid:function()
		{
			var assets_grid=Ext.create('Spectrum.grids.prize.assets',
			{
				prize_id:-1
				,renderTo:'dg-manage-assets'
				,id:o_prizes.assets.grid_id
			});
			assets_grid.on('collapse',function()
			{
				o_prizes.assets.hide();
				o_prizes.prizes.expand();
				return false;
			});
			assets_grid.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		}
		,get:function()
		{
			var g=Ext.getCmp(o_prizes.assets.grid_id);
			//if(!g){alert('assets fail');return;}
			g.prize_id=o_prizes.assets.prize_id;
			g.refresh();
		}
		,show:function()
		{
			 
			Ext.get('ctr-dg-manage-assets').removeCls('dghidden');
			Ext.getCmp(o_prizes.assets.grid_id).show();
			
			Ext.getCmp(o_prizes.assets.grid_id).expand();
			
		}
		,hide:function()
		{
			Ext.getCmp(o_prizes.assets.grid_id).hide();
			Ext.get('ctr-dg-manage-assets').addCls('dghidden');
			//Ext.getCmp(o_prizes.assets.grid_id).collapse();
		}
	}
	
}


o_prizes=new c_prizes();
//create grids
o_prizes.prizes.grid();
o_prizes.categories.grid();
o_prizes.warehouses.grid();
o_prizes.inventory.grid();
o_prizes.sizes.grid();
o_prizes.prices.grid();
o_prizes.assets.grid();