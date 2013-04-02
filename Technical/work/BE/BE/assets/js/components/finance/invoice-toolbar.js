 
var PressedClass                ="";//"x-btn-default-toolbar-small-pressed btn_shinysilver";
var unPressedClass              ="";//"x-btn-default-toolbar-small-pressed btn_inactive";
 

//shouldnt this be in the controller?

var invoiceDateTypeListStore    =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_invoice_date_type_list/TOKEN:"+App.TOKEN+'/',{invoiceType:"invoice"});
var invoiceManage               =new invoiceManageClass();
invoiceManage.initInvoices();
      
toolbar = 
{
    items:
    [
    	{
			text:'',hidden:true,id:'filter_invoice_btn'//just so when someone references id, its ont broken
    	},
    	{
			text:'',hidden:true,id:'filter_invoice_draft_btn'
    	},
        //Filtering Invoice/Draft
 
		{
            text    : 'Invoices',
            iconCls:'fugue_document-invoice',
 
            handler:function()
            {
                var me=invoiceManage.grid;
                //Activate Invoices Button              
                var thisButton          ='filter_invoice_btn';
                Ext.getCmp(thisButton).removeCls(unPressedClass); 
                Ext.getCmp(thisButton).addClass(PressedClass); 
                
                var btn,otherBtn=['filter_invoice_draft_btn']
                for( i in otherBtn)if(otherBtn[i])
				{
    				btn=Ext.getCmp(otherBtn[i]);
					//SB:fix for IE9, was getting some undefiend elements here 
					if(btn){
                						
						btn.removeCls(PressedClass);     
						btn.addClass(unPressedClass);  
					}  
				}
                //Activate Invoices Button
                    
                //Set Default
                Ext.getCmp("invoice_invoice_type_combo"+me.config.generator).setDisabled(false);
                me.getStore().proxy.extraParams.invoice_type_id = Ext.getCmp("invoice_invoice_type_combo"+me.config.generator).getValue();
                me.getStore().load();
                
                invoiceDateTypeListStore.proxy.extraParams.invoiceType='invoice';
                invoiceDateTypeListStore.load();
                /*                       
                 var me=invoiceManage.grid;
                        //Activate Invoices Button              
                        var thisButton          ='filter_invoice_btn';
                        Ext.getCmp(thisButton).removeCls(unPressedClass); 
                        Ext.getCmp(thisButton).addClass(PressedClass); 
                        
                        var btn,otherBtn=['filter_invoice_draft_btn']
                        for(var i=0;i<otherBtn.length;i++)
					    {
    						btn=Ext.getCmp(otherBtn[i]);
					        //SB:fix for IE9, was getting some undefiend elements here 
					        if(btn){
                								
						        btn.removeCls(PressedClass);     
						        btn.addClass(unPressedClass);  
							}  
					    }
                        //Activate Invoices Button
                            
                        //Set Default
                        Ext.getCmp("invoice_invoice_type_combo"+me.config.generator).setDisabled(false);
                        me.getStore().proxy.extraParams.invoice_type_id = Ext.getCmp("invoice_invoice_type_combo"+me.config.generator).getValue();
                        me.getStore().load();
                        
                        invoiceDateTypeListStore.proxy.extraParams.invoiceType='invoice';
                        invoiceDateTypeListStore.load();*/
            }
            
        },            
        {
        	text    : 'Invoice Templates',
            iconCls : 'fugue_blue-document-invoice',
 
            handler:function()
            {
                var me=invoiceManage.grid;
                //Handle button activation
                var thisButton          ='filter_invoice_draft_btn';
                Ext.getCmp(thisButton).removeCls(unPressedClass); 
                Ext.getCmp(thisButton).addClass(PressedClass); 
                
                var btn,otherBtn=['filter_invoice_btn']
                for( i in otherBtn)if(otherBtn[i])
				{
    				btn=Ext.getCmp(otherBtn[i]);
					//SB:fix for IE9, was getting some undefiend elements here 
					if(btn){
                						
						btn.removeCls(PressedClass);     
						btn.addClass(unPressedClass);  
					}  
				}
                //Handle button activation
                
                //Loading Function
                Ext.getCmp("invoice_invoice_type_combo"+me.config.generator).setDisabled(true)
                me.getStore().proxy.extraParams.invoice_type_id=6;
                me.getStore().load();
                
                invoiceDateTypeListStore.proxy.extraParams.invoiceType='draft';
                invoiceDateTypeListStore.load();  
                
                /*                        var me=invoiceManage.grid;
                        //Handle button activation
                        var thisButton          ='filter_invoice_draft_btn';
                        Ext.getCmp(thisButton).removeCls(unPressedClass); 
                        Ext.getCmp(thisButton).addClass(PressedClass); 
                        
                        var btn,otherBtn=['filter_invoice_btn']
                        for(var i=0;i<otherBtn.length;i++)
					    {
    						btn=Ext.getCmp(otherBtn[i]);
					        //SB:fix for IE9, was getting some undefiend elements here 
					        if(btn){
                								
						        btn.removeCls(PressedClass);     
						        btn.addClass(unPressedClass);  
							}  
					    }
                        //Handle button activation
                        
                        //Loading Function
                        Ext.getCmp("invoice_invoice_type_combo"+me.config.generator).setDisabled(true)
                        me.getStore().proxy.extraParams.invoice_type_id=6;
                        me.getStore().load();
                        
                        invoiceDateTypeListStore.proxy.extraParams.invoiceType='draft';
                        invoiceDateTypeListStore.load();  */   
            }
            
        }
    ]
}; 
