//.warn('model depreciated : moved to location','models/order_sizes.js');
//model

var OSmodel = 'OrderPrizeSizes';


var pd="Spectrum.forms.order_prizes";
if(!App.dom.definedExt(pd)){
Ext.define(pd,
{
	extend: 'Ext.spectrumforms', 
	//extend: 'Ext.form.Panel', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
           
    constructor     : function(config)
    {        
		var standard_width=400;
		this.order_id=config.order_id;
		var id='ordersizeitems_form_';
	   
	   
		if(!config.id){config.id=id;}
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
	    
		config.title='';
		config.resizable=false;
		config.autoHeight  = true;
		config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false,width:standard_width};
		//
		//defaults: {anchor: '100%'},
		var small=55;
		var med=120;
		var save_btn={text   : 'Save',iconCls:'disk',scope:this,handler: function(o)
	    {
			//.log('TODOsaving items....one order id, then list of (size id,order id , since size ->prize)');
			var post ='order_id=' +Ext.getCmp('_form_order_prizes_oid_').getValue()
				     +"&prize_id="+Ext.getCmp('_form_order_prize_id_').getValue();
			var g=Ext.getCmp('dg_prize_order_sizes');
			
			var s=g.getStore();
			//var rec,records=s.getUpdatedRecords();
			var qty,item,rec,records=s.data.items;
			
			var order_sizes = new Array();
			for (i in records)
			{
				rec=records[i];
				item={};
				item.size_id=rec.get('size_id');
				qty = rec.get('qty');
				if( isNaN(qty) || !qty) {qty=0;}
				//new amount plus what has already been ordered
				qty=parseInt(qty) + parseInt(rec.get('cur1_qty'))+parseInt(rec.get('cur2_qty'))+parseInt(rec.get('cur3_qty'));
				
				
				item.qty = qty;
				order_sizes.push(item);
			}
			
			var post =post+ '&order_sizes='+YAHOO.lang.JSON.stringify(order_sizes);
			
			
			var url='index.php/prize/post_updateorder/'+App.TOKEN;
	        var callback={failure:App.error.xhr,success:function(o)
	        {
	            var r=o.responseText;
	            if(o.status != 200)
	            {
					App.error.xhr(o);					
	            }
	            else
	            {
	            	//should be success
	            	var title = 'Success';
	            	var msg="";
	            	var result = YAHOO.lang.JSON.parse(r);
	            	var total = result['CASH']+result['FAKE'];
	            	
	            	if (total==0)
	            	{
						title = "Could not process";
						msg= "Either there is not enough stock available, or no price is set for these items.";
	            	}
	            	else
	            	{
						msg ="Order updated by "+total;
	            	}
	            	
					Ext.MessageBox.alert(title,msg);
					Ext.getCmp('dg_prize_order_sizes').getStore().load();//refresh grid
	            }
	           
	        }};
	        YAHOO.util.Connect.asyncRequest('POST',url,callback,post);    
	    }}; 
		config.items= 
		[
			{xtype: 'displayfield',name : 'name',fieldLabel: 'Prize Name',value:config.prize_name}
			,{xtype: 'hidden',name : 'prize_id',value:config.prize_id,id:'_form_order_prize_id_'}
			,{xtype: 'hidden',name : 'order_id',value:config.order_id,id:'_form_order_prizes_oid_'}
			//,{xtype: 'datefield',width:standard_width,name : 'requested_date',fieldLabel: 'Requested By',allowBlank: true}
			,{xtype:'grid',id : 'dg_prize_order_sizes',height: 300,title:'Sizes Available',
				store:Ext.create( 'Ext.data.Store',
    			{
    				model:OSmodel,autoDestroy:false,autoSync :false,autoLoad :true,
		            proxy: 
		            {   type: 'rest',url: 'index.php/prize/json_prize_size_orderitems/'+App.TOKEN,
		                //reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
		                extraParams:{order_id:config.order_id,prize_id:config.prize_id}//takes input to form, and places it here
		            }    
			    })
	        	,columns: 
	        	[
	                {text   : 'Size',dataIndex: 'size_abbr'       ,flex:1}
	                ,{text   : 'In Stock',dataIndex: 'total_stock',width:small}
	                ,{text   : 'Order',dataIndex: 'qty'      ,width:small,
	                	editor: { allowBlank: false ,maskRe:/\d/,xtype: 'numberfield'}
	                }//qty to order
	                ,{header:'Cur1',id:'header_col_cur1',columns:
	                [
	                	{text   : 'Qty',  dataIndex: 'cur1_qty',  width:small},
	                	{text   : 'Price',dataIndex: 'cur1_price',width:small},
	                	{text   : 'Total',dataIndex: 'cur1_total',width:small}
	                ]},
	                {header:'Cur2',id:'header_col_cur2',columns:
	                [
	                	{text   : 'Qty',  dataIndex: 'cur2_qty',  width:small},
	                	{text   : 'Price',dataIndex: 'cur2_price',width:small},
	                	{text   : 'Total',dataIndex: 'cur2_total',width:small}
	                ]},
	                {header:'Cur3',id:'header_col_cur3',hidden:true,columns:
	                [
	                	{text   : 'Qty',dataIndex: 'cur3_qty',    width:small},
	                	{text   : 'Price',dataIndex: 'cur3_price',width:small},
	                	{text   : 'Total',dataIndex: 'cur3_total',width:small}
	                ]}
                ]
                ,plugins:[ Ext.create('Ext.grid.plugin.RowEditing', {clicksToEdit: 2,clicksToMoveEditor: 1,autoCancel: false}) ]
				,listeners:
				{
					edit:function(e)
					{
						
						e.record.commit();
					}
				}
				,bbar:
				[
					"->",save_btn
				]
			}//end of grid
		];
		this.callParent(arguments); 
	}
	
});	}