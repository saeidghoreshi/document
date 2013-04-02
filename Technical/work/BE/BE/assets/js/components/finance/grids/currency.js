
//created by Sam, used in 'manage associations' to handle currencies owned by that assoc 
//model defined in models/currency.js
var grid_cls='Spectrum.grids.currency';
if(!App.dom.definedExt(grid_cls)){
Ext.define(grid_cls,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    entity_id:-1,
    refresh:function()
    {
 
    	this.store.proxy.extraParams.entity_id=this.entity_id;//update in case it changed
    	this.store.load();
    },
 
    constructor     : function(config)
    { 
    	config.searchBar=false;
		config.bottomPaginator=false;
		config.rowEditable=false;
		config.collapsible= true;
		 
    	
        if(!config.id){config.id='as_curr_grid_';}
        if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
        
        var buttons=[];
        if(config.bbar) {buttons=config.bbar;}
        config.bbar=null;
		
		if(!config.title) config.title= 'Currency';//,renderTo: renderTo,id:id,
	    var w_model='Currency';
		config.store=Ext.create( 'Ext.data.Store',
		{
			autoDestroy:false,autoSync :false,autoLoad :false,remoteSort:false,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/finance/json_entity_owned_currencies/'+App.TOKEN,
                reader: {type: 'json'/*,root: 'root',totalProperty: 'totalCount'*/},
                extraParams:{entity_id:config.entity_id}
            }    
		    ,model:w_model
		});
 
		config.columns=
	    [
        	{text     : 'Name',        flex:1,sortable : true,dataIndex: 'type_code'//,      editor: { allowBlank: false }  
			}
			,{text     : 'Description',flex:1,sortable : true,dataIndex: 'type_descr' //,    editor: { allowBlank: false }  
			}
			,{text     : 'Abbrev',     flex:1,sortable : true,dataIndex: 'currency_abbrev'//,editor: { allowBlank: false }  
			}
			,{text     : 'HTML',       flex:1,sortable : true,dataIndex: 'html_character' //,editor: { allowBlank: false }  
			}
			,{text     : 'Silk Icon',  flex:1,sortable : true,dataIndex: 'icon'// ,          editor: { allowBlank: false }  
			}
 
		];
		
		if(!config.dockedItems)config.dockedItems=new Array();
 
		config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items:  //bbar
		[
			{
				tooltip:'Add New Currency',
				scope:this,
				iconCls:'coins_add',
				handler:function()
	            {
	            	
	            	var window_id = 'new_curr_windowfrm_';
	                var f=Ext.create('Spectrum.forms.currency',{window_id:window_id,entity_id:this.entity_id });
	                var win=Ext.create('Spectrum.windows.currency',{items:f,id:window_id});
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
			,{
				tooltip:'Modify Selected Currency',
				scope:this,
				iconCls:'coins_edit',
				text:'', 
				handler:function()
				{
					 
					 var rowsSelected = this.getSelectionModel().getSelection();
		            if(rowsSelected.length==0){return;}
   					var window_id = 'new_curr_windowfrm_';
	                var f=Ext.create('Spectrum.forms.currency',{window_id:window_id,entity_id:this.entity_id });
	                f.loadRecord(rowsSelected[0]);
	                var win=Ext.create('Spectrum.windows.currency',{items:f,id:window_id,title:'Edit Currency'});
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
			//,'-'
			,{
				tooltip:'Remove Selected Currency',
				scope:this,
				iconCls:'fugue_minus-button',
				text:'', 
				handler:function()
				{
					 //
					 var rowsSelected = this.getSelectionModel().getSelection();
	            if(rowsSelected.length==0){return;}
   
 
				var id  =rowsSelected[0].get('type_id');
				var name=rowsSelected[0].get('type_code');
				  
        		 var msg="Are you sure you want to delete currency \""+name+"\" ?";
		 		 Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 		 {
		 			 if(btn_id!='yes'&&btn_id!='ok')return;
     				 var post="type_id="+id;
     				 var url="index.php/finance/post_delete_owned_currency/"+App.TOKEN;
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
	 			 },this);
				}
			}
		]});

        this.callParent(arguments);
	}
	
	
}); }
