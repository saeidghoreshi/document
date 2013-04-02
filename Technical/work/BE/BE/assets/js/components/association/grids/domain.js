var w_model='Domain';
//created by Sam, used in 'manage associations' to handle  owned by that assoc 
//model defined in models/domain.js
var grid_cls='Spectrum.grids.domain';
if(!App.dom.definedExt(grid_cls)){
Ext.define(grid_cls,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    entity_id:-1,//depreciated
    org_id:-1,
    refresh:function()
    {
 
    	this.store.proxy.extraParams.org_id=this.org_id;//update in case it changed
    	this.store.load();
    },
 
    constructor     : function(config)
    { 
    	config.searchBar=false;
		config.bottomPaginator=false;
		config.rowEditable=true;
		config.collapsible= true;
		 
    	
        if(!config.id){config.id='adomain_curr_grid_';}
        if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
        
        var buttons=[];
        if(config.bbar) {buttons=config.bbar;}
        config.bbar=null;
		
		if(!config.title) config.title= 'Domains';//,renderTo: renderTo,id:id,
	    	
		config.store=Ext.create( 'Ext.data.Store',
		{
			autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:true,pageSize:100 ,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/associations/json_owned_domains/'+App.TOKEN,
                reader: {type: 'json'/*,root: 'root',totalProperty: 'totalCount'*/},
                extraParams:{org_id:config.org_id}
            }    
		    ,model:w_model
		});
 
		config.columns=
	    [
        	{text     : 'Domain Name',   flex:1,  sortable : true,dataIndex: 'domain'  }
        	,{text     : 'ID',    width:80,sortable : true,dataIndex: 'id'  }	
        	//,{text     : 'Is Active',    width:80,sortable : true,dataIndex: 'is_active'  }	
			//,{text     : 'Leagues Using',width:80,sortable : true,dataIndex: 'league_count'   } 
 
		];
		
		if(!config.dockedItems)config.dockedItems=new Array();
 
		config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items:  //bbar
		[
			{
				tooltip:'Add New Domain',
				scope:this,
				iconCls:'fugue_application-home--plus',
				handler:function()
	            {
	            	//
	            	var window_id = 'new_domainwindowfrm_';
	                var f=Ext.create('Spectrum.forms.domain',{window_id:window_id,org_id:this.org_id});
	                var win=Ext.create('Spectrum.windows.domain',{items:f,id:window_id});
					win.on('hide',function(o)
					{
						if(typeof this.refresh == 'function')
						{
							this.refresh();
						}
					},this);//pass scope as grid
					win.show();
				}
			}
			,buttons
			,'->'
			/*,{
				tooltip:'Toggle Is Active',
				scope:this,
				iconCls:'contrast',
				text:'', 
				handler:function()
				{
					
					var rowsSelected = this.getSelectionModel().getSelection();
		            if(rowsSelected.length==0){return;}
	 
					var id  =rowsSelected[0].get('id'); 
					var post="id="+id+"&is_active="+rowsSelected[0].get('is_active');
					
     				 var url="index.php/associations/post_domain_valid/"+App.TOKEN;
     				 var callback={failure:App.error.xhr,scope:this,success:function(o)
     				 {
     	 				 var r=o.responseText;
						 if(isNaN(r)||r<0)
						 {
							 App.error.xhr(o);									 
						 }
						 else
						 {
			 				this.refresh();
						 }								 
     				 }};
	 
	 				 YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
				}
				 
			 }
			,'-'*/
			,{
				tooltip:'Remove Selected Domain',
				scope:this,
				iconCls:'fugue_minus-button',
				text:'', 
				handler:function()
				{
					 //
				 var rowsSelected = this.getSelectionModel().getSelection();
	            if(rowsSelected.length==0){return;}
   
 
				var id  =rowsSelected[0].get('id');
				var name=rowsSelected[0].get('domain');
				  
        		 var msg="Are you sure you want to delete domain \""+name+"\" ?";
		 		 Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 		 {
 
		 			 if(btn_id!='yes'&&btn_id!='ok')return;
     				 var post="id="+id+"&org_id="+this.org_id;
     				 var url="index.php/associations/post_delete_domain/"+App.TOKEN;
     				 var callback={failure:App.error.xhr,scope:this,success:function(o)
     				 {
     	 				 var r=o.responseText;
						 if(isNaN(r))
						 {
							 App.error.xhr(o);									 
						 }
						 else if(r<0)
						 {
						 	 r = -1*r;
							 Ext.MessageBox.alert("Cannot delete",'We found '+r+" Leagues using this domain.");
						 }
						 else
						 {
			 				this.refresh();
						 }								 
     				 }};
	 
	 				 YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
	 			 },this);
				}
			}
		]});

        this.callParent(arguments);
	}
	
	
}); }
