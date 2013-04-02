 if(!financeForms)var financeForms={};   

 financeForms.form_invoice=function(currencyTypeStore)
    {   
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},

                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                  {              
                                     xtype       : 'textfield',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'title',
                                     emptyText   : '',
                                     fieldLabel  : 'Title',
                                     labelWidth  : 100,
                                     allowBlank  : false
                                     
                                 },      
                                 {  
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'currency_type_id',
                                     emptyText   : '',
                                     fieldLabel  : 'Currency Type',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'type_name',
                                     valueField  : 'type_id',
                                     queryMode   : 'local',     
                                     allowBlank  : false,
                                     store       : currencyTypeStore
                                     
                                 },
                                 {              
                                     xtype       : 'textareafield',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'description',
                                     emptyText   : '',
                                     fieldLabel  : 'Description',
                                     labelWidth  : 100,
                                     flex        : 1,
                                     height      : 250,
                                     allowBlank  : false
                                 }
                            ]
         });                                                          
         return  form;
    };
    //build invoiceItems
financeForms.form_invoice_items=function(chargeTypeStore)
    {   
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'}, 
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {  
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'charge_type_id',
                                     emptyText   : '',
                                     fieldLabel  : 'Charge Type',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'type_name',
                                     valueField  : 'type_id',
                                     queryMode   : 'local',     
                                     flex        : 1,
                                     allowBlank  : false,
                                     store       : chargeTypeStore                                                  
                                }
                                //price cost quantity
                                ,{
                                    xtype       : 'fieldcontainer',
                                    fieldLabel  : '',
                                    combineErrors: true,
                                    msgTarget   : 'side',          
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    layout      : 'hbox',        
                                    fieldDefaults: {labelAlign: 'top'},
                                    items       :
                                    [
                                        {              
                                            xtype       : 'textfield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'charge_price',
                                            emptyText   : '',
                                            fieldLabel  : 'Charge Price',
                                            labelWidth  : 100,
                                            flex        : 1,
                                            allowBlank  : false
                                            
                                        }
                                        ,{              
                                            xtype       : 'textfield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'charge_cost',
                                            emptyText   : '',
                                            fieldLabel  : 'Charge Cost',
                                            labelWidth  : 100,
                                            flex        : 1,
                                            allowBlank  : true,
                                            value       : 0,
                                            margins     : '0 0 0 10'
                                        }
                                        ,{              
                                            xtype       : 'textfield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'quantity',
                                            emptyText   : '',
                                            fieldLabel  : 'quantity',
                                            labelWidth  : 100,
                                            flex        : 1,
                                            value       : 1,
                                            allowBlank  : false,
                                            margins     : '0 0 0 10'
                                        }
                                    ]
                                }
                                //Description
                                ,{              
                                       xtype       : 'textarea',
                                       labelStyle  : 'font-weight:bold;padding:0',
                                       name        : 'invoice_item_description',
                                       emptyText   : '',
                                       fieldLabel  : 'Description',
                                       labelWidth  : 100,
                                       flex        : 1,
                                       allowBlank  : true
                                }
                            ] 
         }
         );                                                           
         return  form;
    };
    //Payment
financeForms.form_payment=function(currency_is_system,DevOrLive)
    {   
        
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'}, 
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },                
                            items: 
                            [
                                {
                                    xtype: 'fieldset',
                                    title: 'Amount',
                                    collapsible: false,
                                    margin      : '3 3 3 3 ',
                                    defaults: 
                                    {
                                        labelWidth: 89,
                                        anchor: '100%',
                                        layout: {
                                            type: 'hbox',
                                            defaultMargins: {top: 5, right: 5, bottom: 5, left: 5}
                                        }    
                                    },
                                    items:
                                    [
                                        {
                                            xtype       : 'fieldcontainer',
                                            fieldLabel  : '',
                                            combineErrors: true,
                                            msgTarget   : 'side',          
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            layout      : 'hbox',        
                                            fieldDefaults: {labelAlign: 'top'},
                                            items       :
                                            [
                                                {              
                                                    xtype       : 'textfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'payable_amount',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Payable',
                                                    labelWidth  : 100,
                                                    flex        : 1,
                                                    allowBlank  : false,
                                                    disabled    : true
                                                }
                                                ,{              
                                                    xtype       : 'textfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'amount',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Desirable',
                                                    labelWidth  : 100,
                                                    flex        : 1,
                                                    allowBlank  : false,
                                                    margins     : '0 0 0 10'
                                                }
                                            ]
                                       }
                                    ]
                                }
                                ,
                                {
                                    xtype       : 'fieldset',
                                    flex        : 1,
                                    title       : 'Payment Types',
                                    defaultType : 'radio', // each item will be a radio button
                                    layout      : 'anchor',
                                    margin      : '3 3 3 3 ',
                                    defaults    : 
                                    {
                                        anchor          : '100%',
                                        hideEmptyLabel  : false
                                    },
                                    items       :
                                    [
                                        {
                                            checked     : true,
                                            id          : "payment_types_internal",
                                            boxLabel    : 'Wallet (Internal)',
                                            name        : 'payment_type',
                                            inputValue  : '1'
                                        } 
                                        ,{
                                            id          : "payment_types_creditcard",
                                            boxLabel    : 'Credit Card',
                                            name        : 'payment_type',
                                            inputValue  : '2',
                                            disabled    : ((currency_is_system==0)?true:false)
                                        }
                                        ,{
                                            id          : "payment_types_interac",
                                            boxLabel    : 'Interac (Debit Card)',
                                            name        : 'payment_type',
                                            inputValue  : '3',
                                            disabled    : ((currency_is_system==0)?true:((DevOrLive=="DEV")?false:true))
                                        }
                                    ]
                                }
                            ]
         }
         );
                                                                      
         return  form;
    };
    //Pay deposit direct
financeForms.form_setup_deposit_direct=function(chargeTypeStore,mastersStore,slavesStore,currencyStore)
    {   
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'}, 
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {  
                                     id          : 'pay_deposit_direct_charge_type_id',
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'charge_type_id',
                                     emptyText   : '',
                                     fieldLabel  : 'Charge Type',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'type_name',
                                     valueField  : 'type_id',
                                     queryMode   : 'local',     
                                     flex        : 1,
                                     allowBlank  : false,
                                     store       : chargeTypeStore                                                 
                                 }
                                ,{                                                   
                                     id          : 'pay_deposit_direct_master_entity_id',
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'master_entity_id',
                                     emptyText   : '',
                                     fieldLabel  : 'Master',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'entity_name',
                                     valueField  : 'entity_id',
                                     queryMode   : 'local',     
                                     flex        : 1,
                                     allowBlank  : false,
                                     store       : mastersStore                                                   
                                 }
                                 ,{                 
                                     id          : 'pay_deposit_direct_slave_entity_id',                                  
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'slave_entity_id',
                                     emptyText   : '',
                                     fieldLabel  : 'Slaves',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'entity_name',
                                     valueField  : 'entity_id',
                                     queryMode   : 'local',     
                                     flex        : 1,
                                     allowBlank  : false,
                                     store       : slavesStore                                                   
                                 },
                                 {        
                                     id          : 'pay_deposit_direct_currency_type_id',                                           
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'currency_type_id',
                                     emptyText   : '',
                                     fieldLabel  : 'Currency',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'type_name',
                                     valueField  : 'type_id',
                                     queryMode   : 'local',     
                                     flex        : 1,
                                     allowBlank  : false,
                                     store       : currencyStore                                                   
                                 },
                                 {      
                                     id          : 'pay_deposit_direct_amount',        
                                     xtype       : 'textfield',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'amount',
                                     emptyText   : '',
                                     fieldLabel  : 'Amount',
                                     labelWidth  : 100,
                                     width       : 100,
                                     allowBlank  : false
                                     
                                 }
                                /*,{  
                                     id          : 'pay_deposit_direct_currency_type_id',
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'currency_type_id',
                                     emptyText   : '',
                                     fieldLabel  : 'Currency',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'type_name',
                                     valueField  : 'type_id',
                                     queryMode   : 'local',     
                                     flex        : 1,
                                     allowBlank  : false,
                                     store       : currencyTypeStore                                                 
                                 }
                                ,{  
                                    
                                     id          : 'pay_deposit_direct_card_type_id',
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'payment_type_id',
                                     emptyText   : '',
                                     fieldLabel  : 'Card Type',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'type_name',
                                     valueField  : 'type_id',
                                     queryMode   : 'local',     
                                     flex        : 1,
                                     allowBlank  : false,
                                     store       : cardTypeStore                                                  
                                 }
                                ,{              
                                    xtype       : 'textfield',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'amount',
                                    emptyText   : '',
                                    fieldLabel  : 'Amount',
                                    labelWidth  : 100,
                                    width       : 100,
                                    allowBlank  : false
                                }
                                //Description
                                ,{              
                                                    xtype       : 'textarea',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'description',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Description',
                                                    labelWidth  : 100,
                                                    flex        : 1,
                                                    allowBlank  : true
                                }*/ 
                            ]
         }
         );
                                                                      
         return  form;
    };
    
    //Increase Entity Wallet
financeForms.form_increaseEntityWallet=function(currencyTypeStore,orgRecGrid)
    {   
         var me=this;
         
         var form=new Ext.spectrumforms(
         {   
             autowidth   : true,
             autoHeight  : true,
              
             layout      : {type: 'vbox',align: 'stretch'}, 
             border      : false,
                                      
             fieldDefaults: {
                  labelAlign: 'top',
                  labelWidth: 100,
                  labelStyle: 'font-weight:bold'
             },      
             items: 
             [   
                  
                          {
                               id          : 'currency_type_id',
                               xtype       : 'combo',
                               name        : 'currency_type_id',
                               fieldLabel  : 'Currency',
                               labelWidth  : 60,
                               width       : 120,
                               allowBlank  : true,
                               mode        : 'local',
                               forceSelection: true,
                               editable    : false,
                               displayField: 'type_name',
                               valueField  : 'type_id',
                               queryMode   : 'local',
                               labelStyle  : 'font-weight:bold',
                               allowBlank  : false,
                               store       : currencyTypeStore
                          },
                          orgRecGrid
             ]
         }
         );
                                                                      
         return  form;
    };
    //Increase Entity Wallet for Me
financeForms.form_increaseMEEntityWallet=function()
    {   
         var me=this;
         var form=new Ext.spectrumforms(
         {   
            autowidth   : true,
            autoHeight  : true,
            
            layout      : {type: 'vbox',align: 'stretch'}, 
            border      : false,
                                    
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },      
            items: 
            [   
                {
                    xtype       : 'fieldset',
                    title       : 'Please Enter Desirable Amount to transfer to Your Organization Wallet Account',
                    collapsible : false,
                    margin      : '3 3 3 3 ',
                    defaults    : 
                    {
                        labelWidth: 89,
                        anchor: '100%',
                        layout: {
                            type: 'hbox',
                            defaultMargins: {top: 5, right: 5, bottom: 5, left: 5}
                        }    
                    },
                    items:
                    [
                        {
                            xtype           : 'fieldcontainer',
                            fieldLabel      : '',
                            combineErrors   : true,
                            msgTarget       : 'side',          
                            labelStyle      : 'font-weight:bold;padding:0',
                            layout          : 'hbox',        
                            fieldDefaults   : {labelAlign: 'top'},
                            items           :
                            [
                                {              
                                    xtype       : 'textfield',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'amount',
                                    emptyText   : '',
                                    fieldLabel  : 'Amount',
                                    labelWidth  : 100,
                                    flex        : 1,
                                    allowBlank  : false
                                }    
                            ]
                        }
                    ]
                }
            ]
         }
         );
                                                                      
         return  form;
    };

    //CC Payment
financeForms.form_cc_payment=function(Content)
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
            autowidth   : true,
            autoHeight  : true,
            
            layout      : {type: 'vbox',align: 'stretch'}, 
            border      : false,
                                    
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },      
            items: 
            [
                {              
                    xtype   : 'component',
                    html    :Content
                }
            ]
         }
         );
                                                                      
         return  form;
    };
    //embeded fetch form
financeForms.form_intrac_payment=function(_html)
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
            autowidth   : true,
            autoHeight  : true,
            
            layout      : {type: 'vbox',align: 'stretch'},
            
            border      : false,
                                    
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },      
            items: 
            [
                {
                    xtype: 'component',
                    style: 'margin-top:2px;margin-bottom:2px;',
                    html: _html
                }
            ]
         }
         );
                                                                      
         return  form;
    };
    //form charge Items management
financeForms.form_chargeitems=function()
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                autowidth   : true,
                autoHeight  : true,
                
                layout      : {type: 'vbox',align: 'stretch'},

                border      : false,
                                        
                fieldDefaults: {
                    labelAlign: 'top',
                    labelWidth: 100,
                    labelStyle: 'font-weight:bold'
                },      
                items: 
                [
                    {
                        xtype       : 'textfield',
                        labelStyle  : 'font-weight:bold;padding:0',
                        name        : 'charge_code',
                        emptyText   : '',
                        fieldLabel  : 'Code',
                        labelWidth  : 100,
                        allowBlank  : false
                    } 
                    ,{              
                        xtype       : 'textarea',
                        labelStyle  : 'font-weight:bold;padding:0',
                        name        : 'charge_descr',
                        emptyText   : '',
                        fieldLabel  : 'description',
                        labelWidth  : 100,
                        llowBlank  : false
                    }
                ]
         }
         );
                                                                      
         return  form;
    };
    //form Description and due date
financeForms.form_description=function()
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
               
            autowidth   : true,
            autoHeight  : true,
            
            layout      : {type: 'vbox',align: 'stretch'},
             
            border      : false,
                                    
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },      
            items: 
            [
                {
                            xtype           : 'fieldcontainer',
                            fieldLabel      : 'Due Date',
                            combineErrors   : true,
                            msgTarget       : 'side',          
                            labelStyle      : 'font-weight:bold;padding:0',
                            layout          : 'hbox',        
                            fieldDefaults   : {labelAlign: 'top'},
                            items           :
                            [
                                {
                                    id      : 'nettoday_button',
                                    xtype   : 'button',
                                    text    : 'Today',
                                    tooltip : '',
                                    width   : 50,
                                    handler : function()
                                    {
                                         var today              = new Date();                                     
                                         Ext.getCmp("duedate_calendar").setValue(today);
                                         
                                         var pressedClass="x-btn-default-toolbar-small-pressed";
                                         Ext.getCmp("nettoday_button").addClass(pressedClass);
                                         Ext.getCmp("net15_button").removeCls(pressedClass);
                                         Ext.getCmp("net30_button").removeCls(pressedClass);
                                         Ext.getCmp("net45_button").removeCls(pressedClass);
                                         Ext.getCmp("netcustom_button").removeCls(pressedClass);
                                    }
                                },
                                {
                                    id      : 'net15_button',
                                    xtype   : 'button',
                                    text    : 'NET 15',
                                    tooltip : '',
                                    width   : 50,
                                    handler : function()
                                    {
                                         var today              = new Date();                                     
                                         var tenDaysLaterDate   = new Date(today.setDate(today.getDate()+15));
                                         Ext.getCmp("duedate_calendar").setValue(tenDaysLaterDate);    
                                         
                                         var pressedClass="x-btn-default-toolbar-small-pressed";
                                         Ext.getCmp("net15_button").addClass(pressedClass);
                                         Ext.getCmp("net30_button").removeCls(pressedClass);
                                         Ext.getCmp("net45_button").removeCls(pressedClass);
                                         Ext.getCmp("nettoday_button").removeCls(pressedClass);
                                         Ext.getCmp("netcustom_button").removeCls(pressedClass);
                                    }
                                },
                                {
                                    id      : 'net30_button',
                                    xtype   : 'button',
                                    text    : 'NET 30',
                                    tooltip : '',
                                    width   : 50,
                                    handler : function()
                                    {
                                        var today                   = new Date();                                     
                                        var fifteenDaysLaterDate    = new Date(today.setDate(today.getDate()+30));
                                        Ext.getCmp("duedate_calendar").setValue(fifteenDaysLaterDate);
                                        
                                        var pressedClass="x-btn-default-toolbar-small-pressed";
                                        Ext.getCmp("net30_button").addClass(pressedClass);
                                        Ext.getCmp("net15_button").removeCls(pressedClass);
                                        Ext.getCmp("net45_button").removeCls(pressedClass);
                                        Ext.getCmp("nettoday_button").removeCls(pressedClass);
                                        Ext.getCmp("netcustom_button").removeCls(pressedClass);
                                    }
                                },
                                {
                                    id      : 'net45_button',
                                    xtype   : 'button',
                                    text    : 'NET 45',
                                    tooltip : '',
                                    width   : 50,
                                    handler : function()
                                    {
                                        var today                   = new Date();                                     
                                        var thirtyDaysLaterDate     = new Date(today.setDate(today.getDate()+45));
                                        Ext.getCmp("duedate_calendar").setValue(thirtyDaysLaterDate);
                                        
                                        var pressedClass="x-btn-default-toolbar-small-pressed";
                                        Ext.getCmp("net45_button").addClass(pressedClass);
                                        Ext.getCmp("net30_button").removeCls(pressedClass);
                                        Ext.getCmp("net15_button").removeCls(pressedClass);
                                        Ext.getCmp("nettoday_button").removeCls(pressedClass);
                                    }
                                },
                                {
                                    id      : 'netcustom_button',
                                    xtype   : 'button',
                                    text    : 'Custom',
                                    tooltip : '',
                                    width   : 50,
                                    handler : function()
                                    {
                                        
                                    }
                                }
                                ,{   
                                    id          : 'duedate_calendar',           
                                    xtype       : 'datefield',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'due_date',
                                    fieldLabel  : '',
                                    labelWidth  : 70,          
                                    flex        : 1,
                                    allowBlank  : false,
                                    editable    : false, 
                                    margins     : '0 0 0 10',
                                    listeners   :
                                    {
                                        change:function()
                                        {
                                            var pressedClass="x-btn-default-toolbar-small-pressed";
                                            var value=Ext.getCmp("duedate_calendar").getValue();
                                            
                                            var dateDiff    =me.compareDate(new Date(value));
                                            
                                            switch(dateDiff)
                                            {
                                                case 0:
                                                    Ext.getCmp("nettoday_button").addClass(pressedClass);
                                                    Ext.getCmp("net15_button").removeCls(pressedClass);
                                                    Ext.getCmp("net30_button").removeCls(pressedClass);
                                                    Ext.getCmp("net45_button").removeCls(pressedClass);
                                                    Ext.getCmp("netcustom_button").removeCls(pressedClass);    
                                                    break;
                                                case 15:
                                                    Ext.getCmp("net15_button").addClass(pressedClass);
                                                    Ext.getCmp("nettoday_button").removeCls(pressedClass);
                                                    Ext.getCmp("net30_button").removeCls(pressedClass);
                                                    Ext.getCmp("net45_button").removeCls(pressedClass);
                                                    Ext.getCmp("netcustom_button").removeCls(pressedClass);    
                                                  break;
                                                case 30:
                                                    Ext.getCmp("net30_button").addClass(pressedClass);
                                                    Ext.getCmp("nettoday_button").removeCls(pressedClass);
                                                    Ext.getCmp("net15_button").removeCls(pressedClass);
                                                    Ext.getCmp("net45_button").removeCls(pressedClass);
                                                    Ext.getCmp("netcustom_button").removeCls(pressedClass);      
                                                  break;
                                                case 45:
                                                    Ext.getCmp("net45_button").addClass(pressedClass);
                                                    Ext.getCmp("nettoday_button").removeCls(pressedClass);
                                                    Ext.getCmp("net30_button").removeCls(pressedClass);
                                                    Ext.getCmp("net15_button").removeCls(pressedClass);
                                                    Ext.getCmp("netcustom_button").removeCls(pressedClass);      
                                                  break;
                                                  
                                                default:
                                                    Ext.getCmp("netcustom_button").addClass(pressedClass);
                                                    Ext.getCmp("net15_button").removeCls(pressedClass);
                                                    Ext.getCmp("net30_button").removeCls(pressedClass);
                                                    Ext.getCmp("net45_button").removeCls(pressedClass);
                                                    Ext.getCmp("nettoday_button").removeCls(pressedClass);    
                                            }
                                        }
                                    }
                                }
                            ]
                        }
                ,{
                                xtype       : 'textarea',
                                labelStyle  : 'font-weight:bold;padding:0',
                                name        : 'description',
                                emptyText   : 'Description',
                                fieldLabel  : 'Description',
                                labelWidth  : 100,
                                height      : 220,
                                flex        : 1,
                                allowBlank  : true
                        }
            ]  
         }
         );
                                                                      
         return  form;
    };
financeForms.compareDate=function(_Date)
    {
        var today       = new Date();                                     
        today           = new Date(today.getFullYear(),today.getMonth(),today.getDate());
        var otherDate   = new Date(_Date);
        
        var diff=otherDate-today;
        return (((diff / 1000) / 60) / 60) / 24;
    };
    //form cancel_delete invoice
financeForms.form_cancel_delete_invoice=function(handler,htmlDescription)
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
               
            autowidth   : true,
            autoHeight  : true,
            
            layout      : {type: 'vbox',align: 'stretch'},
            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
            border      : false,
                                    
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },      
            items: 
            [
                {
                    xtype: 'component',
                    style: 'margin-top:2px;margin-bottom:2px;',
                    html: htmlDescription
                }
            ]
           
               
         }
         );
                                                                      
         return  form;
    };
    //form Release deposit to Invoice
financeForms.form_release_deposit_invoice=function(_unused_deposit_amount,currency_type_name)
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
               
                autowidth   : true,
                autoHeight  : true,
                
                layout      : {type: 'vbox',align: 'stretch'},
               // bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                border      : false,
                                        
                fieldDefaults: {
                    labelAlign: 'top',
                    labelWidth: 100,
                    labelStyle: 'font-weight:bold'
                },      
                items: 
                [
                    {
                        xtype: 'component',
                        style: 'margin-top:2px;margin-bottom:2px;',
                        html: '<span style="color:red;text-decoration:underline"><b>'
                        +currency_type_name+' '
                        +_unused_deposit_amount
                        +'</b></span> is now available to be used for invoice payment .'
                    }
                    
                ]    
         }
         );
                                                                      
         return  form;
    };

    //Form Invoice Item Info
financeForms.form_invoice_item_info=function(addressStore,emailStore,activeOrgLogo)
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 0px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                        {
                                            xtype           : 'fieldcontainer',
                                            margin          : '3 3 3 3',
                                            fieldLabel      : '',
                                            combineErrors   : true,
                                            msgTarget       : 'side',          
                                            labelStyle      : 'font-weight:bold;padding:0',
                                            layout          : 'hbox',        
                                            fieldDefaults   : {labelAlign: 'top'},
                                            items           :
                                            [
                                                {
                                                    xtype       : 'fieldset',
                                                    title       : '',
                                                    collapsible : false,
                                                    margin      : '3 3 3 3 ',
                                                    defaults    : 
                                                    {
                                                        labelWidth: 89,
                                                        anchor: '100%',
                                                        layout: {
                                                            type: 'hbox',
                                                            defaultMargins: {top: 1, right: 1, bottom: 1, left: 1}
                                                        }    
                                                    },
                                                    items:
                                                    [
                                                        //Visible If By Owner and 
                                                         {
                                                             id          : 'address_combo',
                                                             xtype       : 'combo',
                                                             name        : 'address_combo',
                                                             fieldLabel  : 'Address',
                                                             labelWidth  : 70,
                                                             width       : 200,
                                                             allowBlank  : true,
                                                             mode        : 'local',
                                                             forceSelection: true,
                                                             editable    : false,
                                                             displayField: 'address_name',
                                                             valueField  : 'address_name',
                                                             queryMode   : 'local',
                                                             labelStyle  : 'font-weight:bold',
                                                             store       : addressStore,
                                                             listeners  :
                                                             {
                                                                     buffer:200,
                                                                     change:function(obj,selected_id)
                                                                     { 
                                                                         var selectedAddress    =Ext.getCmp("address_combo").getValue();
                                                                         var selectedEmail      =Ext.getCmp("email_combo").getValue();
                                                                         
                                                                         var rightPanelInfo     =
                                                                         '<table style="width:200;font-family:verdana;font-size:9px">'
                                                                         +'<tr>'
                                                                         +'<td style="vertical-align:top;text-align:left;">'
                                                                         +'<img width="200px" height="60px" src="'+activeOrgLogo+'" />'
                                                                         +'<br/>'
                                                                         +'<b>From</b>:'
                                                                         +'<br/>'
                                                                         +((selectedAddress!=null)?selectedAddress:'')
                                                                         +'<br/>'
                                                                         +((selectedEmail!=null)?selectedEmail:'')
                                                                         +'</td>'
                                                                         +'</tr>'
                                                                         +'</table>'
                                                                         ;
                                                                         Ext.getCmp("invoice_item_info_panel").setValue(rightPanelInfo);  
                                                                     }
                                                             }                                          
                                                        }    
                                                        ,{
                                                             id          : 'email_combo',
                                                             xtype       : 'combo',
                                                             name        : 'email_combo',
                                                             fieldLabel  : 'Emails',
                                                             labelWidth  : 70,
                                                             width       : 200,
                                                             allowBlank  : true,
                                                             mode        : 'local',
                                                             forceSelection: true,
                                                             editable    : false,
                                                             displayField: 'email_name',
                                                             valueField  : 'email_name',
                                                             queryMode   : 'local',
                                                             labelStyle  : 'font-weight:bold',
                                                             store       : emailStore,
                                                             listeners  :
                                                             {
                                                                     buffer:200,
                                                                     change:function(obj,selected_id)
                                                                     {   
                                                                         var selectedAddress    =Ext.getCmp("address_combo").getValue();
                                                                         var selectedEmail      =Ext.getCmp("email_combo").getValue();
                                                                         
                                                                         var rightPanelInfo     =
                                                                         '<table style="width:200;font-family:verdana;font-size:9px">'
                                                                         +'<tr>'
                                                                         +'<td style="vertical-align:top;text-align:left;">'
                                                                         +'<img width="200px" height="60px" src="'+activeOrgLogo+'" />'
                                                                         +'<br/>'
                                                                         +'<b>From</b>:'
                                                                         +'<br/>'
                                                                         +((selectedAddress!=null)?selectedAddress:'')
                                                                         +'<br/>'
                                                                         +((selectedEmail!=null)?selectedEmail:'')
                                                                         +'</td>'
                                                                         +'</tr>'
                                                                         +'</table>'
                                                                         ;
                                                                         Ext.getCmp("invoice_item_info_panel").setValue(rightPanelInfo);  
                                                                     }
                                                             }                                          
                                                        }
                                                        //Visible IF By Receiver
                                                        ,{   
                                                            id          : 'field_receiver_Info',
                                                            xtype       : 'displayfield',
                                                            margin      : '5 5 5 5',
                                                            bodyStyle   : 'vertical-align:top',
                                                            height      : 80,
                                                            width       : 200
                                                            
                                                        }
                                                    ]
                                                }    
                                                ,{   
                                                    id          : 'invoice_item_info_panel',
                                                    xtype       : 'displayfield',
                                                    margin      : '5 5 5 5',
                                                    bodyStyle   : 'vertical-align:top',
                                                    height      : 80,
                                                    width       : 200
                                                }
                                            ]
                                        }
                            ]
                               
         }
         );                                                           
         return  form;
    };
    //Form Invoice Item Description
financeForms.form_invoice_item_description=function()
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
               
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {
                                        id          : 'comment',
                                        name        : 'comment',
                                        xtype       : 'textarea',
                                        labelStyle  : 'font-weight:bold;padding:0',
                                        emptyText   : '',
                                        fieldLabel  : 'Comment',
                                        labelWidth  : 100,
                                        flex        : 1,
                                        allowBlank  : true
                                }
                            ]
         }
         );
                                                                      
         return  form;
    };
    
    //Form Cancellation Test Case
financeForms.formCancellationTestCase=function(paymentListStore)
    {  
         var me=this;
         var form=new Ext.form.Panel(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,                             
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {
                                     xtype       : 'combo',
                                     name        : 'payment',
                                     id          : 'payment',
                                     fieldLabel  : 'Payments',
                                     labelWidth  : 70,
                                     width       : 200,
                                     allowBlank  : true,
                                     mode        : 'local',
                                     forceSelection: true,
                                     editable    : false,
                                     displayField: 'description',
                                     valueField  : 'details',
                                     queryMode   : 'local',
                                     labelStyle  : 'font-weight:bold',
                                     store       : paymentListStore             
                                }    
                            ]                        
         }
         );                                                           
         return  form;
    }
    
    //Form Invoice Item Description
financeForms.form_invoice_item_taxes=function(applyTaxComponent)
    {  
         var me=this;
         var form=new Ext.spectrumforms(
         {   
               
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [   
                                applyTaxComponent,
                                {
                                    id      : 'invoice_item_tax_panel',
                                    xtype   : 'displayfield'
                                }
                            ]
         }
         );
                                                                      
         return  form;
    };


