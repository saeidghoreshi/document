Ext.define('Ext.spectrumgrids.transaction',    
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
                    text        : "Payment Type"
                    ,dataIndex  : "payment_type_name"
                    ,width      : 150
                }
                ,{
                    text        : "Debitor"
                    ,dataIndex  : "invoice_master_ename"
                    ,width      : 150
                }
                ,{
                    text        : "Creditor"
                    ,dataIndex  : "invoice_slave_ename"
                    ,width      : 150
                }
                //if gl_type_id==2 ==> Assets OR EXP DR+  else CR+
                ,{
                    text        : "Debit",
                    xtype       : 'templatecolumn',
                    tpl         : '<div style=text-align:right;font-weight:bold;color:{[ (values.gl_type_id == "2" || values.category_type_id == 4 ) ? "black" :"red"]};>{debit}</div>',
                    width       : 70
                }
                ,{
                    text        : "Credit",
                    xtype       : 'templatecolumn',
                    tpl         : '<div style=text-align:right;font-weight:bold;color:{[values.gl_type_id == "2" || values.category_type_id == 4 ? "red" :"black"]};>{credit}</div>',
                    width       : 70
                }
                ,{
                    text        : "Balance",
                    xtype       : 'templatecolumn',
                    tpl         : '<div style=text-align:right;font-weight:bold;color:{[(values.cumulative_balance >=0 ) ? "black" :"red"]};>{cumulative_balance}</div>',
                    width       : 70
                },
                {
                    text    : 'Transaction',
                    columns :
                    [
                        {
                            text        : "Number",
                            xtype       :'templatecolumn',
                            tpl         :'<div style="text-align:left;color:Teal;cursor:pointer;"><a onclick="javascript:transactionManage.handleTxnNumClick({trans_num})">#{trans_num}</a></div>',
                            flex        : 1
                        },
                        {
                            text        : "Type"
                            ,xtype       :'templatecolumn'
                            ,tpl         :'<div style="text-align:left;font-weight:bold;color:teal;">{transaction_type_name}</div>'
                            ,width      : 140
                        }
                    ]
                },
                {
                    text    : "Description",
                    xtype   :'templatecolumn',
                    tpl     :'<div style="text-align:left;">{trans_datetime_display}<i>{transaction_description}</i></div>',
                    flex    : 1
                }
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            var currencyTypeStore   =new simpleStoreClass().make(['type_id','type_name']   ,"index.php/finance/json_get_currencytypes/TOKEN:"+App.TOKEN+'/',{test:true});        
            var accountTypeStore    =new simpleStoreClass().make(['type_id','type_name']   ,"index.php/finance/json_get_account_types/TOKEN:"+App.TOKEN+'/',{test:true});        
            var transTypeStore      =new simpleStoreClass().make(['type_id','type_name']   ,"index.php/finance/json_get_my_transaction_types/TOKEN:"+App.TOKEN+'/',{test:true});        
            
            config.topItems.push
            (
                //Filter By Currency
                {
                     id          : 'transaction_currency_type_id',
                     xtype       : 'combo',
                     name        : 'transaction_currency_type_id',
                     fieldLabel  : 'Currency',
                     labelWidth  : 60,
                     width       : 200,
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: true,
                     editable    : false,
                     displayField: 'type_name',
                     valueField  : 'type_id',
                     queryMode   : 'local',
                     labelStyle  : 'font-weight:bold',
                     store       : currencyTypeStore,
                     listeners  :
                     {
                             buffer:100,
                             change:function(obj,selected_id)
                             {
                                 me.getStore().proxy.extraParams.currency_type_id=selected_id;
                                 me.getStore().load();
                             }
                     }                                          
                },
                '-',
                //Filter By Account
                {
                     id          : 'transaction_account_type_id',
                     xtype       : 'combo',
                     name        : 'transaction_account_type_id',
                     fieldLabel  : 'Account',
                     labelWidth  : 60,
                     width       : 220,
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: true,
                     editable    : false,
                     displayField: 'type_name',
                     valueField  : 'type_id',
                     queryMode   : 'local',
                     labelStyle  : 'font-weight:bold',
                     store       : accountTypeStore,
                     listeners   :
                     {
                             buffer:1,
                             change:function(obj,selected_id)
                             {
                                     me.getStore().proxy.extraParams.account_type_id=selected_id;
                                     me.getStore().load();
                             }
                     }                                          
                },
                '-',
                //Filtering By from/to Date 
                {   
                    id          : 'spectrumgrids_txn_from_date',           
                    name        : 'spectrumgrids_txn_from_date',
                    emptyText   : 'From Date',
                    xtype       : 'datefield',
                    labelWidth  : 0,          
                    width       : 150, 
                    allowBlank  : true,
                    editable    : false, 
                },
                {   
                    id          : 'spectrumgrids_txn_to_date',           
                    name        : 'spectrumgrids_txn_to_date',
                    xtype       : 'datefield',
                    emptyText   : 'To date',
 

                    labelWidth  : 0,          
                    width       : 150, 
                    allowBlank  : true,
                    editable    : false, 
                },
                {
                     iconCls : 'fugue_magnifier--plus',
                     xtype   : 'button',
                     pressed : true,
                     tooltip : 'Apply Filter',
                     handler : function()
                     {                                                                                                 
                        var currency_type_id=((Ext.getCmp('transaction_currency_type_id')).isDisabled()?6:Ext.getCmp("transaction_currency_type_id").getValue());
                        var fromDate        =(Ext.getCmp("spectrumgrids_txn_from_date").getValue()!=null  ?new Date(Ext.getCmp("spectrumgrids_txn_from_date").getValue()):'');
                        var toDate          =(Ext.getCmp("spectrumgrids_txn_to_date").getValue()!=null    ?new Date(Ext.getCmp("spectrumgrids_txn_to_date").getValue()):'');
                        var account_id      =Ext.getCmp("transaction_account_type_id").getValue();
                                                                                                 
                        var rec=transTypeStore.getAt(transTypeStore.find("type_name","No Filter"));
                        Ext.getCmp("transaction_action_filter").select(rec);
                                                                               
                        if(fromDate=='' || toDate=='' )
                        {
                            Ext.MessageBox.alert({title:"Error",msg:"Please Fill in all three Filtering Items", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }                                                                                    
                        //Empty Txn_num box
                        Ext.getCmp("txn_num").setRawValue("");
                        me.getStore().proxy.extraParams.txn_num         ='';
                        me.getStore().proxy.extraParams.currency_type_id=currency_type_id;
                        me.getStore().proxy.extraParams.fromDate        =fromDate;
                        me.getStore().proxy.extraParams.toDate          =toDate;
                        me.getStore().proxy.extraParams.account_id      =account_id;
                        
                        me.getStore().load();                                      
                     }
                },
                {
                     iconCls : 'fugue_magnifier--minus',
                     xtype   : 'button',
                     pressed : true,
                     tooltip : 'Remove Filter',
                     handler : function()
                     {
                        Ext.getCmp("spectrumgrids_txn_from_date").setValue('');
                        Ext.getCmp("spectrumgrids_txn_to_date").setValue('');
                                                                               
                        var currency_type_id=Ext.getCmp('transaction_currency_type_id').getValue();
                        var fromDate        =(Ext.getCmp("spectrumgrids_txn_from_date").getValue()!=null ?new Date(Ext.getCmp("spectrumgrids_txn_from_date").getValue()):'');
                        var toDate          =(Ext.getCmp("spectrumgrids_txn_to_date").getValue()!=null   ?new Date(Ext.getCmp("spectrumgrids_txn_to_date").getValue()):'');
                        var account_id      =Ext.getCmp("transaction_account_type_id").getValue();
                        
                        var rec=transTypeStore.getAt(transTypeStore.find("type_name","No Filter"));
                        Ext.getCmp("transaction_action_filter").select(rec);
                        
                        //Empty Txn_num box
                        Ext.getCmp("txn_num").setRawValue("");
                        me.getStore().proxy.extraParams.txn_num         ='';         
                        
                        me.getStore().proxy.extraParams.currency_type_id=currency_type_id;
                        me.getStore().proxy.extraParams.fromDate        =fromDate;
                        me.getStore().proxy.extraParams.toDate          =toDate;
                        me.getStore().proxy.extraParams.account_id      =account_id;
                                               
                        me.getStore().load();
                        me.handleButtons();
                     }
                },
                '-',
                //Filter By Action
                {
                     id          : 'transaction_action_filter',
                     xtype       : 'combo',
                     name        : 'transaction_action_filter',
                     fieldLabel  : 'Action',
                     labelWidth  : 60,
                     width       : 350,
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: true,
                     editable    : false,
                     displayField: 'type_name',
                     valueField  : 'type_id',
                     queryMode   : 'local',
                     labelStyle  : 'font-weight:bold',
                     store       : transTypeStore,
                     margin      : '0 0 0 5',
                     listeners   :
                     {
                             buffer:1,
                             change:function(obj,selected_id)
                             {
                                     me.getStore().proxy.extraParams.action_id  =selected_id;
                                     //Empty Txn_num box
                                     Ext.getCmp("txn_num").setRawValue("");
                                     me.getStore().proxy.extraParams.txn_num    ='';
                                     me.getStore().load();
                             }
                     }                                          
                },
                //search by txn_num [Right-side]
                '-',
                '->'
                ,{              
                    xtype       : 'textfield',
                    labelStyle  : 'font-weight:bold;padding:0',
                    id          : 'txn_num',
                    name        : 'txn_num',
                    emptyText   : 'Search By TXN #',
                    fieldLabel  : '',
                    labelWidth  : 0,
                    width       : 120,
                    allowBlank  : true,
                    margin      : '0 0 0 10',
                    enableKeyEvents:true,
                    listeners:
                    {
                        keypress:function( _this, e, eOpts )
                        {  
                            if (e.keyCode == 13)
                            { 
                                me.getStore().proxy.extraParams.txn_num =   Ext.getCmp("txn_num").getValue();
                                me.getStore().load();
                            }
                        }   
                    },
                    margin      :'0 3 0 0 '
                    
                }
            );
            
            currencyTypeStore.on("load",function()
            {
                var rec=currencyTypeStore.getAt(0);
                Ext.getCmp("transaction_currency_type_id").select(rec);
            });
            accountTypeStore.on("load",function()
            {
                accountTypeStore.add({type_id:false,type_name:"All"}); 
                
                var index=accountTypeStore.find("type_name","All");
                var rec=accountTypeStore.getAt(index);
                Ext.getCmp("transaction_account_type_id").select(rec);
            });
            transTypeStore.on("load",function()
            {
                transTypeStore.add({type_name:"No Filter"}); 
                
                var index=transTypeStore.find("type_name","No Filter");
                var rec=transTypeStore.getAt(index);
                Ext.getCmp("transaction_action_filter").select(rec);
            });
            
            config.bottomLItems.push
            (
                
            );       
            config.bottomRItems.push
            (
                
            );   
              
            
            config.autoLoad             =false;
            if(config.fields==null)     config.fields       = ['transaction_id','account_id','category_type_id','category_type_name','currency_type_id','currency_type_name','payment_type_id','payment_type_name','transaction_type_id','transaction_type_name', 'trans_amount','trans_descr' ,'trans_datetime_display','invoice_id','balance','credit' ,'debit' ,'cumulative_balance','invoice_master_eid','invoice_master_ename','invoice_slave_eid','invoice_slave_ename','account_name','order','transaction_description','gl_type_id','trans_num'];
            if(config.sorters==null)    config.sorters      = null;
            if(config.groupers==null)   config.groupers     = ['order'];
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = "/index.php/finance/json_get_transaction_history/TOKEN:"+App.TOKEN;
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 500;
            if(config.groupField==null) config.groupField   = "account_name";
                               
            this.override_edit          =config.override_edit;
            this.override_selectiochange=config.override_selectionchange;
            this.override_itemdblclick  =config.override_itemdblclick;
            this.override_collapse      =config.override_collapse;
            this.override_expand        =config.override_expand;
                               
            this.config=config;
            this.callParent(arguments); 
        },
        afterRender: function() 
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
