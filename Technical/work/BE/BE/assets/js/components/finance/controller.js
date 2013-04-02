
//*************************************************************************************** INCOME STATEMENT
var incomeStatementManageClass= function(){this.construct();};
incomeStatementManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
     }, 
    
     init:function()
     {
        var me=this;                                    
        me.load_grid();//Ext.onReady(function(){me.load_grid();});  
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            renderTo        : "manage-list-report",
            title           : 'Income Statement',
            extraParamsMore : {},
            collapsible     :true,
            
            columns         :
            [    
                {
                    text        : "Balance",
                    xtype       :'templatecolumn',
                    tpl         :'<div style="text-align:left;font-weight:bold;color:gray;">{cumulative_balance}</div>',
                    flex        : 1
                }
            ],
            url             :"/index.php/finance/json_get_income_statement/TOKEN:"+App.TOKEN,
            //customized Components
            
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false  
            
            
        }
        me.grid = Ext.create('Ext.spectrumgrids.transaction',config);
      /*  me.grid.on("edit",function(e){}); 
        me.grid.on("expand",function(){}); */
        //me.grid.on('itemmouseenter', function(view, record, HTMLElement , index, e, Object ) {}); 
        
        //create last record as a SUM record
        var _store=me.grid.getStore();
        _store.on("load",function(e)
        {
            var i,total_balance=0;
            for(var i=0;i<_store.data.items.length;i++)
            	total_balance+=parseFloat(_store.data.items[i].data.cumulative_balance);
            
            var recordData={}
            recordData["account_name"]      ="Total Income Balance";
            recordData["cumulative_balance"]=total_balance;
            
            var newRec=Ext.ModelManager.create(recordData, "model_" +me.grid.config.generator);
            _store.insert(2,newRec);
        });
         
     }
}                                                        
//***************************************************************************************BalanceSheet
var balanceSheetManageClass= function(){this.construct();};
balanceSheetManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
     }, 
    
     init:function()
     {
        var me=this;                                    
        me.load_grid();//Ext.onReady(function(){me.load_grid();});  
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            renderTo        : "manage-list-report",
            title           : 'Balance Sheet',
            extraParamsMore : {},
            collapsible     :true,
            
            columns         :
            [    
                {
                    text        : "debit",
                    xtype       :'templatecolumn',
                    tpl         :'<div style="text-align:right;font-weight:bold;color:green;">{debit}</div>',
                    width       : 70
                }
                ,{
                    text        : "Credit",
                    xtype       :'templatecolumn',
                    tpl         :'<div style="text-align:right;font-weight:bold;color:red;">{credit}</div>',
                    width       : 70
                }
                ,{
                    text        : "Balance",
                    xtype       :'templatecolumn',
                    tpl         :'<div style="text-align:right;font-weight:bold;color:gray;">{cumulative_balance}</div>',
                    width       : 70
                }
                ,{
                    text        : ""
                    ,dataIndex  : "trans_datetime_display"
                    ,flex       : 1
                }
            ],
            url             :"/index.php/finance/json_get_balancesheet/TOKEN:"+App.TOKEN,
            //customized Components
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :false,
            searchBar       :false,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false
            
            
        }
        me.grid = Ext.create('Ext.spectrumgrids.transaction',config);
       /* me.grid.on("edit",function(e){}); 
        me.grid.on("expand",function(){}); */
        //me.grid.on('itemmouseenter', function(view, record, HTMLElement , index, e, Object ) {}); 
        
     }
}                                                       
//*************************************************************************************** CARD PAYMENT
var cardPaymentClass= function(){this.construct();};
cardPaymentClass.prototype=
{
     construct:function()
     {
         var me=this;
         me.init();
     }, 
     init:function()
     {
        var me=this;                                    
     },
     load_cc_payment_screen:function()
     {
         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
         Ext.Ajax.request(
         {
             url: 'index.php/finance/json_pay_invoice_direct/TOKEN:'+App.TOKEN,
             params  : {test:true},
             success : function(response)
             {
                  box.hide();
                  var res=YAHOO.lang.JSON.parse(response.responseText);
                  alert(YAHOO.lang.dump(res.result));
             }
             ,//always need a failure case. otherwise entire page is locked under the wait box forever
             failure: function( action){box.hide();App.error.xhr(action);}
         });    
     },                                 
     ____load_cc_payment_screen:function(paymentButton)
     {  
        var me=this;                                    
        var userAddressStore=new simpleStoreClass().make(['address_id','address_name'],"index.php/finance/json_get_user_addresses/TOKEN:"+App.TOKEN,{test:true});        
        var cc_conf=
        {
            width   :300,
            height  :400,
            bottomItems :
            [
                '->'
                ,{
                       xtype   : "button",
                       iconCls : 'application_go',
                       text    : 'Pay ',
                       tooltip : '',
                       pressed :true,
                       handler:function()
                       {
                           var post =_form.getForm().getValues();
                           if (_form.getForm().isValid()) 
                           {   
                               _form.getForm().submit({
                                   url: 'index.php/finance/json_pay_invoice_direct/TOKEN:'+App.TOKEN,
                                   //waitMsg: 'Processing...',
                                   params: post,
                                   success: function(form, action)
                                   {
                                       var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                   },//always need a failure case. otherwise entire page is locked under the wait box forever
                                 failure: function( f,action){box.hide();App.error.xhr(action.response);}
                               });
                           }
                  
                       }
                }
            ]
        }
        var _form=financeForms.form_cc_payment(userAddressStore);
        var final_form=new Ext.spectrumforms.mixer(cc_conf,[_form],['form']);
        show_window(312,435,final_form,'Credit Card Payment');
        
        //load record
        var gen=Math.random();
        Ext.define('model_'+gen,{extend: 'Ext.data.Model',fields: ['amount']});
        _form.loadRecord(Ext.ModelManager.create(
        {
            'amount'           : 100
        }, "model_"+gen ));       
     }
}                                                        
//***************************************************************************************  CHARGE ITEMS
var chargeItemsManageClass= function(){this.construct();};
chargeItemsManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
     }, 
    
     init:function()
     {
        var me=this;                                    
        me.load_grid();//Ext.onReady(function(){me.load_grid();});  
     },
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            renderTo        : "manage-list-setup",
            title           : 'Charge Items',
            extraParamsMore : {},
            collapsible     :true,
            
            //customized Components
            rowEditable     :false,
            groupable       :false,
            bottomPaginator :false,
            searchBar       :false,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false
        }
        me.grid = Ext.create('Ext.spectrumgrids.chargeItem',config);
            /*
        me.grid.on("edit",function(e){}); 
        me.grid.on("expand",function(){}); */
       
     }
}                                                        
//*************************************************************************************** INVOICE
var invoiceManageClass= function(){this.construct();};
invoiceManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
         
     }, 
     initInvoices:function()
     {
        var me=this;                                    
       me.loadInvoices();/* Ext.onReady(function()
        {
            me.loadInvoices();
        });  */         
     },
     initPendingInvoices:function()
     {
        var me=this;                                    
       me.loadPendingInvoices();/* Ext.onReady(function()
        {
            me.loadPendingInvoices();
        });  */         
     },
     initDraftInvoices:function()
     {
        var me=this;                                    
        me.loadDraftInvoices();/*Ext.onReady(function()
        {
            me.loadDraftInvoices();
        });  */
     },                                   
     loadDraftInvoices:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            renderTo        : "manage-list",
            title           : 'Draft Invoices',
            extraParamsMore : {},
            collapsible     :true,
            
            //customized Components
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :false,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false  
        }
        
        me.grid = Ext.create('Ext.spectrumgrids.draftinvoice',config);
        
            
       /* me.grid.on("edit",function(e){}); 
        me.grid.on("expand",function(){}); */
      
        /*
        me.grid.on('itemcontextmenu', function(view, record, HTMLElement , index, e, Object ) 
        {                           
            var contextMenu = new Ext.menu.Menu({
              items: 
              [
                  {
                    text        : 'Sent to Recipients',
                    iconCls     : 'application_cascade',
                    handler     : function()
                    {
                        me.grid.send_to_recipients(); 
                    }
                  }
                  ,{
                    text        : 'Edit Items',
                    iconCls     : 'application_add',
                    handler     : function()
                    {
                        me.grid.view_items();
                    }   
                  }
              ]
            });
            
            e.stopEvent();
            e.preventDefault();     
            contextMenu.showAt(e.getXY());     
            return false;
        }); 
        */
     },
     loadInvoices:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            renderTo        : "manage-list-invoice",
            title           : 'Invoices',
            extraParamsMore : {},
            collapsible     :true,
            url             :"/index.php/finance/json_get_invoices/TOKEN:"+App.TOKEN,
            
            //customized Components
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false
        }
        me.grid = Ext.create('Ext.spectrumgrids.invoice',config);
        
            
       /*me.grid.on("edit",function(e){}); 
        me.grid.on("expand",function(){});*/ 
        
        /*
        me.grid.on('itemcontextmenu', function(view, record, HTMLElement , index, e, Object ) 
        {                           
            var contextMenu = new Ext.menu.Menu({
              items: 
              [
                  {
                    text        : 'Use Deposit To pay',
                    iconCls     : 'database_add',
                    handler     : function()
                    {
                        me.grid.release_deposit_to_invoice(); 
                    }
                  }
                  ,{
                    text        : 'Items',
                    iconCls     : 'application_add',
                    handler     : function()
                    {
                         me.grid.view_invoice_items();
                    }   
                  }
                  
              ]
            });
            
            e.stopEvent();
            e.preventDefault();     
            contextMenu.showAt(e.getXY());     
            return false;
        }); 
        */
     },
     loadPendingInvoices:function()
     {  
        var me=this;                  
         
        var config=
        {
            generator       : Math.random(),
            owner           : me,
            
            renderTo        : "manage-list",
            title           : 'Pending Invoices',
            extraParamsMore : {},
            collapsible     :true,
            url             :"/index.php/finance/json_get_pending_invoices/TOKEN:"+App.TOKEN,
            
            //customized Components
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :false,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false 
        }
        me.grid = Ext.create('Ext.spectrumgrids.invoice',config);
        
        Ext.getCmp('invoice_invoice_type_combo'+config.generator).hide();

     }
     
}                                                        
//*************************************************************************************** PAY DEPOSIT
var payDepositManageClass= function(){this.construct();};
payDepositManageClass.prototype=
{
     construct:function()
     {
         var me=this;
     }, 
    
     init:function()
     {
        var me=this;                                    
        var _config=
        {
            width   : 300,
            height  : 250,
            bottomItems:   
            [
                '->'
                ,{   
                     xtype   :"button",
                     text    :"Save",
                     iconCls :'creditcards',
                     pressed :true,
                     width   :70,
                     tooltip :'Save',
                     handler :function()
                     {                     
                         if (form.getForm().isValid()) 
                         {   
                             var post={}
                             post=form.getForm().getValues();
                             form.getForm().submit({
                                 url     : 'index.php/finance/json_setup_entities_payment_plan/TOKEN:'+App.TOKEN,
                                 //waitMsg : 'Processing ...',
                                 params  : post,
                                 success : function(form, action)
                                 {
                                     var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                     if(res.result=="1")
                                     {
                                         win.Hide();
                                         Ext.MessageBox.alert({title:"Status",msg:"Deposit Payment Planned Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                     }           
                                     if(res.result=="-1")
                                     {
                                         win.Hide();                               
                                         Ext.MessageBox.alert({title:"Cannot Complete",msg:"Still non-closed Similar Deposit Request Exists", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                     }
                                 },
                                 failure: function(form, action){App.error.xhr(action.response);}
                             }); 
                         
                         }   
                     }                          
                }
            ]
        }    
        
        var chargeTypeStore     =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_charge_types/TOKEN:"+App.TOKEN+'/',{test:true});              
        var mastersStore        =new simpleStoreClass().make(['entity_id','entity_name'],"index.php/finance/json_get_masters/TOKEN:"+App.TOKEN+'/',{test:true});              
        var slavesStore         =new simpleStoreClass().make(['entity_id','entity_name'],"index.php/finance/json_get_slaves/TOKEN:"+App.TOKEN+'/',{test:true});              
        var currencyStore       =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_currencytypes/TOKEN:"+App.TOKEN+'/',{test:true});              
        
        
        var form=financeForms.form_setup_deposit_direct(chargeTypeStore,mastersStore,slavesStore,currencyStore);
        var final_form=new Ext.spectrumforms.mixer(_config,[form],['form']);
        
        var win_cnf=
        {
            title       : 'SetUp Entities Payment Plan',
            final_form  : final_form
        }
        var win=new Ext.spectrumwindow.finance(win_cnf);
        win.show();
        
        //Load Test records
        chargeTypeStore.on("load",function()
        {
                var index=Ext.getCmp('pay_deposit_direct_charge_type_id').store.find( 'type_id', '2', 0, true, false, false);
                var rec=chargeTypeStore.getAt(index);
                Ext.getCmp('pay_deposit_direct_charge_type_id').select(rec);
        });
        mastersStore.on("load",function()
        {
                var index=Ext.getCmp('pay_deposit_direct_master_entity_id').store.find( 'entity_id', '5', 0, true, false, false);
                var rec=mastersStore.getAt(index);
                Ext.getCmp('pay_deposit_direct_master_entity_id').select(rec);
        });
        slavesStore.on("load",function()
        {
                var index=Ext.getCmp('pay_deposit_direct_slave_entity_id').store.find( 'entity_id', '80', 0, true, false, false);
                var rec=slavesStore.getAt(index);
                Ext.getCmp('pay_deposit_direct_slave_entity_id').select(rec);
        });
        currencyStore.on("load",function()
        {
                var index=Ext.getCmp('pay_deposit_direct_currency_type_id').store.find( 'type_id', '1', 0, true, false, false);
                var rec=currencyStore.getAt(index);
                Ext.getCmp('pay_deposit_direct_currency_type_id').select(rec);
        });
        
        
        //load record
        var modelName="model_" +Math.random();
        Ext.define(modelName,{extend: 'Ext.data.Model',fields: ['pay_deposit_direct_amount']});
        form.loadRecord(Ext.ModelManager.create(
        {
            'pay_deposit_direct_amount' : '100'
        }, modelName));  
     }                                   
}      

//***************************************************************************************    walletBalanceManage
var walletBalanceManageClass= function(){this.construct();};
walletBalanceManageClass.prototype=
{
     construct:function()
     {
         var me=this;
     },
     init:function()
     {
        var me  =this;                                    
        var formEntityWalletConfig=
        {
            width   : 600,                
            height  : 470,
            bottomItems:   
            [
                '->'
                ,{   
                     xtype   :"button",
                     text    :"Add Wallet Currency",
                     iconCls :'money_add',
                     tooltip :'Add Wallet Currency to Selected Entities',
                     handler :function()
                     {                     
                         if (formEntityWallet.getForm().isValid()) 
                         {   
                             var records = orgRecGrid.getSelectionModel().getSelection();
                             if(records.length==0)
                             {
                                 Ext.MessageBox.alert({title:"No Amount",msg:"Please Select at least one Record and Fill in Amount", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }
                                                
                             var entity_amount_s='';                                                                                                                      
                             for(var i=0;i<records.length;i++)
                                 if(records[i].data.custom_empty!='' && parseInt(records[i].data.custom_empty)!=0)
                                    entity_amount_s  +=records[i].data.entity_id+','+records[i].data.custom_empty+'<==>';                           
                                    
                             if(entity_amount_s=='')
                             {
                                 Ext.MessageBox.alert({title:"No Amount",msg:"No Amount Entered", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }
                             entity_amount_s=entity_amount_s.substring(0,entity_amount_s.length-4);
                                             
                             var values =formEntityWalletFinal.getForm().getValues();                                
                             var post   ={}
                             post["entity_amount_s"]    =entity_amount_s;
                             post["currency_type_id"]   =values["currency_type_id"];
                             
                             var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                             Ext.Ajax.request(
                             {
                                 url     : 'index.php/finance/json_add_wallet_moneys/TOKEN:'+App.TOKEN,
                                 params  : post,
                                 success : function(response)
                                 {
                                      box.hide();
                                      var res=YAHOO.lang.JSON.parse(response.responseText);
                                      if(res.result=="1")         
                                          formEntityWalletWin.Hide();    
                                      App.updatePanel();
                                 },
                                 //always need a failure case. otherwise entire page is locked under the wait box forever
                                 failure: function( action){box.hide();App.error.xhr(action);}
                             });
                         }   
                     }                          
                }
            ]
        }    
        
        //var slavesTypeStore     =new simpleStoreClass().make(['type_id','type_name']    ,"index.php/finance/json_get_slaveTypeStore/TOKEN:"+App.TOKEN+'/',{test:true});              
        //var countryStore    =new simpleStoreClass().make(['country_id','country_name']  ,"index.php/facilities/json_get_country_store/TOKEN:"   +App.TOKEN,{test:false});
        //var regionStore     =new simpleStoreClass().make(['region_id','region_name']    ,"index.php/facilities/json_get_region_store/TOKEN:"    +App.TOKEN,{test:false});
        var currencyOwnTypeStore=new simpleStoreClass().make(['type_id','type_name']    ,"index.php/finance/json_get_currencyowntypes/TOKEN:"+App.TOKEN+'/',{test:true});              
        var orgLevelStore   =new simpleStoreClass().make(['org_type_id','org_type_name'],"index.php/finance/json_org_levels_store/TOKEN:"+App.TOKEN+'/',{test:true});        
        currencyOwnTypeStore.on("load",function()
        {
            if(currencyOwnTypeStore.data.items.length==0)
            {
                Ext.MessageBox.alert({title:"No Currency",msg:"Current Organization does not Own any Currency", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});    
                formEntityWalletWin.Hide();
                return;
            }
        });
        
        var orgRecGrid              =me.createOrgGrid(orgLevelStore);
        var formEntityWallet        =financeForms.form_increaseEntityWallet(currencyOwnTypeStore,orgRecGrid);
        var formEntityWalletFinal   =new Ext.spectrumforms.mixer(formEntityWalletConfig,[formEntityWallet],['form']);
        
        var formEntityWalletWinConfig=
        {
            title       : 'Add Wallet Currency',
            final_form  : formEntityWalletFinal
        }
        var formEntityWalletWin=new Ext.spectrumwindow.finance(formEntityWalletWinConfig);
        formEntityWalletWin.show();         
        //Load Default
        orgLevelStore.on("load",function()
        {
           var firstRecord=orgLevelStore.getAt(0);
           Ext.getCmp("transaction_level_filter").select(firstRecord); 
        });
     },                                   
     createOrgGrid:function(orgLevelStore)
     {
        var me=this;   
        var entity_cnf=
        {  
                    height          : App.MAX_GRID_HEIGHT,
                    generator       : Math.random(),
                    
                    owner           : me,
                    //url             : "index.php/finance/json_get_child_entities/TOKEN:"+App.TOKEN,
                    url             : "index.php/finance/json_getHierarchicalEntities/TOKEN:"+App.TOKEN,
                    orgLevelStore   :orgLevelStore,
                    
                    startCollapsed  : true,
                    collapsible     : false,
                    extraParamsMore :{},
                    
                    title           : 'Related Child Entities',  
                    /*selModel        : Ext.create('Ext.selection.CheckboxModel', 
                    {
                        mode        :'MULTI',
                        listeners   : {selectionchange: function(sm, selections){}}
                    }), 
                    */
                    columns         :
                    [    
                        {
                            text        : "Name",
                            dataIndex   : 'entity_name',
                            flex        :1
                        },
                        {
                            text        : "Amount <img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/money.png'>",
                            dataIndex   : "custom_empty",
                            flex        : 1,
                            editor      : 
                            {
                                xtype       :'textfield',
                                allowBlank  : true
                            }
                        }
                    ],
                
                    hiddenSeaonList :true,
                    autoLoad        :false,
                    collapsible     :false,
                    //customized Components
                    rowEditable     :false,
                    cellEditable    :true,
                    groupable       :true,
                    bottomPaginator :true,
                    searchBar       :true         
        }
        return Ext.create('Ext.spectrumgrids.entity',entity_cnf); 
     }
}      
                                            
//***************************************************************************************    PAYMENT LIST
var paymentListManageClass= function(){this.construct();};
paymentListManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
         
     }, 
    
     init:function()
     {
        var me=this;                                    
        me.load_grid();//Ext.onReady(function(){me.load_grid();});  
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
            renderTo        : "manage-list-transaction",
            title           : 'Payment List',
            extraParamsMore : {},
            collapsible     :true,
            
            //customized Components
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false  
            
            
        }
        
        me.grid = Ext.create('Ext.spectrumgrids.paymentList',config);                             
        
        /*me.grid.on("edit",function(e){}); 
        me.grid.on("expand",function(){}); */
        //me.grid.on('itemmouseenter', function(view, record, HTMLElement , index, e, Object ) {}); 

     }
}                                                        
//*************************************************************************************** REST
var resetManageClass= function(){this.construct();};
resetManageClass.prototype=
{
     construct:function()
     {
         var me=this;
     },     
     init:function()
     {
        var me=this;                                    
        me.reset();//Ext.onReady(function(){me.reset();});  
     },                                   
     reset:function()
     {  
         var me=this;                  
         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
         Ext.Ajax.request(
         {
             url     : 'index.php/finance/json_reset_transactions/TOKEN:'+App.TOKEN, 
             params  : {test:true},
             success : function(response)
             {
                  box.hide();
                  var res=YAHOO.lang.JSON.parse(response.responseText);
                  if(res.result=="1")           
                      Ext.MessageBox.alert({title:"Status",msg:"Reset Done Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
             }
         });    
     }
}                                                        
//*************************************************************************************** TRANSACTION
var transactionManageClass= function(){this.construct();};
transactionManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
         
     }, 
    
     init:function()
     {
        var me=this;                                    
        me.load_grid();//Ext.onReady(function(){me.load_grid();});  
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            renderTo        : "manage-list-transaction",
            title           : 'Transaction History',
            extraParamsMore : {},
            collapsible     :true,
            
            //customized Components
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :true,
            searchBar       :false,    
            
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false
        }
        
        me.grid = Ext.create('Ext.spectrumgrids.transaction',config);
        /*
        me.grid.getView().on("load",function()
        {
            me.grid.getView().addRowCls( me.grid.getStore().getAt(0), "test00");
        });
        
        me.grid.on("edit",function(e)
        {
            //e.record.data//e.record.data[e.field];
        }); 
        me.grid.on("expand",function()
        {                                    
            
        }); 
        me.grid.on('itemmouseenter', function(view, record, HTMLElement , index, e, Object ) 
        {                                                                                                                     
       
        }); 
        
        me.grid.getView().on('render', function(view) {});*/ 
     },
     handleTxnNumClick:function(txn_num)
     {
            var me=this;
            Ext.getCmp("txn_num").setValue(txn_num);
            
            transactionManage.grid.getStore().proxy.extraParams.txn_num=   txn_num;
            transactionManage.grid.getStore().load();
     }
}                                                        

var OnlineReportManageClass= function(){this.construct();};
OnlineReportManageClass.prototype=
{
     construct:function()
     {
         var me=this;
         
     }, 
    
     init:function()
     {
        var me=this;                                    
        
        //Build Panel
        Ext.create('Ext.form.Panel', 
        {
                width       : 400,
                height      : 200,
                title       : 'Report Builder',
                bodyPadding : 10,
                renderTo    : 'manage-list-onlinereport',
                
                items: 
                [   
                    {  
                        xtype       : 'combo',
                        labelStyle  : 'font-weight:bold;padding:0',
                        id          : 'onlinereport-type',
                        name        : 'onlinereport-type',
                        emptyText   : '',
                        fieldLabel  : 'Report Type',
                        labelWidth  : 150,    
                        forceSelection: false,
                        editable    : false,
                        displayField: 'type_name',
                        valueField  : 'type_id',
                        queryMode   : 'local',     
                        allowBlank  : false,
                        store       : Ext.create('Ext.data.Store', 
                        {
                            fields  :["type_id","type_name"],
                            data    :
                            [
                                    {"type_id":"1","type_name":"External Payments"},
                                    {"type_id":"2","type_name":"Internal Payments"},
                                    {"type_id":"3","type_name":"Withdraws"},
                                    {"type_id":"4","type_name":"Cancellations (Void & Refunds)"},
                                    {"type_id":"5","type_name":"Invoice & Payments"},
                                    {"type_id":"6","type_name":"Transactions"},
                                    
                            ]                                                   
                        })
                    },
                    {   
                        id          : 'onlinereport-fromdate',           
                        xtype       : 'datefield',
                        labelStyle  : 'font-weight:bold;padding:0',
                        name        : 'onlinereport-fromdate',
                        fieldLabel  : 'From',
                        labelWidth  : 150,          
                        flex        : 1,
                        allowBlank  : false,
                        editable    : false, 
                        listeners   :
                        {
                            change:function(){}
                        }
                    },
                    {   
                        id          : 'onlinereport-todate',           
                        xtype       : 'datefield',
                        labelStyle  : 'font-weight:bold;padding:0',
                        name        : 'onlinereport-todate',
                        fieldLabel  : 'To',
                        labelWidth  : 150,          
                        flex        : 1,
                        allowBlank  : false,
                        editable    : false, 
                        margins     : '0 0 0 10',
                        listeners   :
                        {
                            change:function(){}
                        }
                    }
                ],
                dockedItems: 
                {
                    xtype   : 'toolbar',
                    dock    : 'bottom',
                    ui      : 'footer',
                    items   : 
                    [
                        {
                            text: 'Build Report',
                            handler: function()
                            {
                                if
                                (
                                    Ext.getCmp("onlinereport-type").getValue()==''||
                                    Ext.getCmp("onlinereport-fromdate").getValue()==''||
                                    Ext.getCmp("onlinereport-todate").getValue()==''
                                )
                                {
                                    Ext.MessageBox.alert({title:"Error",msg:"Please Fill in all Parameters", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    return;
                                }
                                
                                var values  =this.up('form').getForm().getValues();
                                
                                var post    ={};
                                post["report_type"]             =values["onlinereport-type"];
                                post["onlinereport_fromdate"]   =values["onlinereport-fromdate"];
                                post["onlinereport_todate"]     =values["onlinereport-todate"];
                                
                                Ext.Ajax.request(
                                {
                                     url     : 'index.php/finance/json_getReportsContent/TOKEN:'+App.TOKEN,
                                     params  : post,
                                     success : function(response)
                                     {
                                          var res=YAHOO.lang.JSON.parse(response.responseText);
                                          var content=res.result;
                                          
                                          me.pdfBuilder(content);
                                          
                                          var reportWindow=window.open('','','width=800,height=600,scrollbars=yes');
                                          reportWindow.document.write(content);             
                                     }
                                });
                            }
                        }
                    ]
                }
            });
               
     },
     
     pdfBuilder:function(content)
     {
         var post=
         {
             content:content
         }
         Ext.Ajax.request(
         {
                          url     : 'index.php/finance/json_PDFBuilder/TOKEN:'+App.TOKEN, 
                          params  : post,
                          success : function(o)
                          {
                              var res=o.responseText;
                              var index1=res.indexOf('"result":"')+10;
                              var index2=res.indexOf('"}');
                              var filename=res.substring(index1,index2);
                              window.open('/tmp/'+filename);      
                          }
           });                                            
         
     }
     
}    
var OnlineReportManage=new OnlineReportManageClass();                                                    
