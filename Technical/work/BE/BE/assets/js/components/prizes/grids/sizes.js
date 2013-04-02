var size_model='PrizeSize';


var sizegr_cls='Spectrum.grids.prize.sizes';
if(!App.dom.definedExt(sizegr_cls)){
Ext.define(sizegr_cls,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    prize_id:null,
    refresh:function()
    {
 
    	this.store.proxy.extraParams.prize_id=this.prize_id;
    	this.store.loadPage(1);
    },
    
    deleteRec:function(size_id,prize_id)
    {
		
		var url='index.php/prize/post_delete_size/'+App.TOKEN;

		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,failure:App.error.xhr,success:function(o)
		{
			var r=o.responseText;
			if(r==-1)
			{
				Ext.MessageBox.alert("Error",'Cannot delete a default (non-custom) size.  To make this unavailable, disable the prices, or modify the quantities in stock');
			}
			else if(isNaN(r))
			{
				//alert(r);
				App.error.xhr(o);//php or sql error?
			}
			else
			{
				if(typeof this.refresh == 'function')
				{
					this.refresh();
				}
			}
			
		}},'size_id='+size_id+"&prize_id="+prize_id);
    },
    constructor     : function(config)
    { 
    	config.searchBar=false;
		config.bottomPaginator=true;
		config.rowEditable=false;
		
		config.collapsible= true;
		
        if(!config.id){config.id='cinvsizes_grid';}
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}

        if(Ext.getCmp(id)){Ext.getCmp(id).destroy();}

		if(!config.title) config.title = "Sizes";
		
		config.store=Ext.create( 'Ext.data.Store',
		{
			autoDestroy:false,autoSync :false,autoLoad :false,remoteSort:true,pageSize:100 ,
	        proxy: 
	        {   
	            type: 'rest',url: 'index.php/prize/json_sizes/'+App.TOKEN,
	            reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
	            extraParams:{prize_id:config.prize_id}
	        } ,model:size_model
		});
		
	    config.listeners=
	    {
	    	selectionchange: function(sm, selectedRecord) 
    		{
				if (!selectedRecord.length) return;
				var lu = selectedRecord[0].get('lu_size_id');
				var disab = false;
				if(lu && lu!='null')
		    	{//this is also tested for in server side
		    		disab=true;
		    	}
		    	Ext.getCmp('btn_size_delete').setDisabled(disab);	
		    	Ext.getCmp('btn_size_edit'  ).setDisabled(disab);	
			}
			,edit:function(e)
			{
				
				var data=e.record.data;
				
				var post="&category_name="+escape(data['category_name'])
						+"&category_desc="+escape(data['category_desc'])
						+"&category_id="  +data['category_id']
						+"&org_id="       +data['org_id'];
						
				//.log(post);
				var callback={scope:this,success:this.refresh};
				var url='index.php/prize/post_update_categories/'+App.TOKEN;
				//YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
			}
			
		};//end of listenres

		config.columns= 
	    [
        	{ text    : 'Name',flex:1,sortable : true,dataIndex: 'size_name' //, editor: { allowBlank: false }  
        	}	
			,{text    : 'Abbr',flex:1,sortable : true,dataIndex: 'size_abbr' //, editor: { allowBlank: false }   
			}	   
			//,{text     : '# of Prizes',flex:1,sortable : true,dataIndex: 'prize_count'   }	   
		];
		var buttons=[];
        if(config.bbar) {buttons=config.bbar;}
		config.bbar=null;
		
		
		if(!config.dockedItems)config.dockedItems=new Array();
		config.dockedItems.push({dock: 'top',xtype: 'toolbar',// bbar:
	        items: 
	        [  //filters would go here
	        ]
	        
		 });
		config.dockedItems.push(
		 {dock: 'bottom',xtype: 'toolbar',// bbar:
	        items:
			[
				{tooltip:'Add New Size',scope:this,iconCls:'add',handler:function()
	                {
	                	var f=Ext.create('Spectrum.forms.sizes',{ prize_id:this.prize_id});
	                	var win=Ext.create('Spectrum.windows.sizes',{items:f});
	                	
	                	
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
				,'->'
				,buttons
				,{scope:this,disabled:true,iconCls:'pencil',text:'',id:'btn_size_edit',
					tooltip:'Edit Size',handler:function()
					{
						
						var rowsSelected=this.getSelectionModel().getSelection();
						if(!rowsSelected.length){return;}//in case no rows selected
						var row=rowsSelected[0];
						var lu = rowsSelected[0].get('lu_size_id');
						
						if(lu && lu!='null') {return;}//also checks for this in stored procedure
						var f=Ext.create('Spectrum.forms.sizes',{ });
						f.loadRecord(row);
	                	var win=Ext.create('Spectrum.windows.sizes',{items:f});
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
				,'-'
				
				,{scope:this,disabled:true,iconCls:'delete',text:'',id:'btn_size_delete',
					tooltip:'Delete Size',handler:function()
					{
						
						var rowsSelected=this.getSelectionModel().getSelection();
						if(!rowsSelected.length){return;}//in case no rows selected
						var row=rowsSelected[0];
						this.deleteRec(row.get('size_id'),row.get('prize_id'));

					}
				}
				
			]
		});
    
    
        this.callParent(arguments);
	}
	
	
}); }



