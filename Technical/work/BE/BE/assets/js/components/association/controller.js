var c_assoc= function(){this.construct();};
c_assoc.prototype=
{
	construct:function()
	{
	 	Ext.QuickTips.init();                                                                              
	},  
	assoc:
	{
		grid_id:'my_assoc_grid_id_',
		get:function()
		{
			Ext.getCmp(o_assoc.assoc.grid_id).refresh();
		},
		grid:function()
		{
			var bbar=
			[
				{tooltip:'Manage Selected Association Users',iconCls :'fugue_user--pencil',disabled:false,handler:function()
	            {
	                var rowsSelected = Ext.getCmp(o_assoc.assoc.grid_id).getSelectionModel().getSelection();
 
        			if(rowsSelected.length==0)  return;  
 
                	o_assoc.users.org_id  = rowsSelected[0].get('org_id');
                	
                	o_assoc.users.get();
                	o_assoc.assoc.hide();
                	o_assoc.users.show();
				}}	
				,{tooltip:'Manage Selected Association Currencies',iconCls :'coins',disabled:false,handler:function()
	            {
	                var rowsSelected = Ext.getCmp(o_assoc.assoc.grid_id).getSelectionModel().getSelection();
 
        			if(rowsSelected.length==0)  {return; } 
 
                	o_assoc.curr.entity_id  = rowsSelected[0].get('entity_id');
                	o_assoc.curr.name  = rowsSelected[0].get('association_name');
               
                	//pass the id, get data and show hide
                	o_assoc.curr.get();
                	o_assoc.assoc.hide();
                	o_assoc.curr.show();
				} }
				,{tooltip:'Manage Selected Association Domains',iconCls :'fugue_application-home',disabled:false,handler:function()
	            {
	                var rowsSelected = Ext.getCmp(o_assoc.assoc.grid_id).getSelectionModel().getSelection();
 
        			if(rowsSelected.length==0) { return;  }
 
                	o_assoc.domain.org_id  = rowsSelected[0].get('org_id');
                	o_assoc.domain.name  = rowsSelected[0].get('association_name');
                	 
                	o_assoc.domain.get();
                	o_assoc.assoc.hide();
                	o_assoc.domain.show();
				}  	}
			];
			var g = Ext.create('Spectrum.grids.assoc',{id:o_assoc.assoc.grid_id,renderTo:'dg-assoc-grid',bbar:bbar});
			g.on('expand',function()
			{
				try{
 
				o_assoc.users.hide();
				o_assoc.curr.hide();
				o_assoc.domain.hide();
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
				o_assoc.assoc.get();
 
				}catch (e){console.log(e);}
				
			}); 
			g.show();
		}
		,show:function()
		{
			Ext.getCmp(o_assoc.assoc.grid_id).expand();
		}
		,hide:function()
		{
			Ext.getCmp(o_assoc.assoc.grid_id).collapse();
		}
	}
	,users:
	{
		grid_id:'assoc_usersgrid_id_',
		org_id:-1,
		get:function()
		{
			var g=Ext.getCmp(o_assoc.users.grid_id);
			g.org_id=o_assoc.users.org_id;
			//.log('refresh users with orgid'+g.org_id);
			g.refresh();
		},
		grid:function()
		{
			var g=Ext.create('Spectrum.grids.org_users',{renderTo:'dg-assoc-users',id:o_assoc.users.grid_id,org_type:2});//2 is orgtype=assoc
			
			g.on('collapse',function()
			{
				o_assoc.users.hide();
				o_assoc.assoc.show();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		}
		,show:function()
		{
			var g=Ext.getCmp(o_assoc.users.grid_id);
			
			if(g) g.expand();
			Ext.get('dg-assoc-users').removeCls('dghidden'); 
		}
		,hide:function()
		{
			Ext.get('dg-assoc-users').addCls('dghidden'); 
		}
	}
	,curr:
	{
		get:function()
		{
			 
			//currency is owned by entity. update the id int eh grid before refresh
			var g=Ext.getCmp(o_assoc.curr.grid_id);
			 
			if(!g)return;
			g.setTitle('Currency owned by '+o_assoc.curr.name);
			g.association_id=o_assoc.curr.association_id;
			g.entity_id=o_assoc.curr.entity_id;
			g.refresh();
			
		}
		,name:""
		,entity_id:-1
		,association_id:-1//deprecitaed
		,grid_id:'assc_curr_grid_'
		,grid:function()
		{
			 
			var g=Ext.create('Spectrum.grids.currency',{renderTo:'dg-assoc-currency',id:o_assoc.curr.grid_id});
			 
			g.on('expand',function()
			{ 
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout(); 
			}); 
			g.on('collapse',function()
			{
				o_assoc.curr.hide();
				o_assoc.assoc.show();
			});
		}
		,hide:function()
		{ 
 
			Ext.get('dg-assoc-currency').addCls('dghidden'); 
		}
		,show:function()
		{ 
			//remove class
			Ext.get('dg-assoc-currency').removeCls('dghidden'); 
			var g=Ext.getCmp(o_assoc.curr.grid_id);
			 
			if(!g)return;
			g.expand();
		}
		
		
	}
	,domain:
	{
		get:function()
		{
			var g=Ext.getCmp(o_assoc.domain.grid_id);
			 
			if(!g)return;
			g.setTitle('Domains used by '+o_assoc.domain.name);
			g.org_id=o_assoc.domain.org_id;
			g.refresh();
		}
		,org_id:-1
		,name:''
		,grid_id:'assoc_domain_grid'
		,grid:function()
		{
 
			var g=Ext.create('Spectrum.grids.domain',{renderTo:'dg-assoc-domain',id:o_assoc.domain.grid_id});
			 
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout(); 
				
			});
			g.on('collapse',function()
			{
				o_assoc.domain.hide();
				o_assoc.assoc.show();
			});
		}
		,hide:function()
		{
			
			Ext.get('dg-assoc-domain').addCls('dghidden'); 
		}
		,show:function()
		{ 
			//remove class
			Ext.get('dg-assoc-domain').removeCls('dghidden'); 
			var g=Ext.getCmp(o_assoc.domain.grid_id);
			 
			if(!g)return;
			g.expand();
		}
		
	}
}
 
var o_assoc=new c_assoc();

o_assoc.assoc.grid();//init grids once each. from now on only hide and show
o_assoc.users.grid();
o_assoc.curr.grid();
o_assoc.domain.grid();