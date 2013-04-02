var cmodel = 'OrderItem';


var gridClass = 'Spectrum.grids.order_items';
if(!App.dom.definedExt(gridClass)){
Ext.define(gridClass,
{
    //extend: 'Ext.grid.Panel', 
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    
    refresh:function()
    {
		
		//load from url and go to page 1
		this.store.loadPage(1);
    },
    
    constructor     : function(config)
    {  
    	//first define custom paramters for spectrumgrids
		config.searchBar=false;
		config.bottomPaginator=true;
		
		//now manage actual parameters
		
		if(!config.id)
		{//do not overwrite input id
			//default id if needed
			config.id='orderitmss-grid-';
		}
		if(Ext.getCmp(config.id))//if id is given, check if it has been already used
		{
			Ext.getCmp(config.id).destroy();
		}
		config.collapsible=true;
		config.store =Ext.create( 'Ext.data.Store',
    	{
    		model:cmodel,autoDestroy:false,autoSync :false,
            autoLoad :true,
           // groupField:'prize_id',//needed for groupfeature
           groupField:'name',//needed for groupfeature
            remoteSort:true,pageSize:100 ,
            proxy: 
            {   type: 'rest',url: 'index.php/prize/json_getorderitems/'+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{order_id:-1}
            }    

	    });
	    config.features=
	    [
		    Ext.create('Ext.grid.feature.Grouping',
		    {//group by
		        groupHeaderTpl: '{name}'
		    })
		];  
		config.columns=
		[

           // {dataIndex : 'name',flex:1,text: 'Prize'}
           // ,
            {dataIndex : 'size_name',flex:1,text: 'Size'}
            ,{dataIndex : 'price',width:90,text: 'Price'}
            ,{dataIndex : 'currency_abbrev',width:90,text: 'Currency'}
            //,{dataIndex : 'available',width:90,text: 'In Stock'}
            ,{dataIndex : 'qty',width:90,text: 'Qty'
            	//,editor: { allowBlank: false ,maskRe:/\d/}//row editing disabled: only use form algorithm
            }

		];
		
		
		if(typeof config.title == 'undefined')//do not just check false, because empty string is false, but it is valid input
		{
			config.title = 'Cart'
		}
 
				
		if(typeof config.dockedItems=='undefined')
		{
			config.dockedItems=new Array();
		}
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items:
	        [
	        	//filters go here
	        ]
		}
		);
		

		    
		var cart_window= {xtype:'button',iconCls:'cart_edit',scope:this,handler:function()
		{

			var rows=this.getSelectionModel().getSelection();
			if(!rows.length){return;}
			var row=rows[0];
			
			var prize_id = row.get('prize_id');
			var order_id = row.get('order_id');
			var pname = row.get('name');
			var f=Ext.create('Spectrum.forms.order_prizes',
			{
				prize_id:prize_id,
				order_id:order_id,
				prize_name:pname
			});
			
			var w=Ext.create('Spectrum.windows.order_prizes',{items:f});
			w.show();
			w.on('hide',this.refresh,this);//refresh data on close
		}}   

			
		var new_bbar = new Array();
		//new_bbar.push(add_btn);
		new_bbar.push('->');
		for(i in config.bbar)//array_merge splice hack thing
			{new_bbar.push(config.bbar[i]);}
		
		//new_bbar.push('-');
		new_bbar.push(cart_window);
		
		//replace, now that we have added our own create/delete buttons around
		//config.dockedItems.push(new_bbar);
		config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items: new_bbar});
		config.bbar=null;
		    	
        this.callParent(arguments);
        //this.refresh();//loadd ata after create
	}
	
})}