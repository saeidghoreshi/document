Ext.define('Ext.spectrumgrids.paymentList',    
{
        extend: 'Ext.spectrumgrids', 
        initComponent: function(config) 
        {       
            this.callParent(arguments);
        },
        
         override_edit          :null
        ,override_selectiochange:null
        ,override_itemdblclick  :null
        ,override_collapse      :null
        ,override_expand        :null
        
        ,config                 :null
        
        ,constructor : function(config) 
        {   
            var me=this;                             
            if(config.columns ==null)
            config.columns  =
            [   
                {
                    text        : "Type"
                    ,xtype       :'templatecolumn'
                    ,tpl         :'<div style="text-align:left;font-weight:bold;color:black;">{payment_type_name}</div>'
                    ,width      : 70
                },
                {
                    text        : "Action"
                    ,xtype      :'templatecolumn'
                    ,tpl        :'<div style=text-align:left;font-weight:bold;color:black;>{reason_type_name}</div>'
                    ,width      : 120
                },
                {
                    text        : "Charging Entity"
                    ,dataIndex  : "master_entity_name"
                    ,width      : 120
                }, 
                {
                    text        : "Paying Entity"
                    ,dataIndex  : "slave_entity_name"
                    ,width      : 120
                },
                {
                    text        : "Amount",
                    xtype       :'templatecolumn',
                    tpl         :'<div style="text-align:right;font-weight:bold;color:black;">{amount}</div>',
                    width      : 100
                }   
                ,{
                    text        : "Currency"
                    ,dataIndex  : "currency_type_name"
                    ,width      : 70
                }
                ,{
                    text        : "Status"
                    ,xtype      :'templatecolumn'
                    ,tpl        //:'{[values.payment_status_id == "1" ? "<div style=text-align:center;><img src='+config.imageBaseUrl+'accept.png></div>" :(values.payment_status_id == "2" ? "<div style=text-align:center;><img src='+config.imageBaseUrl+'delete.png></div>" : (values.payment_status_id == "6" ? "<img src='+config.imageBaseUrl+'delete.png>" :"<img src='+config.imageBaseUrl+'lightning.png>"))]}'
                    :'{[values.payment_status_id == "1" ? "<div style=text-align:center;color:green>Approved</div>" :(values.payment_status_id == "2" ? "<div style=text-align:center;color:red>Voided</div>" : (values.payment_status_id == "6" ? "<div style=text-align:center;color:red>Refunded</div>" :"<div style=text-align:center;color:red>Closed</div>"))]}'
                    ,width      : 70
                }
                ,{
                    text        : "Ref#"
                    ,dataIndex  : "ref_number"
                    ,width      : 140
                }
                ,{
                    text        : "Date"
                    ,dataIndex  : "created_on"
                    ,width      : 140
                }
                
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            config.topItems.push
            (
                
            );  
            config.bottomLItems.push
            (
                
            );       
            config.bottomRItems.push
            (
                //Cancel Payment
                {
                        id      : 'payment_void_btn',
                        icon    : 'http://endeavor.servilliansolutionsinc.com/global_assets/fugue/cross-shield.png',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Cancel Payment',
                        handler : function()
                        {
                             if(me.getSelectionModel().getSelection().length==0)
                             {
                                Ext.MessageBox.alert({title:"Error",msg:"Please select a Payment", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                             }
                             var record=me.getSelectionModel().getSelection()[0].data;
                             if(record.payment_status_id==2 || record.invoice_status_id==6) //Voided of Refunded
                             {
                                 Ext.MessageBox.alert({title:"Error",msg:"Payment is Already Cancelled", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }
                             if(record.reason_type_id==2)                                                  //No payment Cancellation fort deposit
                             {
                                Ext.MessageBox.alert({title:"Error",msg:"Deposit Payment can not be Cancelled", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});                                 
                                return;
                             }
                             
                             Ext.MessageBox.confirm('Payment Cancellation Action', "Are you sure ?", function(answer)
                             {     
                                    if(answer=="yes")
                                    {
                                         var post={}
                                         post["payment_id"]     =record.payment_id;
                                         post["amount"]         =record.amount;
                                         post["payment_on"]     =record.created_on;
                                         post["payment_type_id"]=record.payment_type_id;
                                         
                                         
                                         if(record.payment_type_id==2)                                  //DEBIT Record
                                         {
                                            post["auth_num"]    =record.authorization_num;
                                            post["trans_tag"]   =record.transaction_tag;
                                         }
                                         if(record.payment_type_id==3 || record.payment_type_id==4)     //CREDIT Record
                                         {
                                            post["orderid"]     =record.orderid;
                                            post["txrefnum"]    =record.txrefnum;
                                         }
                                         
                                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                         Ext.Ajax.request(
                                         {
                                             url     : 'index.php/finance/json_cancel_payment/TOKEN:'+App.TOKEN, 
                                             params  : post,
                                             success : function(response)
                                             {
                                                    box.hide();
                                                    var res=YAHOO.lang.JSON.parse(response.responseText);
                                                    if(res.result=="1" || res.result=="2" || res.result=="3")
                                                    {   
                                                        Ext.MessageBox.alert({title:"Status",msg:"Payment  Cancelled Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                        me.getStore().load();   
                                                    }
                                                    if(res.result=="-1")
                                                    {   
                                                        Ext.MessageBox.alert({title:"Error",msg:"Already Cancelled", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    }
                                                    if(res.result=="-2")
                                                    {   
                                                        Ext.MessageBox.alert({title:"Error",msg:"Paymetn Record Not found", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    }
                                                    if(res.result=="-3")
                                                    {   
                                                        Ext.MessageBox.alert({title:"Error",msg:"Not Enough Wallet Money in Invoice Issuer to Void this Payment", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                        me.getStore().load();   
                                                        Ext.getCmp("invoice_items_delete_void_btn").hide();   
                                                    }
                                                    
                                                    box.hide();
                                                    App.updatePanel();                          
                                             }
                                         });                                            
                                    }
                             });  
                        }
                    }
            );   
              
            
            if(config.fields==null)     config.fields       = 
            [
                'payment_id','amount','payment_status_id','status_name','currency_type_id','currency_type_name','payment_type_id','payment_type_name',
                'transaction_tag','authorization_num','cardbrand_type','cardbrand_value','message_type_type','message_type_value','orderid','txrefnum','procstatus_type',
                'procstatus_value','approvalstatus_type','approvalstatus_value','respcode_type','respcode_value','authcode_type','authcode_value','statusmsg_type',
                'statusmsg_value','resptime','description','master_entity_id','master_entity_name','slave_entity_id','slave_entity_name' ,'owned_by','owned_by_name' ,
                'reason_type_id','reason_type_name','ref_number','created_on'
            ];
            if(config.sorters==null)    config.sorters      = null;
            if(config.pageSize==null)   config.pageSize     = 1000;
            if(config.url==null)        config.url          = "/index.php/finance/json_get_payment_history/TOKEN:"+App.TOKEN;
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 400;
            if(config.groupField==null) config.groupField   = "reason_type_name";
            
            
            this.override_edit          =config.override_edit;
            this.override_selectiochange=config.override_selectionchange;
            this.override_itemdblclick  =config.override_itemdblclick;
            this.override_collapse      =config.override_collapse;
            this.override_expand        =config.override_expand;
            
            
            this.config=config;
            this.callParent(arguments); 
        }
        ,afterRender: function() 
        {  
            var me=this;
            if(!this.override_edit)
            {   
                this.on("edit",function(e)
                {   
                });
            }
            if(!this.override_selectionchange)                              
            {   
                this.on("selectionchange",function(sm,records)
                {   
                });
            }
            if(!this.override_collapse)                              
            {   
                this.on("collapse",function(){});
            }
            if(!this.override_expand)
            {   
                this.on("expand",function(){});
            }                                 
            this.callParent(arguments);         
        }
});
