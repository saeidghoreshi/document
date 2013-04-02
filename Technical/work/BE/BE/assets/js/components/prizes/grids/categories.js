
var cat_model='PrizeCategory';
var gr_cls='Spectrum.grids.categories';
if(!App.dom.definedExt(gr_cls)){
Ext.define(gr_cls,
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

    	this.getStore().loadPage(1);
    },
    
    deleteRec:function(c_id)
    {
		var url='index.php/prize/post_delete_category/'+App.TOKEN;

		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,failure:App.error.xhr,success:function(o)
		{
			var r=o.responseText;
			if(isNaN(r)|| r<=0)
			{
				//alert(r);
				App.error.xhr(o);
			}
			else
			{
				if(typeof this.refresh == 'function')
				{
					this.refresh();
				}
			}
			
		}},'category_id='+c_id);
    },
    constructor     : function(config)
    { 
    	config.searchBar=false;
		config.bottomPaginator=true;
		config.rowEditable=true;
 
        if(!config.id){config.id='catgry_grid_';}

	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
	    
        var buttons=[];
        if(config.bbar) {buttons=config.bbar;}
		config.bbar=null;	//otherwise we get doubles
	    if(!config.title)config.title= 'Categories';
	    //,renderTo: renderTo,id:id,collapsible: false
		config.store= Ext.create( 'Ext.data.Store',
		{
			model:cat_model,autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:true,pageSize:100 ,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/prize/json_getcategories/'+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{}
            }  
		});
	        //stateful: true,width:"100%",height: 400,	
	    config.listeners=
	    {
	        selectionchange: function(sm, rows) 
    		{
    			if(!rows.length)return;
    			var row=rows[0];
    			Ext.getCmp('btn_category_delete').setDisabled(false);
			}
			,edit:function(e)
			{

				var data=e.record.data;
				
				var post="category_name="+escape(data['category_name'])
						+"&category_desc="+escape(data['category_desc'])
						+"&category_id="  +data['category_id'];
				
				var callback={scope:this,success:this.refresh};
				var url='index.php/prize/post_update_categories/'+App.TOKEN;
				YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
			}
			
		}//end of listenres
		config.columns= 
	    [
        	{text     : 'Name',flex:1,sortable : true,dataIndex: 'category_name', editor: { allowBlank: false }  }	
			,{text     : 'Description',flex:1,sortable : true,dataIndex: 'category_desc' , editor: { allowBlank: false }   }	   
			,{text     : '# of Prizes',flex:1,sortable : true,dataIndex: 'prize_count'   }	   
		];
		if(!config.dockedItems)config.dockedItems=new Array();
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items:
	        [
	        	//filter buttons go here, also this is where searchBar gets added
	        ]
		});
		
		config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items://bbar 
		[
			{tooltip:'Add New Category',scope:this,iconCls:'add',handler:function()
	            {
	            	var window_id="new_categorywindowform_";
	                var f=Ext.create('Spectrum.forms.categories',{ window_id:window_id});
	                var win=Ext.create('Spectrum.windows.categories',{items:f,id:window_id});
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
				scope:this,
				disabled:true,
				iconCls:'delete',
				id:'btn_category_delete',
				tooltip:'Delete',
				handler:function()
				{
					var rowsSelected=this.getSelectionModel().getSelection();
					if(!rowsSelected.length){return;}//in case no rows selected
					var row=rowsSelected[0];
					
					var c_id=row.get('category_id');
					//build a confirm dialog box based on linked prizes
					var url= 'index.php/prize/json_prizes_by_category/'+App.TOKEN  ;
					YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
					{
						var p,prizes=YAHOO.lang.JSON.parse(o.responseText);
						var count=prizes.length;
						if(!count)
						{
							this.deleteRec(c_id);
							return;
						}
						var msg='<p>The following prizes in this category will also be deleted, is this ok?</p>';
						for(i in prizes)
						{
							p=prizes[i];
							msg+= "<p>"+p['sku']+" : "+p['name']+"</p>";
						}
						Ext.MessageBox.confirm('Delete?',msg,function(btn_id)
						{
 
							if(btn_id=='ok'||btn_id=='yes')
								{this.deleteRec(c_id);}//delete rec is defined by the grid class
							
						},this);
					}},'category_id='+c_id  );
				}
			}
		]});
		
		
    
    
        this.callParent(arguments); 
	}
	
	
}); 
}// 