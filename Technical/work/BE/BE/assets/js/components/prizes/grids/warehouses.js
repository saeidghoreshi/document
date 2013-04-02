var w_model='Warehouse';

    
var grid_cls='Spectrum.grids.warehouses';
if(!App.dom.definedExt(grid_cls)){
Ext.define(grid_cls,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    //org_id:null,
    refresh:function()
    {
    	Ext.getCmp('btn_category_delete').setDisabled(true);
    	this.store.loadPage(1);
    },
    
    deleteRec:function(warehouse)
    {
		var url='index.php/prize/post_delete_warehouse/'+App.TOKEN;

		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,failure:App.error.xhr,success:function(o)
		{
			var r=o.responseText;
			if(isNaN(r)|| r<=0)
			{
				App.error.xhr(o);
			}
			else
			{
				if(typeof this.refresh == 'function')
				{
					this.refresh();
				}
			}
			
		}},'warehouse_id='+warehouse);//checks active org internally
    },
    constructor     : function(config)
    { 
    	config.searchBar=false;
		config.bottomPaginator=true;
		config.rowEditable=true;
 
        if(!config.id){config.id='pi_warehouse_grid_';}
        if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
        
        var buttons=[];
        if(config.bbar) {buttons=config.bbar;}
        config.bbar=null;
		
		if(!config.title) config.title= 'Warehouses';//,renderTo: renderTo,id:id,collapsible: false
	    	
		config.store=Ext.create( 'Ext.data.Store',
		{
			autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:true,pageSize:100 ,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/prize/json_getwarehouses/'+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{}
            }    
		    ,model:w_model
		});

	    config.listeners=
	    {
	        selectionchange: function(sm, rows) 
    		{
    			if(!rows.length)return;
    			var row=rows[0];
    			Ext.getCmp('btn_warehouse_delete').setDisabled(false);
				//.log(row);
			}
			,edit:function(e)
			{
				//e.record.reject();
				//var data=e.record.data;
				var post="warehouse_name=" +escape(e.record.data['warehouse_name'])
						+"&warehouse_desc="+escape(e.record.data['warehouse_desc'])
						+"&warehouse_id="  +e.record.data['warehouse_id']
						//+"&entity_org_id="       +data['entity_org_id']
						;
				var callback={scope:this,success:function(o)
				{
					e.record.commit();
				}};
				var url='index.php/prize/post_update_warehouse/'+App.TOKEN;
				YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
			}
			
		};//end of listenres
		config.columns=
	    [
        	{text     : 'Name',flex:1,sortable : true,dataIndex: 'warehouse_name', editor: { allowBlank: false }  }	
			,{text     : 'Description',flex:1,sortable : true,dataIndex: 'warehouse_desc' , editor: { allowBlank: false }   }	   
		   // ,{text     : '# of Prizes',flex:1,sortable : true,dataIndex: 'prize_count'   }	   
		];
		
		if(!config.dockedItems)config.dockedItems=new Array();
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items:
	        [
	        	//filters go here if any
	        ]
		});
		config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items:  //bbar
		[
			{tooltip:'Add New warehouse',scope:this,iconCls:'add',handler:function()
	            {
	            	var window_id = 'new_warehouse_windowfrm_';
	                var f=Ext.create('Spectrum.forms.warehouses',{window_id:window_id });
	                var win=Ext.create('Spectrum.windows.warehouses',{items:f,id:window_id});
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
			,{scope:this,disabled:true,iconCls:'delete',text:'',id:'btn_warehouse_delete',
				tooltip:'Delete',handler:function()
				{
					var rowsSelected=this.getSelectionModel().getSelection();
					if(!rowsSelected.length){return;}//in case no rows selected
					var row=rowsSelected[0];
					
					var w_id=row.get('warehouse_id');
					//build a confirm dialog box based on linked prizes
					YAHOO.util.Connect.asyncRequest('POST',
						'index.php/prize/json_warehouse_prizes/'+App.TOKEN,{scope:this,success:function(o)
					{
						var p,prizes=YAHOO.lang.JSON.parse(o.responseText);
						var count=prizes.length;
						if(!count)
						{
							this.deleteRec(w_id);
							return;
						}
						var msg='<p>The following inventory items will also be deleted, since they are stored in this warehouse.'
						+' Is this ok?</p>';
						for(i in prizes)
						{
							p=prizes[i];
							msg+= "<p>"+p['total']+" : "+p['name']+"</p>";
						}
						Ext.MessageBox.show({title:'Delete?',icon: Ext.MessageBox.QUESTION,msg:msg,scope:this,buttons: Ext.Msg.YESNO,fn:function(btn_id)
						{
							if(btn_id=='ok'||btn_id=='yes')
								{this.deleteRec(w_id);}
							
						}});
						
					}},'warehouse_id='+w_id  );
					
					
					
				}
			}
		]});

        this.callParent(arguments);
	}
	
	
}); }
