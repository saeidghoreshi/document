
//the maintenance screen controller
var ctr_maintenance=
{
	//no need for prototype/constructor stuff
	
	init:function()
	{
		ctr_maintenance.setup_file();
	}
	
	,setup_file:function()
    {
		var file=prompt("Enter the task number from intervals, or a short useful message that will prefix"
		+"  the maintenance file, along with a generated timestamp."
		,'0000');
		//.log(file);
		
		if(file.split(' ').join('')=='')
			this.setup_file();//start over if invalid
		
		var post='file='+file;
		
		var url='index.php/permissions/post_maintenance_filename/'+App.TOKEN;
		
		var callback=
		{
			scope:this
			,failure:App.error.xhr,success:function(o)
			{
				this.setup_panel();
			}
		};
		
		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
    },
	setup_panel:function()
	{
		Ext.create('Ext.panel.Panel',
		{
			height:400
			,  layout: 
			{
			    type: 'hbox',       // Arrange child items vertically
			    align: 'stretch'   // Each takes up full width
			    
			}
			,title:'Spectrum Maintenance - Internal use Only'
			,width:'100%'
			,bodyPadding:5
			,renderTo:'mnt-panel-ctr'
			,items:
			[
				ctr_maintenance.roles.grid()
				,ctr_maintenance.methods.grid()
				,ctr_maintenance.menu.grid()
			]
		});
		
	},
	roles:
	{
		role_id:-1,
		role_name:'',//empty at first
 		grid_id:'mnt_roles_grid',
		grid:function()
		{
			
			var g=Ext.create('Spectrum.grids.all_roles',
			{
				//flex:0.25, 
				
				width:200,
				height:355,
				listeners:
				{
					
					select:function(model,row,index,o)
					{
						//http://docs.sencha.com/ext-js/4-0/#!/api/Ext.grid.Panel-event-select
 
						 
						ctr_maintenance.roles.role_id   = row.get('role_id'); 
						ctr_maintenance.roles.role_name = row.get('role_name');
						
						
						ctr_maintenance.methods.get();//update checkboxes on select
						ctr_maintenance.menu.get();//update this also
					}
				}
			});
 
			return g;
		}
 
	},
                
	menu:
	{
 		grid_id:'mnt_menu_grid',
		get:function()
		{
			var g=Ext.getCmp(ctr_maintenance.menu.grid_id);
			if(!g){return;}
			g.role_id=ctr_maintenance.roles.role_id;
			g.refresh();
		}
		,grid:function()
		{
		
			return Ext.create('Spectrum.grids.menu',
			{
				title:'Menu by Parent'
				,height:375
				,width:400
				,role_id:ctr_maintenance.roles.role_id
				,id:ctr_maintenance.menu.grid_id
				
			})	
		}
 
		
	},
	methods:
	{
		method_id:-1,
 		grid_id:'mnt_meth_grid',
		method_name:'',//selected row
		get:function()
		{
			Ext.getCmp(ctr_maintenance.methods.grid_id).role_id=ctr_maintenance.roles.role_id;
			Ext.getCmp(ctr_maintenance.methods.grid_id).refresh();
		}
		,grid:function()
		{
			var g=Ext.create('Spectrum.grids.methods',
			{
				//flex:0.25,
				width:200,
				height:375,
				role_id:ctr_maintenance.roles.role_id ,
				id:ctr_maintenance.methods.grid_id,
				url: "index.php/permissions/json_get_rolemethods/"+App.TOKEN,
				listeners:
				{
					select:function(model,row,index,o)
					{ 
						if(Ext.getCmp(ctr_maintenance.methods.grid_id).loading){return;}
						ctr_maintenance.methods.method_id   = row.get('method_id');
						ctr_maintenance.methods.method_name = row.get('method_name');
						
						
						ctr_maintenance.save_assignment(ctr_maintenance.roles.role_id ,ctr_maintenance.methods.method_id ,ctr_maintenance.methods.method_name,'t');
					}	
					,deselect:function(model,row,index,o)
					{
						if(Ext.getCmp(ctr_maintenance.methods.grid_id).loading){return;}
						ctr_maintenance.methods.method_id   = row.get('method_id');
						ctr_maintenance.methods.method_name = row.get('method_name');
						
						ctr_maintenance.save_assignment(ctr_maintenance.roles.role_id ,ctr_maintenance.methods.method_id ,ctr_maintenance.methods.method_name,'f');
					}	
					
				}
			});
			 
			return g;
		}
 
		
	},	
	save_assignment:function(role_id,method_id,method_name,allowed)
	{
 		var save,post={}
		
		post["role_id"]=role_id  ;
		post["method_id"]=  method_id;
		post["method_name"]=  method_name;
		post["controller_name"]=  Ext.getCmp(ctr_maintenance.methods.grid_id).controller_name;
		 
	     //save to different method depending on new permission
	    if(allowed=='t')
            save = 'post_rolemethod';
        else //delete
            save = 'post_delete_rolemethod';
 	

		
		 Ext.Ajax.request(
	     {
	         url        : 'index.php/permissions/'+save+'/'+App.TOKEN,
	         params     : post,
	         method     :"POST",
	         //scope      :this,
	         failure    :App.error.xhr,
	         success    : function(o)
	         {
	             //  
	            //   .log(o.responseText);
	         }
	     });          
		
	}
}

if(Ext.getCmp(ctr_maintenance.menu.grid_id)) Ext.getCmp(ctr_maintenance.menu.grid_id).destroy();
if(Ext.getCmp(ctr_maintenance.methods.grid_id)) Ext.getCmp(ctr_maintenance.methods.grid_id).destroy();
if(Ext.getCmp(ctr_maintenance.roles.grid_id)) Ext.getCmp(ctr_maintenance.roles.grid_id).destroy();
ctr_maintenance.init();
