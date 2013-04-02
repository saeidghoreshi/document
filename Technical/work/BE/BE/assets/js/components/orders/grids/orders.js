var poModel = 'PrizeOrder';


var gridClass = 'Spectrum.grids.orders';
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
    	var post_stats=new Array();
    	for(id in this.status_ids)
    	{
			if(this.status_ids[id])
				{post_stats.push(id);}
    	}
    	this.store.proxy.extraParams.status_ids = YAHOO.lang.JSON.stringify(post_stats);

		this.store.loadPage(1);//reload data, goto page 1
    },
    constructor     : function(config)
    {  
    	//first define custom paramters for spectrumgrids
		config.searchBar=false;
		config.bottomPaginator=true;
		config.rowEditable=true;
		//now manage actual parameters
		this.org_type=config.org_type;
		if(!config.id)
		{//do not overwrite input id
			//default id if needed
			config.id='orders-grid-';
		}
		if(Ext.getCmp(config.id))//if id is given, check if it has been already used
		{
			Ext.getCmp(config.id).destroy();
		}
		if(typeof config.collapsible == 'undefined') config.collapsible= true;
		
		config.store =Ext.create( 'Ext.data.Store',
    	{
    		model:poModel,autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:true,pageSize:100 ,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/prize/json_getorders/'+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{}
            }    
	    });
		config.columns=
		[
			{ dataIndex: 'created_name',text     : 'Creator',flex:1,sortable : true}		    
			,{ dataIndex: 'org_name',text     : 'Ordered For',flex:1,sortable : true}		    
			,{dataIndex: 'order_desc',text     : 'Desc',flex:1,sortable : true,editor: { allowBlank: false }}
            ,{dataIndex : 'status_name',width:120,text: 'Status'}
            ,{dataIndex : 'requested_date',width:120,text: 'Requested By'}
            ,{dataIndex : 'order_id',width:70,text: 'Order No.'}
		];
		
		if(typeof config.title == 'undefined')//do not just check false, because empty string is falsey, but it is valid input
		{
			config.title = 'Prize Orders'
		}
 
		//config.plugins=[Ext.create('Ext.grid.plugin.RowEditing', {clicksToEdit: 2,clicksToMoveEditor: 1,autoCancel: false})];
		config.listeners=
		{
			edit:function(e)//listener for row editor
			{
	 			var order=[];
    			for(i in e.record.fields.items) if(e.record.fields.items[i])
    			{
					var col  = e.record.fields.items[i].name;
					var data = e.record.data[col];
					data=escape(data);
					order.push(col+"="+data);
    			}
    			var post = order.join("&"); 

    			var url="index.php/prize/post_updateorder_details/"+App.TOKEN;
    			var callback={failure:App.error.xhr,success:function(o)
    			{
    				var r=o.responseText;
    				//if message is a negative number, or not a number at all, then a problem has happened
    				if(isNaN(r) || r<=0)
    				{
    					App.error.xhr(o); 
    					return; 
    				}
					e.record.commit();

    			},failure:App.error.xhr,scope:this};
    			YAHOO.util.Connect.asyncRequest('POST',url,callback,post);	
			}
		}
		if(typeof config.dockedItems=='undefined')
		{
			config.dockedItems=new Array();
		}
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items:
	        [
	        	{xtype:'button',id:'btn_order_status_filter',text:'Filter by Status',menu:[]}
	        	//filters go here if any
	        ]
		});
		if(!config.bbar)
			config.bbar=new Array();
		
		
		var add_btn={tooltip:'Create New Prize Order',iconCls:'add',scope:this,handler:function()
		{
			var win_id='create_order_window';
			var f=Ext.create('Spectrum.forms.order',  {window_id :win_id});
			var w=Ext.create('Spectrum.windows.order',{items:f,id:win_id});
			w.show();
			//on close , refresh in case the save worked
			w.on('hide',this.refresh,this);//third argument is scope
			
		}}
		var delete_btn={tooltip:'Delete',iconCls:'delete',scope:this,handler:function()
		{
		    var recs=this.getSelectionModel().getSelection();
				
			if(!recs.length){return;}//if no records selected
			
		    var msg='Are you sure?';//TODO: we could make the message show more details of the order : # items, etc
		    Ext.MessageBox.show({title:"Delete?",msg:msg,buttons: Ext.Msg.YESNO,icon: Ext.MessageBox.QUESTION,scope:this,fn:function(btn_id)
			{
				if(btn_id!='ok'&&btn_id!='yes'){return;}//if no do nothing
		        
				var recs=this.getSelectionModel().getSelection();
				
				if(!recs.length){return;}
				
				var rec=recs[0];
				var post= 'order_id='+rec.get('order_id');
				var url='index.php/prize/post_delete_order/'+App.TOKEN;
				var callback={scope:this,failure:App.error.xhr,success:function(o)
				{
					//check returnv alue
					var r=o.responseText;
					if(isNaN(r)||r<0)
					{
						Ext.MessageBox.alert('Error: ',r);
						return;
					}
					//if delete worked fine refresh 
					this.refresh();
				}};
				YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
			}});
		}};
		var help_btn={tooltip:'Order Status Information',iconCls:'help',hidden:true,scope:this,handler:function()
		{
		    //alert('TODO: a form will go here that lists every order status and its info , and what to do at each point - if anything');
		}}        
		var new_bbar = new Array();
		
		
		if(this.org_type != 2) new_bbar.push(add_btn);//if we are NOT an associatoin, we can create orders
		//new_bbar.push('-');
		new_bbar.push(help_btn);
		new_bbar.push('->');
		for(i in config.bbar)//array_merge splice hack thing
		{
			if(config.bbar[i] && config.bbar[i].iconCls)//added for IE
				new_bbar.push(config.bbar[i]);
		}
		
		new_bbar.push('-');
		new_bbar.push(delete_btn);
		
		config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items: new_bbar});
		config.bbar=null;

        this.callParent(arguments);
        this.buildStatusMenu();
	}
	,status_ids:[]
    ,set_status:function(status,bool)
    {
		this.status_ids[status]=bool;	
    }
	,buildStatusMenu:function()
	{
		 ///////////////////checkbox menu
        YAHOO.util.Connect.asyncRequest('GET','index.php/prize/json_order_status/'+App.TOKEN,{scope:this,failure:App.error.xhr,success:function(o)
        {
        	var img_src,icon,item,id,name,stat=YAHOO.lang.JSON.parse(o.responseText);
        	var stat_filter=new Array();
        	var onItemCheck=function (item, checked)
        	{
        		//save the check status, indexed by id.  this is  passed to the store.proxy on refresh
        		this.set_status(item.value,checked);
        		this.refresh();
		    }
        	for(i in stat)
        	{
        		id = stat[i]['status_id'];
        		if(id==null||typeof id == 'undefined') continue;//added for IE 
        		name=stat[i]['status_name'];
        		icon=stat[i]['icon'];//icon not used yet
        		//if an icon is given
        		if(icon)
        			{img_src="<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/"+icon+".png'   />";}
        		else
        			{img_src='';}
        		//icon in front of name:
        		name=img_src+name;
        	
        		this.status_ids[id]=true;
        		item= {text:name,value:id,checked:this.status_ids[id],checkHandler:onItemCheck,scope:this};
				stat_filter.push( item);
        	}
			//make the button 
			Ext.getCmp('btn_order_status_filter').menu=Ext.create('Spectrum.btn_menu',{items:stat_filter});
        }});
	}
})}