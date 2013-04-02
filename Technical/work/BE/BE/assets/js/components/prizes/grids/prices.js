var price_model='PrizePrice';


formatBoolYN=function(value)
{
	return value =='t' ? 'Yes' : 'No';
}
var gr_cls='Spectrum.grids.prize.prices';
if(!App.dom.definedExt(gr_cls)){
Ext.define(gr_cls,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    prize_id:null,
    currency_id:null,
    refresh:function()
    {
    	this.store.proxy.extraParams.currency_id=this.currency_id;
    	this.store.proxy.extraParams.prize_id=this.prize_id;
    	this.store.loadPage(1);
 
    },

    constructor     : function(config)
    { 
    	config.searchBar=false;
		config.bottomPaginator=true;
		config.rowEditable=true;
		
        if(!config.id){config.id='cwprices_grid';}

	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
        
        var buttons=[];
        if(config.bbar) {buttons=config.bbar;}
        //var renderTo=config.renderTo;
        
		//this.org_id=config.org_id;
		
	    config.title='Prices';//,renderTo: renderTo,id:id,collapsible: true
		config.store= Ext.create( 'Ext.data.Store',
		{
			autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:true,pageSize:100 ,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/prize/json_getprices/'+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{prize_id:config.prize_id,currency_id:config.currency_id}
            }   ,
		    model:price_model
		}),
	        //stateful: true,width:"100%",height: 400,	
	    config.listeners=
	    {
	        /*selectionchange: function(sm, rows) 
    		{
    			if(!rows.length)return;
    			var row=rows[0];
    			//Ext.getCmp('btn_category_delete').setDisabled(false);
				//.log(row);
			}
			,*/
			edit:function(e)//also exist: beforeedit,validateedit
			{

				var data=e.record.data;

				e.record.commit();
				//.log(data);
				var active='f';
				
				if(data['is_active']){active='t';}
				
				var post="currency_id="+this.currency_id
						+"&prize_id="  +this.prize_id
						+"&size_id="   +data['size_id']
						+"&price="     +data['price']
						+"&is_active=" +active;
						
				//.log(post);
				var callback={scope:this,success:this.refresh,failure:App.error.xhr};
				var url='index.php/prize/post_updateprice/'+App.TOKEN;
				YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
				data['quantity_change']='';
			}
			
		}//end of listenres

		config.columns=
	    [
        	{ text    : 'Size',flex:1,sortable : true,dataIndex: 'size_name'  }	
			,{text    : '',flex:1,sortable : true,dataIndex: 'size_abbr'    }	   
			,{text    : 'Price',flex:1,sortable : true,dataIndex: 'price' ,width:100
			 ,editor: {  allowBlank: true}  /*,xtype: 'numberfield'*/ }     
			 
			,{
		        dataIndex: 'is_active',
		       
		        width: 50,
		        header: 'Active',
	     		renderer:formatBoolYN,//custom renderer MUST line up with combobox store
			    editor: {xtype: 'combobox', typeAhead: true,triggerAction: 'all',selectOnTab: true,store: [['t','Yes'],['f','No']]}
			}


		];

    
    	////////////
    	var buttons=[];
        if(config.bbar) {buttons=config.bbar;}
		config.bbar=null;
		
		if(!config.dockedItems)config.dockedItems=[];
		config.dockedItems.push(
		 {dock: 'top',xtype: 'toolbar',// bbar:
	        items: 
	        [  
	            {xtype:'button',menu:[],id:'price_currency_filter',text:'Select Currency'}
	        ]
	        
		 });
		 config.dockedItems.push(
		 {dock: 'bottom',xtype: 'toolbar',// bbar:
	        items:buttons
		 });
    
   	 	/////
        this.callParent(arguments);

		this.buildCurrencyFilter()
	}
	,buildCurrencyFilter:function()
	{
		var url='index.php/prize/json_active_currencies/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',url,{scope:this,success:function(o)
		{
			var cat=YAHOO.lang.JSON.parse(o.responseText);
			if(typeof cat['root'] != 'undefined') cat=cat['root'];//skip over paginator data
			var itemClick=function(o,e)
			{
        		Ext.getCmp('price_currency_filter').setText(o.text);
				this.currency_id=o.value;
				
				this.refresh();
			};
			var filter=new Array();
			var first=null;
			for(i in cat)
			{
				w=cat[i];
				name=w['type_code'];
				id  =w['type_id'  ];
				//def =w['is_default'];
				//icon=w['html_character']
				if(first===null)
				{
					first={id:id,name:name};// so we default to one selected always
				}
				filter.push({text:name,value:id,handler:itemClick,iconCls:w['icon'],scope:this});
			}
			Ext.getCmp('price_currency_filter').menu=Ext.create('Spectrum.btn_menu',{items:filter});
			
			if(first != null)
			{//always set up a default selection
				this.currency_id = first.id; 
				Ext.getCmp('price_currency_filter').setText(first.name);
			}
			
		}});
	}
}); }
