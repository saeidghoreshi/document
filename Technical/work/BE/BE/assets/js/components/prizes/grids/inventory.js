

var inventory_model='Inventory';


var gr_cls='Spectrum.grids.prize.inventory';
if(!App.dom.definedExt(gr_cls)){
Ext.define(gr_cls,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    prize_id:null,
    warehouse_id:null,
    refresh:function()
    {
    	
    	//Ext.getCmp('btn_category_delete').setDisabled(true);
    	//.log('INVENTORY REFRESH STARTED');
    	this.store.proxy.extraParams.warehouse_id=this.warehouse_id;
    	this.store.proxy.extraParams.prize_id=this.prize_id;
    	this.store.loadPage(1);

    },

    constructor     : function(config)
    { 
    	config.searchBar=false;
		config.bottomPaginator=true;
		config.rowEditable=true;
		this.warehouse_id=config.warehouse_id;
		this.prize_id    =config.prize_id;
        if(!config.id){config.id='cinventory_grid';}
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
        
		//this.org_id=config.org_id;
	    if(!config.title)config.title= 'Warehouse Inventory';//,renderTo: renderTo,id:id,collapsible: true
		config.store= Ext.create( 'Ext.data.Store',
		{
			autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:true,pageSize:100 ,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/prize/json_getinventory/'+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{warehouse_id:config.warehouse_id,prize_id:config.prize_id}
            }  
		    ,model:inventory_model
		}),
	       // stateful: true,width:"100%",height: 400,	
	    config.listeners=
	    {
			edit:function(e)
			{
				var data=e.record.data;

				var post="&warehouse_id="+this.warehouse_id
						+"&prize_id="+this.prize_id
						+"&inventory_id="  +data['inventory_id']
						+"&size_id="  +data['size_id']
						+"&quantity_change="       +data['quantity_change'];
						
				var callback={scope:this,success:this.refresh};
				var url='index.php/prize/post_updateinventory/'+App.TOKEN;
				YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
				data['quantity_change']='';
			}
			
		};//end of listenres

		config.columns= 
	    [
        	{ text    : 'Size',flex:1,sortable : true,dataIndex: 'size_name'  }	
			,{text    : '',flex:1,sortable : true,dataIndex: 'size_abbr'    }	   
			,{text    : 'Current Stock',flex:1,sortable : true,dataIndex: 'quantity'   }	   
			,{text    : '+/-',flex:1,sortable : true,dataIndex: 'quantity_change' , 
			    editor: {  xtype: 'numberfield',allowBlank: false/*,minValue: 1,maxValue: 150000*/ }   
			}	   

		];
		var buttons=[];
        if(config.bbar) {buttons=config.bbar;}
		config.bbar=null;
		
		
		if(!config.dockedItems)config.dockedItems=new Array();
		config.dockedItems.push(
		 {dock: 'top',xtype: 'toolbar',// bbar:
	        items: 
	        [  
	            {xtype:'button',menu:[],id:'p_inv_warehouse_filter',text:'Select Warehouse'}
	        ]
	        
		 });
		 config.dockedItems.push(
		 {dock: 'bottom',xtype: 'toolbar',// bbar:
	        items:buttons
		 });

        this.callParent(arguments);
		this.buildWarehouseFilter()
	}
	,buildWarehouseFilter:function()
	{
		var url='index.php/prize/json_getwarehouses/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',url,{scope:this,success:function(o)
		{
			var wh=YAHOO.lang.JSON.parse(o.responseText);
			var itemClick=function(o,e)
			{
        		Ext.getCmp('p_inv_warehouse_filter').setText(o.text);
				this.warehouse_id=o.value;
				
				this.refresh();
			};
			var filter=new Array();
			var first=null;
			for(i in wh)
			{
				
				w=wh[i];
				name=w['warehouse_name'];
				id  =w['warehouse_id'  ];
				def =w['is_default'];
				
				filter.push({text:name,value:id,handler:itemClick,scope:this});
				if(first===null)
				{
					first={id:id,name:name};// so we default to one selected always
				}
				
			}
			
			Ext.getCmp('p_inv_warehouse_filter').menu=Ext.create('Spectrum.btn_menu',{items:filter});
			
			
			if(first != null)
			{
				this.warehouse_id = first.id; 
				Ext.getCmp('p_inv_warehouse_filter').setText(first.name);
			}
		}});
		
	}
}); }

        
//.log('grid.inv loaded');