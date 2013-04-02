Ext.define('Ext.spectrumgrids.invoice',    
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
        
        ,a_o_id                 :null
        ,a_o_name               :null
        ,a_o_t_id               :null
        
        ,a_o_tax_info           :null
        ,unused_deposits        :null
        ,applied_tax            :null
        
        
        ,constructor : function(config) 
        {   
            var me=this;                             
            config.columns  =
            [    
                {
                    text        : "Title",
                    dataIndex   : "invoice_title",
                    width       : 150
                },
                {
                    text        : "From"
                    ,dataIndex  : "invoice_master_ename"
                    ,width      : 150
                }
                ,{
                    text        : "To"
                    ,dataIndex  : "invoice_slave_ename"
                    ,width      : 150
                }
                ,{
                    text        : "Amount"
                    ,xtype       :'templatecolumn'
                    ,tpl         :'<div style="text-align:center;font-weight:bold;color:black;">{invoice_amount}</div>'
                    ,width      : 70
                }
                ,{
                    text        : "Paid"
                    ,xtype       :'templatecolumn'
                    ,tpl         :'<div style="text-align:center;font-weight:bold;color:black;">{invoice_paid}</div>'
                    ,width      : 70
                }
                ,{
                    text        : "Owing"
                    ,xtype       :'templatecolumn'
                    ,tpl         :'<div style="text-align:center;font-weight:bold;color:black;">{invoice_owing}</div>'
                    ,width      : 70
                }
                ,{
                    text        : "Currency"
                    ,dataIndex  : "currency_type_name"
                    ,width      : 70
                }
                ,{
                    text        : "Number"
                    ,dataIndex  : "invoice_number"
                    ,width      : 70
                }
                ,{
                    text        : "Custom Number"
                    ,dataIndex  : "custom_invoice_number"
                    ,width      : 100
                }
                ,{
                    text        : "Issue Date"
                    ,dataIndex  : "date_issued"
                    ,width      : 70
                }
                ,{
                    text        : "Due Date"
                    ,dataIndex  : "date_due"
                    ,width      : 70
                }
                ,{
                    text        : "Created By"
                    ,dataIndex  : "created_by_name"
                    ,width      : 100
                }
                ,{
                    text        : "Created On"
                    ,dataIndex  : "created_on_display"
                    ,flex       : 1
                }
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            var currencyTypeStore           =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_currencytypes/"+App.TOKEN+'/',{test:true});
            var invoiceTypeStore            =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_invoice_types/"+App.TOKEN+'/',{test:true});
            var invoiceDateTypeListStore    =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_invoice_date_type_list/"+App.TOKEN+'/',{invoiceType:"invoice"});
               
            var PressedClass     ="";//"x-btn-default-toolbar-small-pressed btn_active";
            var unPressedClass   ="";//"x-btn-default-toolbar-small-pressed btn_inactive";
        
            config.topItems.push
            (
                {
                     id          : 'invoice_invoice_type_combo'+config.generator,
                     xtype       : 'combo',
                     name        : 'invoice_type_id',
                     fieldLabel  : '',
                     labelWidth  : 70,
                     width       : 100,
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: true,
                     editable    : false,
                     displayField: 'type_name',
                     valueField  : 'type_id',
                     queryMode   : 'local',
                     labelStyle  : 'font-weight:bold',
                     store       : invoiceTypeStore,
                     listeners  :
                     {
                             buffer:200,
                             change:function(obj,selected_id)
                             {
                                 var invoice_type_id    =selected_id;
                                 var fromDate           =(Ext.getCmp("spectrumgrids_invoice_from_date").getValue()!=null  ?new Date(Ext.getCmp("spectrumgrids_invoice_from_date").getValue()):'');
                                 var toDate             =(Ext.getCmp("spectrumgrids_invoice_to_date").getValue()!=null    ?new Date(Ext.getCmp("spectrumgrids_invoice_to_date").getValue()):'');
                                 var dateTypeList       =Ext.getCmp("spectrumgrids_invoice_date_type_list_combo").getValue();
                                 
                                 me.getStore().proxy.extraParams.invoice_type_id    =invoice_type_id;
                                 me.getStore().proxy.extraParams.fromDate           =((fromDate=='')?'':fromDate.getFullYear() +'-'+ (parseInt(fromDate.getMonth())+1) +'-'+ fromDate.getDate());
                                 me.getStore().proxy.extraParams.toDate             =((toDate=='')?'':toDate.getFullYear() +'-'+ (parseInt(toDate.getMonth())+1) +'-'+ toDate.getDate());
                                 me.getStore().proxy.extraParams.dateTypeList       =dateTypeList;
                                 me.getStore().load();
                                 me.handleButtons();
                             }
                     }                                          
                },
                '-',
                //Filtering By from/to Date and Type
                {   
                    id          : 'spectrumgrids_invoice_from_date',           
                    name        : 'spectrumgrids_invoice_from_date',
                    emptyText   : 'From Date',
                    xtype       : 'datefield',
 

                    labelWidth  : 0,          
                    width       : 150, 
                    allowBlank  : true,
                    editable    : false, 
                    margins     : '0 0 0 100'
                },
                {   
                    id          : 'spectrumgrids_invoice_to_date',           
                    name        : 'spectrumgrids_invoice_to_date',
                    xtype       : 'datefield',
                    emptyText   : 'To date',
 

                    labelWidth  : 0,          
                    width       : 150, 
                    allowBlank  : true,
                    editable    : false, 
                    margins     : '0 0 0 5'
                },
                {
                     id          : 'spectrumgrids_invoice_date_type_list_combo',
                     xtype       : 'combo',
                     emptyText   : 'Date Type',
                     name        : 'date_list_type_id',
                     fieldLabel  : '',
                     labelWidth  : 70,
                     width       : 100,
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: true,
                     editable    : false,
                     displayField: 'type_name',
                     valueField  : 'type_id',
                     queryMode   : 'local', 
                     store       : invoiceDateTypeListStore,
                     margins     : '0 0 0 5'                                          
                },
                {
                     iconCls : 'fugue_magnifier--plus',
                     xtype   : 'button',
                     pressed : true,
                     tooltip : 'Apply Filter',
                     handler : function()
                     {                                                                                                 
                        var invoice_type_id=((Ext.getCmp('invoice_invoice_type_combo'+config.generator)).isDisabled()
                            ?6:Ext.getCmp("invoice_invoice_type_combo"+me.config.generator).getValue());
                        
                        var fromDate            =(Ext.getCmp("spectrumgrids_invoice_from_date").getValue()!=null  ?new Date(Ext.getCmp("spectrumgrids_invoice_from_date").getValue()):'');
                        var toDate              =(Ext.getCmp("spectrumgrids_invoice_to_date").getValue()!=null    ?new Date(Ext.getCmp("spectrumgrids_invoice_to_date").getValue()):'');
                        var dateTypeList        =Ext.getCmp("spectrumgrids_invoice_date_type_list_combo").getValue();
                                                                               
                        if(fromDate=='' || toDate=='' || dateTypeList=='')
                        {
                            Ext.MessageBox.alert({title:"Error",msg:"Please Fill in all three Filtering Items", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }                                                                                    
                        
                        me.getStore().proxy.extraParams.invoice_type_id    =invoice_type_id;
                        me.getStore().proxy.extraParams.fromDate           =fromDate;
                        me.getStore().proxy.extraParams.toDate             =toDate;
                        me.getStore().proxy.extraParams.dateTypeList       =dateTypeList;
                        
                        me.getStore().load();
                        
                        me.handleButtons();
                     }
                },
                {
                     iconCls : 'fugue_magnifier--minus',
                     xtype   : 'button',
                     pressed : true,
                     tooltip : 'Remove Filter',
                     handler : function()
                     {
                        //Activate Invoices Button
                        var pressedClass    ="";//"x-btn-default-toolbar-small-pressed";
                        var unPressedClass  ="";//"x-btn-default-small x-noicon x-btn-noicon x-btn-default-small-noicon";
                        Ext.getCmp("filter_invoice_btn").addClass(pressedClass);  
                        Ext.getCmp("filter_invoice_btn").removeCls(unPressedClass);  
                        Ext.getCmp("filter_invoice_draft_btn").removeCls(pressedClass);     
                        Ext.getCmp("filter_invoice_draft_btn").addClass(unPressedClass); 
                        //Activate Invoices Button
                            
                        Ext.getCmp("spectrumgrids_invoice_from_date").setValue('');
                        Ext.getCmp("spectrumgrids_invoice_to_date").setValue('');
                        Ext.getCmp("spectrumgrids_invoice_date_type_list_combo").setValue('');
                                                                               
                        var invoice_type_id    =Ext.getCmp('invoice_invoice_type_combo'+config.generator).getValue();
                        var fromDate           =(Ext.getCmp("spectrumgrids_invoice_from_date").getValue()!=null ?new Date(Ext.getCmp("spectrumgrids_invoice_from_date").getValue()):'');
                        var toDate             =(Ext.getCmp("spectrumgrids_invoice_to_date").getValue()!=null   ?new Date(Ext.getCmp("spectrumgrids_invoice_to_date").getValue()):'');
                        var dateTypeList       =Ext.getCmp("spectrumgrids_invoice_date_type_list_combo").getValue();
                                 
                        me.getStore().proxy.extraParams.invoice_type_id    =invoice_type_id;
                        me.getStore().proxy.extraParams.fromDate           =fromDate;
                        me.getStore().proxy.extraParams.toDate             =toDate;
                        me.getStore().proxy.extraParams.dateTypeList       =dateTypeList;
                                               
                        me.getStore().load();
                        me.handleButtons();
                     }
                }
            ); 
            config.bottomLItems.push
            (
                //New Template Invoice                               
                {
                        id      : 'spectrumgrids_draft_invoice_new_btn',
                        iconCls : 'fugue_blue-document--plus',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Create New Invoice Template',
                        handler : function()
                        {
                            var _config=
                            {
                                width   : 300,
                                height  : 300,
                                bottomItems:   
                                [
                                    '->'
                                    ,{   
                                         xtype   :"button",
                                         text    :"Save",
                                         iconCls :'disk',
                         
                                         tooltip :'Save',
                                         handler :function()
                                         {                     
                                             if (form.getForm().isValid()) 
                                             {   
                                                 var post={}
                                                 post=form.getForm().getValues();
                                                 form.getForm().submit({
                                                     url     : 'index.php/finance/json_generate_invoice_draft/TOKEN:'+App.TOKEN,
                                                     waitMsg : 'Processing ...',
                                                     params  : post,
                                                     success : function(form, action)
                                                     {
                                                         var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                         //Here res.result ~ Template Invoice_id If Successfull
                                                         if(res.result!="-1")
                                                         {
                                                             win.Hide();
                                                             me.getStore().load();
                                                            
                                                             //Fire Invoice Template button to load Template Grid
                                                             var invoiceDraftButton=Ext.getCmp("filter_invoice_draft_btn");
                                                             invoiceDraftButton.fireEvent("click",invoiceDraftButton);
                                                             
                                                             //After Draft Created 1-load 2-select newly-generated record 3-open addItem Window
                                                             var newDraftInvoiceId = res.result;
                                                             me.getStore().on("load",function()
                                                             {        
                                                                //var recIndex            =me.getStore().find( "invoice_draft_id", newDraftInvoiceId, 0, true, false, false);    
                                                                //me.getSelectionModel().select( recIndex, true, false); 
                                                                //me.view_invoice_items()
                                                             });                       
                                                         }           
                                                     },
                                                     failure: function(form, action){alert(action.response.responseText);}
                                                 }); 
                                             
                                             }   
                                         }                          
                                    }
                                ]
                            }          
                            var form        =financeForms.form_invoice(currencyTypeStore);
                            var final_form  =new Ext.spectrumforms.mixer(_config,[form],['form']);
                            var win_cnf     =
                            {
                                title       : 'Create New Template',
                                final_form  : final_form
                            }
                            var win=new Ext.spectrumwindow.finance(win_cnf);
                            win.show();
                        }
                } 
            );       
            config.bottomRItems.push
            (
                //Payment [internal/Debit/Credit]
                {
                        id      : 'spectrumgrids_invoice_pay_btn',
                        iconCls : 'money',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Payment',
                        handler : function()
                        {
                             var post={test:"test"}
                             Ext.Ajax.request(
                             {
                                 url     : 'index.php/finance/json_getActiveUserOrgEntity/TOKEN:'+App.TOKEN,
                                 params  : post,
                                 success : function(response)
                                 {
                                      var res=YAHOO.lang.JSON.parse(response.responseText);
                                      if(parseInt(res.result)==-1)
                                      {
                                          Ext.MessageBox.alert({title:"Error",msg:"You are logged out", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                          return
                                      }
                                      else
                                      {
                                          
                                      }
                                      var post={test:"test"}
                                      Ext.Ajax.request(
                                      {
                                         url     : 'index.php/finance/json_Dev_or_Live/TOKEN:'+App.TOKEN,                                 
                                         params  : post,
                                         success : function(response)
                                         {
                                              var DevOrLive=YAHOO.lang.JSON.parse(response.responseText);
                                              
                                              me.pay(res.result,DevOrLive.result);
                                         }
                                     });    
                                     
                                 }
                            });    
                        }
                }   
                //Approve & Resend Invoice
                ,{
                        id      : 'spectrumgrids_invoice_resend_btn',
                        icon    : 'http://endeavor.servilliansolutionsinc.com/global_assets/fugue/arrow-circle-135.png',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Approve and Resend Invoice',
                        handler : function()
                        {
                             if(me.getSelectionModel().getSelection().length==0)
                             {
                                Ext.MessageBox.alert({title:"Error",msg:"Please select an Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                             }
                             var record=me.getSelectionModel().getSelection()[0].data;
                             
                             var post={}
                             post["invoice_status_id"]      ="4";//ready
                             post["invoice_id"]             =record.invoice_id;
                             
                             var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                             Ext.Ajax.request(
                             {
                                 url     : 'index.php/finance/json_change_invoice_status/TOKEN:'+App.TOKEN,
                                 params  : post,
                                 success : function(response)
                                 {
                                      box.hide();  
                                      var res=YAHOO.lang.JSON.parse(response.responseText);
                                      if(res.result=="1")
                                      {                           
                                          Ext.MessageBox.alert({title:"Status",msg:"Invoice is now approved and received by payer", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                          me.getStore().load();                                                                  
                                          me.handleButtons();
                                      }
                                      App.updatePanel();
                                 }
                             });
                        }
                    }
                //Release Deposit to Invoice
                ,{
                        id      : 'spectrumgrids_invoice_releaseDeposit_btn',
                        icon    : 'http://endeavor.servilliansolutionsinc.com/global_assets/fugue/bank--arrow.png',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Use Deposit to pay Invoice',
                        handler : function()
                        {
                            me.release_deposit_to_invoice();
                        }
                    }
                //Cancel Invoice
                ,{
                        id      : 'spectrumgrids_invoice_cancel_btn',
                        icon    : 'http://endeavor.servilliansolutionsinc.com/global_assets/fugue/cross-shield.png',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Cancel Invoice',
                        handler : function()
                        {
                             if(me.getSelectionModel().getSelection().length==0)
                             {
                                Ext.MessageBox.alert({title:"Error",msg:"Please select an Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                             }
                             var record=me.getSelectionModel().getSelection()[0].data;
                             if(record.invoice_status_id==3 || record.invoice_status_id==5)
                             {
                                 Ext.MessageBox.alert({title:"Error",msg:"Invoice is Already Cancelled or in Pending Status", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }
                             //Cancel
                             if((record.invoice_status_id==2 || record.invoice_status_id==3 || record.invoice_status_id==4) && me.a_o_id==record.invoice_master_oid );
                             else 
                             {
                                Ext.MessageBox.alert({title:"Error",msg:"Invoice Cancellation Action is not Permitted at this Stage", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return; 
                             }
                             
                             //FIRST CHECK WHETHER OR NOT THIS INVOICE CAN BE CANCELLED OR NOT
                             var post={}
                             post["invoice_id"]=record.invoice_id;
                             Ext.Ajax.request(
                             {
                                 url     : 'index.php/finance/json_invoicePaymentsCancellationPossibility/TOKEN:'+App.TOKEN, 
                                 params  : post,
                                 success : function(response)
                                 {
                                    var res=YAHOO.lang.JSON.parse(response.responseText);
                                    if(res.result=="true")
                                        me.cancelInvoice(record); 
                                    else
                                        Ext.MessageBox.alert({title:"Error",msg:"No Enough Wallet Balance to Complete Cancellation[1]", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});       
                                 }
                             });
                        }
                    } 
                //Delete Invoice
                ,{
                        id      : 'spectrumgrids_invoice_delete_btn',
                        icon    : 'http://endeavor.servilliansolutionsinc.com/global_assets/fugue/cross-script.png',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Delete Invoice', 
                        handler : function()
                        {
                            
                             if(me.getSelectionModel().getSelection().length==0)
                             {
                                Ext.MessageBox.alert({title:"Error",msg:"Please select an Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                             }
                             var record=me.getSelectionModel().getSelection()[0].data;
                             
                             /*if(record.invoice_status_id==4 && me.a_o_id==record.invoice_master_oid && record.invoice_paid==0);
                             else
                             {
                                 Ext.MessageBox.alert({title:"Error",msg:"Invoice Illumination Action is not Permitted at this Stage", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }*/    
                             
                             Ext.MessageBox.confirm('Invoice Illumination Action', "Are you sure ?", function(answer)
                             {     
                                    if(answer=="yes")
                                    {                                                                                
                                         var post={}
                                         post["invoice_id"]=record.invoice_id;
                                            
                                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                         Ext.Ajax.request(
                                         {
                                             url     : 'index.php/finance/json_delete_invoice/TOKEN:'+App.TOKEN, 
                                             params  : post,
                                             success : function(response)
                                             {
                                                  var res=YAHOO.lang.JSON.parse(response.responseText);
                                                  
                                                  if(parseInt(res.result)==1)
                                                  {     
                                                        box.hide();
                                                        App.updatePanel();
                                                                              
                                                        Ext.MessageBox.alert({title:"Status",msg:"Invoice Got Deleted Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                        me.getStore().load();
                                                        me.handleButtons();
                                                  }
                                                  if(parseInt(res.result)==-1)
                                                  {     
                                                      box.hide();
                                                      Ext.MessageBox.alert({title:"Error",msg:"There is At lease one none-cancelled invoice payment", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                      me.getStore().load();   
                                                      me.handleButtons();
                                                  }
                                                  
                                             }
                                         });
                                    } 
                             });
                             
                        }
                    } 
                //View Invoice/Template Items List
                ,{
                        id      : 'spectrumgrids_invoice_viewitems_btn',
                        icon    : 'http://endeavor.servilliansolutionsinc.com/global_assets/fugue/inbox-image.png',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Invoice Items',
                        handler : function()
                        {
                            me.view_invoice_items();
                        }
                }
                //Template-Related Buttons*************************************************************
                //Edit Template Invoice                               
                ,{
                        id      : 'spectrumgrids_draft_invoice_edit_btn',
                        iconCls : 'fugue_blue-document--pencil',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Modify Invoice Template',
                        handler : function()
                        {
                            if(me.getSelectionModel().getSelection().length==0)
                            {
                                Ext.MessageBox.alert({title:"Error",msg:"Please select a Template Record to Edit", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }                                                       
                            var record=me.getSelectionModel().getSelection()[0].data;
                            if(record.invoice_status_id!=1)
                            {
                                Ext.MessageBox.alert({title:"Error",msg:"Selected Record is not an Invoice Template", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;                                
                            }
                            var _config=
                            {
                                width   : 300,
                                height  : 300,
                                bottomItems:   
                                [
                                    '->'
                                    ,{   
                                         xtype   :"button",
                                         text    :"Update",
                                         iconCls :'table_save',
                                         pressed :true,
                                         tooltip :'Update',
                                         handler :function()
                                         {       
                                                       
                                             if (form.getForm().isValid()) 
                                             {   
                                                 var post           ={}
                                                 post               =form.getForm().getValues();
                                                 post["invoice_id"] =record.invoice_id;
                                                 
                                                 var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                 Ext.Ajax.request(
                                                 {
                                                     url     : 'index.php/finance/json_update_invoice_draft/TOKEN:'+App.TOKEN,
                                                     params  : post,
                                                     success : function(response)
                                                     {
                                                          box.hide();  
                                                          var res=YAHOO.lang.JSON.parse(response.responseText);
                                                          if(res.result=="1")
                                                          {  
                                                              win.Hide();
                                                              Ext.MessageBox.alert({title:"Status",msg:"Invoice Template Named '<b>"+record.invoice_title+"'</b> Updated Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                              me.getStore().load();                           
                                                              
                                                              me.handleButtons();
                                                          }
                                                          App.updatePanel();
                                                     }
                                                 });            
                                             }   
                                         }                          
                                    }
                                ]
                            }          
                            var form        =financeForms.form_invoice(currencyTypeStore);
                            var final_form  =new Ext.spectrumforms.mixer(_config,[form],['form']);
                            var win_cnf     =
                            {
                                title       : 'Edit Invoice Template',
                                final_form  : final_form
                            }
                            var win=new Ext.spectrumwindow.finance(win_cnf);
                            win.show();
                            
                            //load records
                            var Gen=Math.random();
                            Ext.define('model_'+Gen,{extend: 'Ext.data.Model'});
                            form.loadRecord(Ext.ModelManager.create(
                            {
                              'title'           : record.invoice_title,
                              'currency_type_id': record.currency_type_id,
                              'description'     : record.invoice_description
                            }, 'model_'+Gen));
                        }
                }      
                //Distribute Invoice Template to Recipients and Finalize the invoice
                ,{
                        id      : 'spectrumgrids_draft_invoice_recipient_btn',
                        iconCls : 'fugue_blue-document-share',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Distribute Invoices form Template',
                        handler : function()
                        {
                            me.send_to_recipients();
                        }   
                    }   
                //View & Edit invoice Items list
                ,{
                        id      : 'spectrumgrids_draft_invoice_viewitems_btn',
                        iconCls : 'fugue_blue-document-list',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Manage Invoice Items',
                       // hidden  : true,
                        handler : function()
                        {
                            me.view_invoice_items();
                        }
                    }   
                //Delete Template Invoice 
                ,{
                        id      : 'spectrumgrids_draft_invoice_delete_btn',
                        iconCls : 'fugue_minus-button',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Remove Invoice Template',
                        handler : function()
                        {
                             if(me.getSelectionModel().getSelection().length==0)
                             {
                               // Ext.MessageBox.alert({title:"Error",msg:"Please select an Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                             }
                             var record=me.getSelectionModel().getSelection()[0].data;
                                                                     
                             //Delete Darft Invoice
                             if(record.invoice_status_id==1 && me.a_o_id==record.invoice_master_oid );
                             else                                            
                             {
                             	 //NOT AN ERROR
                                 Ext.MessageBox.alert({title:"Cannot continue",msg:"Invoice Template Illumination is not Permitted at this point", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }          
                             Ext.MessageBox.confirm('Invoice Template Illumination Action', "Are you sure ?", function(answer)
                             {     
                                    if(answer=="yes")
                                    {
                                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                         var post={}
                                         post["invoice_id"]=record.invoice_draft_id;
                                         
                                         Ext.Ajax.request(
                                         {
                                             url     : 'index.php/finance/json_delete_draft_invoice/TOKEN:'+App.TOKEN, 
                                             params  : post,
                                             success : function(response)
                                             {
                                                  box.hide();
                                                  var res=YAHOO.lang.JSON.parse(response.responseText);
                                                  if(res.result=="1")
                                                  {                           
                                                        Ext.MessageBox.alert({title:"Status",msg:"Invoice Template Named <b>"+record.invoice_title+"</b> Removed Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                        me.getStore().load();
                                                        me.handleButtons();
                                                  }
                                                  if(res.result=="-1")
                                                  {   
                                                  	  //NOT AN ERROR
                                                        Ext.MessageBox.alert({title:"Not allowed",msg:"Cannot Delete Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                        me.getStore().load();   
                                                        me.handleButtons();
                                                       // Ext.getCmp("invoice_items_delete_void_btn").hide();
                                                  }
                                             }
                                             //failure case is a must have esp for box.hide
                                             ,failure:function(o){box.hide();App.error.xhr(o);}
                                         });
                                    } 
                             });
                        }
                    }
            ); 
              
            invoiceTypeStore.on("load",function()
            {
                 var rec=invoiceTypeStore.getAt(0);
                 Ext.getCmp("invoice_invoice_type_combo"+config.generator).select(rec);    
            });
            
            //RowExpanding plugin
            /*config.plugins=
            [
                {
                    ptype: 'rowexpander',
                    rowBodyTpl : [
                            //'<p><b>Company:</b> {firstname}</p><br>',
                            //'<p><b>Summary:</b> {lastname}</p>'    
                            '<p><b>Summary:</b></p>'    
                    ]
                }    
            ]*/
                    
            
            //RowExpanding plugin
                
            
            config.autoLoad             =false;
            if(config.fields==null)     config.fields       = ['invoice_id','invoice_draft_id','invoice_title','invoice_master_oid','invoice_master_eid','invoice_master_ename','invoice_slave_oid','invoice_slave_eid','invoice_slave_ename','invoice_number','custom_invoice_number','custome_invoice_number','invoice_description','invoice_comment','issuerinfo_address','issuerinfo_email','slaveinfo_address' ,'slaveinfo_email','master_logo','invoice_amount','invoice_paid','invoice_owing','invoice_status_id','invoice_status_name','currency_type_id','currency_type_name','created_by','created_by_name','date_issued','date_due','currency_is_system'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = "void";
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 500;
            if(config.groupField==null) config.groupField   = "invoice_status_name";
            
            this.override_edit          =config.override_edit;
            this.override_selectiochange=config.override_selectionchange;
            this.override_itemdblclick  =config.override_itemdblclick;
            this.override_collapse      =config.override_collapse;
            this.override_expand        =config.override_expand;
            
            this.config=config;
            this.callParent(arguments); 
           
            //Getting Tax and Organization Information
            Ext.Ajax.request(
            {
                url: 'index.php/permissions/json_get_active_org_and_type/'+App.TOKEN,
                params: {test:'test'},
                success: function(response)
                {
                    var res        =YAHOO.lang.JSON.parse(response.responseText);
                    me.a_o_t_id    =res.result.org_type_id;
                    me.a_o_id      =res.result.org_id;
                    me.a_o_name    =res.result.org_name;
                    Ext.Ajax.request(
                    {
                        url: 'index.php/finance/json_getEntityTaxes/'+App.TOKEN,
                        params: {test:'test'},
                        success: function(response)
                        {
                            var res=YAHOO.lang.JSON.parse(response.responseText);
                            if(res.result.length==0)                    
                            {
                                me.a_o_tax_info=null;
                                Ext.MessageBox.alert({title:"Status",msg:"No Address Setup Found for <b>"+me.a_o_name+"</b> & wont be able to Retrive <b>Tax</b> Information", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                            }
                            else  
                                me.a_o_tax_info   =res.result;
                        }
                        ,failure: function( action){App.error.xhr(action);}
                         
                    });
                },
				//always need a failure case. otherwise entire page is locked under the wait box forever
                 failure: function( action){App.error.xhr(action);}
            });
        }
        ,afterRender: function() 
        {  
            var me=this;
 
            if(!this.override_selectionchange)                              
            {   
                this.on("selectionchange",function(sm,records)
                {   
                    if(records ==undefined || records =='undefined' || records.length==0)return false;
                    var rec=records[0].raw;
                    me.handleButtons();
                    
                    var post={}
                    var URL='';
                    
                    if(rec.invoice_status_id==1)
                    {
                        post["invoice_id"]=rec.invoice_draft_id;
                        URL='index.php/finance/json_get_applied_tax_status_invoice_draft/TOKEN:'+App.TOKEN;
                    }                                                                                      
                    else 
                    {
                        post["invoice_id"]=rec.invoice_id;
                        URL='index.php/finance/json_get_applied_tax_status_invoice/TOKEN:'+App.TOKEN;
                    }
                     
                    Ext.Ajax.request(
                    {
                        url     : URL, 
                        params  : post,
                        success : function(response)
                        {
                             var res=YAHOO.lang.JSON.parse(response.responseText);
                             me.applied_tax=(res.result==1)?true:false;
                        }
                        ,failure: function( action){App.error.xhr(action);}
                    });    
                });
                this.on("itemclick",function(sm,records)
                {   
                    var rec=me.getSelectionModel().getSelection()[0].data;
                    me.handleButtons();    
                    
                    var post={}
                    var URL='';
                    
                    if(rec.invoice_status_id==1)
                    {
                        post["invoice_id"]=rec.invoice_draft_id;
                        URL='index.php/finance/json_get_applied_tax_status_invoice_draft/TOKEN:'+App.TOKEN;
                    }                                                                                      
                    else 
                    {
                        post["invoice_id"]=rec.invoice_id;
                        URL='index.php/finance/json_get_applied_tax_status_invoice/TOKEN:'+App.TOKEN;
                    }
                    Ext.Ajax.request(
                    {
                        url     : URL, 
                        params  : post,
                        success : function(response)                                            
                        {
                             var res=YAHOO.lang.JSON.parse(response.responseText);
                             me.applied_tax=(res.result==1)?true:false;
                        }
                        ,failure: function( action){App.error.xhr(action);}
                    });    
                }); 
            }
                         
            this.callParent(arguments);         
        }
        
        //Functions
        ,handleVisibility:function(buttonName,visibility)
        {
        	 
            //Invoice
            if(buttonName=='invoice_pay')
                Ext.getCmp("spectrumgrids_invoice_pay_btn").setVisible(visibility);
            if(buttonName=='invoice_items')
                Ext.getCmp("spectrumgrids_invoice_viewitems_btn").setVisible(visibility);
            if(buttonName=='invoice_resend')
                Ext.getCmp("spectrumgrids_invoice_resend_btn").setVisible(visibility);
            if(buttonName=='invoice_releasedeposit')
                Ext.getCmp("spectrumgrids_invoice_releaseDeposit_btn").setVisible(visibility);
            if(buttonName=='invoice_cancel')
                Ext.getCmp("spectrumgrids_invoice_cancel_btn").setVisible(visibility);
            if(buttonName=='invoice_delete')
                Ext.getCmp("spectrumgrids_invoice_delete_btn").setVisible(visibility);
                
            //Invoice Template
            if(buttonName=='draftinvoice_recipient')
                Ext.getCmp("spectrumgrids_draft_invoice_recipient_btn").setVisible(visibility);
            if(buttonName=='draftinvoice_items')
                Ext.getCmp("spectrumgrids_draft_invoice_viewitems_btn").setVisible(visibility);
            if(buttonName=='draftinvoice_delete')
                Ext.getCmp("spectrumgrids_draft_invoice_delete_btn").setVisible(visibility);
            if(buttonName=='draftinvoice_new')
                Ext.getCmp("spectrumgrids_draft_invoice_new_btn").setVisible(visibility);
            if(buttonName=='draftinvoice_edit')
                Ext.getCmp("spectrumgrids_draft_invoice_edit_btn").setVisible(visibility);
        }
        ,resetButtons:function()
        {
        	 
            //Invoices
            Ext.getCmp("spectrumgrids_invoice_pay_btn").setVisible(false);
            Ext.getCmp("spectrumgrids_invoice_viewitems_btn").setVisible(false);
            Ext.getCmp("spectrumgrids_invoice_resend_btn").setVisible(false);
            Ext.getCmp("spectrumgrids_invoice_releaseDeposit_btn").setVisible(false);
            Ext.getCmp("spectrumgrids_invoice_delete_btn").setVisible(false);
            Ext.getCmp("spectrumgrids_invoice_cancel_btn").setVisible(false);
            
            //Invoices Template
            Ext.getCmp("spectrumgrids_draft_invoice_recipient_btn").setVisible(false);
            Ext.getCmp("spectrumgrids_draft_invoice_viewitems_btn").setVisible(false);
            Ext.getCmp("spectrumgrids_draft_invoice_delete_btn").setVisible(false);
            Ext.getCmp("spectrumgrids_draft_invoice_edit_btn").setVisible(false);
        }
        ,handleButtons:function()
        {
        	 
            var me=this;
            if(me.getSelectionModel().getSelection().length==0)
            {   
                me.resetButtons();
                return;
            }
            //Help [InvoiceStatusId]
            /*
            (1)Template
            (2)Paid
            (3)Pending
            (4)Ready
            (5)Cancelled
            */
            
            var rec  =me.getSelectionModel().getSelection()[0].data;
            if(rec.invoice_status_id!=1)//Invoice
            {
                me.resetButtons();
                
                me.handleVisibility('invoice_items'                     ,true);
                me.handleVisibility('draftinvoice_new'                  ,true);
                
                if(rec.invoice_status_id!=2 && rec.invoice_status_id!=5)
                {
                    if(rec.invoice_slave_oid == me.a_o_id)
                    {
                        me.handleVisibility('invoice_pay'               ,true);
                        me.handleVisibility('invoice_releasedeposit'    ,true);    
                    }   
                }
                if(rec.invoice_status_id==3)
                {
                    if(rec.invoice_master_oid == me.a_o_id)
                        me.handleVisibility('invoice_resend'            ,true);    
                }
                if(rec.invoice_status_id!=5)
                {
                    if(rec.invoice_master_oid == me.a_o_id)
                    {
                        me.handleVisibility('invoice_cancel'            ,true);   
                        me.handleVisibility('invoice_delete'            ,true);   
                    }
                        
                }
            }
            else//Invoice Template
            {
                me.resetButtons();                    
                me.handleVisibility('draftinvoice_edit'    ,true);
                if(rec.invoice_master_oid == me.a_o_id)
                {
                    if(rec.invoice_amount!=0 )
                        me.handleVisibility('draftinvoice_recipient'    ,true);
                        
                    me.handleVisibility('draftinvoice_items'            ,true);
                    me.handleVisibility('draftinvoice_delete'           ,true);   
                }
            }
        }
        ,release_deposit_to_invoice:function()
        {
            var me=this;
            if(me.getSelectionModel().getSelection().length==0)
            {
            	//not an error
                //Ext.MessageBox.alert({title:"Error",msg:"Please select an Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                return;
            }
            var record=me.getSelectionModel().getSelection()[0].data;
            
            var postx={}
            postx["master_entity_id"]   =record.invoice_master_eid;
            postx["slave_entity_id"]    =record.invoice_slave_eid;
            postx["currency_type_id"]   =record.currency_type_id;
            
            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
            //first get the total unused deposit
            Ext.Ajax.request(
            {
                url     : 'index.php/finance/json_get_deposit_balance/TOKEN:'+App.TOKEN,
                params  : postx,
                success : function(response)
                {
                    box.hide();
                    var res=YAHOO.lang.JSON.parse(response.responseText);
                    me.unused_deposits=res.result;
                    if(me.unused_deposits==0)
                    {
                    	//NOT AN ERROR
                        Ext.MessageBox.alert({title:"Cannot finish",msg:"Total Unused Deposit is Zero", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    if(parseInt(record.invoice_owing)==0)
                    {
                    	//NOT AN ERROR
                        Ext.MessageBox.alert({title:"Cannot finish",msg:"Invoice is Fully Paid", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    if(parseInt(record.invoice_status_id)==5)
                    {
                    	//NOT AN ERROR
                        Ext.MessageBox.alert({title:"Cannot finish",msg:"Invoice is Cancelled", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    var _config=
                    {
                        width   : 400,
                        height  : 75,
                        bottomItems:   
                        [
                            '->',
                            {   
                                 id      : 'invoice_transfer_btn',
                                 xtype   : "button",
                                 text    : "Tranfer",
                                 iconCls : 'money',
                                 pressed : true,
                                 width   : 70,
                                 tooltip : 'Delete Invoice',
                                 handler : function()
                                 {                     
                                     Ext.getCmp("invoice_transfer_btn").setDisabled(true);
                                     var post={}
                                     post["invoice_id"]             =record.invoice_id;
                                     post["amount"]                 =me.unused_deposits;
                                     
                                     var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                     Ext.Ajax.request(
                                     {
                                         url     : 'index.php/finance/json_release_deposit_invoice/TOKEN:'+App.TOKEN,
                                         params  : post,
                                         success : function(response)
                                         {
                                              box.hide();
                                              var res=YAHOO.lang.JSON.parse(response.responseText);
                                              if(res.result=="1")
                                              {                           
                                                  Ext.MessageBox.alert({title:"Status",msg:"Deposit Successfully Transfered", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                  me.getStore().load();                                                                  
                                                  me.handleButtons();
                                                  releasedeposit_win.Hide();
                                              }
                                              App.updatePanel();
                                         }
                                     });
                                 }                          
                            }
                        ]
                     }          
                    var releasedeposit_form=financeForms.form_release_deposit_invoice(me.unused_deposits,record.currency_type_name);
                    var releasedeposit_final_form=new Ext.spectrumforms.mixer(_config,[releasedeposit_form],['form']);
                     
                    var win_cnf=
                    {
                        title       : 'Release Deposit to Invoice as Payment',
                        final_form  : releasedeposit_final_form
                    }
                    var releasedeposit_win=new Ext.spectrumwindow.finance(win_cnf);
                    releasedeposit_win.show();
                },
				//always need a failure case. otherwise entire page is locked under the wait box forever
	             failure: function( action){box.hide();App.error.xhr(action);}
            });
                     
        }
        ,view_invoice_items:function()
        {          
             var me=this;
             var addressStore        =new simpleStoreClass().make(['address_id','address_name'],"index.php/finance/json_getOrgAddresses/TOKEN:"+App.TOKEN+'/',{address_type_id:2});
             var emailStore          =new simpleStoreClass().make(['email_id','email_name'],"index.php/finance/json_getOrgEmails/TOKEN:"+App.TOKEN+'/',{test:true});        
            
             var record             = me.getSelectionModel().getSelection()[0].data;    
                                      
             var invoice_items_cnf=
             {  
                    generator       : Math.random(),
                    owner           : me,
                    
                    collapsible     :false,
                    
                    extraParamsMore :{invoice_id    :(record.invoice_status_id==1)?record.invoice_draft_id:record.invoice_id},
                    invoice_status_id:record.invoice_status_id,
                    
                    title           : '',
                    //ExtraDocksTexts :['row1','row2','row3','row4','row5'],
                    
                    a_o_tax_info    : me.a_o_tax_info,
                    applied_tax     : me.applied_tax,
                    
                    //customized Components
                    rowEditable     :false,
                    groupable       :false,
                    bottomPaginator :false,
                    searchBar       :false           
             }
             var invoice_items_grid= Ext.create('Ext.spectrumgrids.invoiceItem',invoice_items_cnf);
             
             /*Build Information Form*/
             //Get the right logo
             var activeOrgLogo;
             
             
             
             if(record.invoice_status_id==4)
                activeOrgLogo   ="assets/uploaded/logo/"+record.master_logo;
             else
                activeOrgLogo   =App.activeOrgLogo;
                       
             
             var infoForm       =financeForms.form_invoice_item_info(addressStore,emailStore,activeOrgLogo);
             var infoFormConfig =
             {
                    width   : "100%",
                    height  : 150
             }          
             var infoFinalForm  =new Ext.spectrumforms.mixer(infoFormConfig,[infoForm],['form']);
             /*Build Information Form*/
             /*Build Information Form2*/
             var infoForm2      =financeForms.form_invoice_item_description();
             var infoFormConfig2=
             {
                    width   : "100%",
                    height  : 150
             }          
             var infoFinalForm2 =new Ext.spectrumforms.mixer(infoFormConfig2,[infoForm2],['form']);
             /*Build Information Form2*/
             /*Build Information Form3*/ 
             var applyTaxComponent= 
             {
                 id          : 'invoice_item_apply_taxes_checkbox',
                 xtype       : 'checkbox',
                 fieldLabel  : 'Apply Taxes ?',
                 checked     : me.applied_tax,
                 width       : 200,
                 listeners   : 
                 {
                     change: 
                     {
                         fn: function(a,checked)
                         {                              
                             var post={}
                             post["tax_applies"]     =((Ext.getCmp('invoice_item_apply_taxes_checkbox').checked==true)?1:0);
                             
                             var URL='';
                             if(record.invoice_status_id==1)
                             {
                                 post["invoice_id"]   =me.getSelectionModel().getSelection()[0].data.invoice_draft_id;
                                 URL='index.php/finance/json_apply_tax_invoice_draft/TOKEN:'+App.TOKEN;
                             }
                                 
                             else 
                             {
                                 post["invoice_id"]   =me.getSelectionModel().getSelection()[0].data.invoice_id;
                                 URL='index.php/finance/json_apply_tax_invoice/TOKEN:'+App.TOKEN;
                             }
                                 
                                 
                             var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                             Ext.Ajax.request(
                             {
                                 url     : URL,
                                 params  : post,
                                 success : function(response)
                                 {
                                     box.hide();
                                     var res =YAHOO.lang.JSON.parse(response.responseText);
                                     if(res.result=="1")
                                     {   
                                         me.ApplyTaxes(checked,invoice_items_grid);
                                     }
                                     if(res.result=="-1")
                                     {
                                        Ext.MessageBox.alert({title:"Error",msg:"This Action is not Possible at this Point [Paid Amount Becomes Greater than Total Amount]", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK}); 
                                        
                                        //Keep old check status
                                        a.suspendEvents();
                                        Ext.getCmp("invoice_item_apply_taxes_checkbox").setValue(!checked);
                                        a.resumeEvents();
                                     }  
                                     //Reload Invoice Grid
                                     me.reloadInvoiceGrid();
                                 }
                                 ,failure:function(o){box.hide();App.error.xhr(o);}
                             }); 
                         },
                         scope: this, 
                         buffer:1                  
                     }       
                 }
             }     
             var infoForm3      =financeForms.form_invoice_item_taxes(applyTaxComponent);
             var infoFormConfig3=
             {
                    width       : "100%",
                    height      : 150,
                    collapsible : false
             }          
             var infoFinalForm3 =new Ext.spectrumforms.mixer(infoFormConfig3,[infoForm2,infoForm3],['form','form']);
             /*Build Information Form3*/
             //Build Final View
             var finalFormConfig=
             {
                    width   : 500,
                    height  : 600,
                    bottomItems:   
                    [
                        '->'
                        ,{   
                             id      :"invoice_item_save_btn",
                             xtype   :"button",
                             text    :"Save",
                             iconCls :'table_save',
                             pressed :true,
                             tooltip :'Save',
                             handler :function()
                             {    
                                 var record=me.getSelectionModel().getSelection()[0].data;                 
                                 var post={}
                                 post["issuerinfo_address"] =Ext.getCmp("address_combo").getValue();
                                 post["issuerinfo_email"]   =Ext.getCmp("email_combo").getValue();
                                 post["invoice_comment"]    =Ext.getCmp("comment").getValue();
                                                      
                                 var url='';
                                 if(record.invoice_status_id==1)                                 
                                 {
                                     post["invoice_id"]             =record.invoice_draft_id;
                                     url='index.php/finance/json_final_save_invoice_draft/TOKEN:'+App.TOKEN;
                                 }                    
                                 else
                                 {
                                     post["invoice_id"]             =record.invoice_id;
                                     url='index.php/finance/json_final_save_invoice/TOKEN:'+App.TOKEN;
                                 }                                                                    
                                 
                                 var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                 Ext.Ajax.request(
                                 {
                                     url     : url,
                                     params  : post,
                                     success : function(response)
                                     {
                                          box.hide();  
                                          var res=YAHOO.lang.JSON.parse(response.responseText);
                                          if(res.result=="1")
                                          {                           
                                              if(record.invoice_status_id==1)
                                                Ext.MessageBox.alert({title:"Status",msg:'Extra Information Saved for Invoice Template Named <b>"'+record.invoice_title+'"</b>', icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                              else
                                                Ext.MessageBox.alert({title:"Status",msg:'Extra Informtion Saved for Invoice Named <b>"'+record.invoice_title+'"</b>', icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                              finalFormWin.Hide();
                                              me.getStore().load();
                                          }
                                     }
                                     ,failure:function(o){box.hide();App.error.xhr(o);}
                                 });
                             }                          
                        }
                    ]
             }          
             var finalForm  =new Ext.spectrumforms.mixer(finalFormConfig,[infoFinalForm,invoice_items_grid,infoFinalForm3],['form','grid','form']);
             var finalFormWinConfig=
             {
                title       : 'Invoice Items',
                final_form  : finalForm
             }
             var finalFormWin   =new Ext.spectrumwindow.finance(finalFormWinConfig);
             finalFormWin.show();
             
             //Set Defaults
             var rightPanelInfo=
             '<table style="width:200;font-family:verdana;font-size:9px">'
             +'<tr>'
             +'<td style="vertical-align:top;text-align:left;">'    
             +'<img width=200 height=60 src="'+activeOrgLogo+'" />'
             +'<br/>'
             +'<b>From</b>:'
             +'<br/>'
             +((record.issuerinfo_address!=null)?record.issuerinfo_address:'')
             +'<br/>'
             +((record.issuerinfo_email!=null)  ?record.issuerinfo_email:'')
             +'</td>'
             +'</tr>'
             +'</table>'
             ;
             
             Ext.getCmp("invoice_item_info_panel").setValue(rightPanelInfo);
             Ext.getCmp("comment").setValue(record.invoice_comment);
             
             //Apply Settings to [Template]Invoice Items Grid
             //Reset
             Ext.getCmp("invoice_items_add_btn").setDisabled(true);
             Ext.getCmp("invoice_items_del_btn").setDisabled(true);
             Ext.getCmp('invoice_item_apply_taxes_checkbox').setDisabled(true);
             Ext.getCmp('comment').setDisabled(true);
             
             
             if
             (
                    record.invoice_status_id==1 
                ||  record.invoice_status_id==3
                ||  (record.invoice_status_id==4 &&  me.a_o_id==record.invoice_master_oid)
             )
             {
                
                Ext.getCmp("invoice_items_add_btn").setDisabled(false);
                Ext.getCmp("invoice_items_del_btn").setDisabled(false);
                Ext.getCmp('invoice_item_apply_taxes_checkbox').setDisabled(false);
                
             }       
             if
             (
                    record.invoice_status_id==2
                ||  record.invoice_status_id==5
             )
             {
                Ext.getCmp("invoice_items_add_btn").setDisabled(true);
                Ext.getCmp("invoice_items_del_btn").setDisabled(true);
                Ext.getCmp('invoice_item_apply_taxes_checkbox').setDisabled(true);
             }                                       
             
             if(me.a_o_id==record.invoice_master_oid)
             {
                Ext.getCmp('comment').setDisabled(false);
                Ext.getCmp("invoice_item_save_btn").setDisabled(false);
                
                Ext.getCmp('address_combo').setVisible(true);
                Ext.getCmp('email_combo').setVisible(true);
                Ext.getCmp('field_receiver_Info').setVisible(false);
             }  
             else
             {
                Ext.getCmp('comment').setDisabled(true);
                Ext.getCmp("invoice_item_save_btn").setDisabled(true);
                                                     
                Ext.getCmp('address_combo').setVisible(false);
                Ext.getCmp('email_combo').setVisible(false);
                Ext.getCmp('field_receiver_Info').setVisible(true);
                
                Ext.getCmp('field_receiver_Info').setValue('TO :<br/><br/>'
                +((record.slaveinfo_address  !=null)?record.slaveinfo_address:'')
                +'<br/><br/>'
                +((record.slaveinfo_email    !=null)?record.slaveinfo_email  :''));
             }
              
             //Check on Tax at issuer side
             if(me.a_o_tax_info == null && me.a_o_id==record.invoice_master_oid)
                Ext.getCmp("invoice_item_apply_taxes_checkbox").setDisabled(true);        
        }
        ,pay:function(activeUserOrgEntity,DevOrLive)
        {
               var spittedActiveInfo=activeUserOrgEntity.split(',');
               
               var me   =this;
               if(me.getSelectionModel().getSelection().length==0)
               {
                  Ext.MessageBox.alert({title:"Cannot Pay",msg:"Please select an Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                  return;
               }
               var record   =me.getSelectionModel().getSelection()[0].data;
               var formPaymentConfig=
               {
                      width   : 250,
                      height  : 275,
                      bottomItems:   
                      [  
                          '->' 
                          //payment
                          ,{   
                               id      :'spectrumgrids_invoice_paydecision_btn',
                               xtype   :"button",
                               text    :"Pay",
                               iconCls :'money',
                               pressed :true,
                               width   :50,
                               tooltip :'',
                               handler :function()
                               {  
                                  Ext.getCmp("spectrumgrids_invoice_paydecision_btn").setDisabled(true);
                                  var formValues    =formPayment.getForm().getValues();                       
                                  if(parseFloat(formValues["amount"])>record.invoice_owing)
                                  {
                                      Ext.MessageBox.alert({title:"Cannot Pay",msg:"Please enter desirable payment amount equal or less than Payable amount", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                      return;
                                  }
                                  if(formValues["payment_type"]=="1") //SYSTEM          [WALLET]          PAYMENT
                                  {
                                      
                                      var post={}
                                      post["invoice_id"]          =record.invoice_id;
                                      post["amount"]              =formValues["amount"];
                                      post["payable_amount"]      =record.invoice_owing;
                                      post["currency_type_id"]    =record.currency_type_id;
                                      
                                      var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                      Ext.Ajax.request(
                                      {
                                          url     : 'index.php/finance/json_pay_invoice_internal/'+App.TOKEN, 
                                          params  : post,
                                          success : function(response)
                                          {
                                               box.hide();
                                               var res=YAHOO.lang.JSON.parse(response.responseText);
                                               if(parseInt(res.result)>=1)
                                               {                           
                                                   Ext.MessageBox.alert({title:"Status",msg:"Payment Done Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                   me.getStore().load();   
                                                   me.handleButtons();
                                                   formPaymentWin.Hide();
                                               }
                                               if(res.result=="-1")
                                               {                           
                                                   Ext.MessageBox.alert({title:"Cannot Pay",msg:"Wallet Balance Is Not Enough to Make a Payment", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                   Ext.getCmp("spectrumgrids_invoice_paydecision_btn").setDisabled(false);
                                               }
                                               if(res.result=="-2")
                                               {                           
                                                   Ext.MessageBox.alert({title:"Cannot Pay",msg:"Desirable Payment must not be greater than Payable Amount", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                   me.getStore().load();   
                                                   me.handleButtons();
                                                   Ext.getCmp("spectrumgrids_invoice_paydecision_btn").setDisabled(false);
                                               }
                                               App.updatePanel(); 
                                          }
                                          ,failure:function(o){box.hide(); App.error.xhr(o);}
                                      });    
                                  }
                                  if(formValues["payment_type"]=="2") //CHASE           [CREDIT-CARD]     PAYMENT
                                  {                                    
                                      var userAddressStore  =new simpleStoreClass().make(['address_id','address_name'],"index.php/finance/json_get_user_addresses/TOKEN:"+App.TOKEN,{test:true});        
                                      var formCreditcardConfig  =
                                      {
                                          width   :500,
                                          height  :500
                                      }
                                      var formCreditcardContent='<iframe frameborder=0 src="'+spittedActiveInfo[3]+'/index.php/finance/json_get_cc_payment_form'
                                                 +'/'   +App.TOKEN
                                                 //GENERAL
                                                 +'/'   +record.currency_type_id
                                                 +'/'   +record.currency_type_name
                                                 +'/'   +formValues["amount"]
                                                 +'/'   +record.invoice_owing
                                                 //INVOICE-SPECIFIC  FROM BE
                                                 +'/'   +record.invoice_id
                                                 //DEPOSIT-SPECIFIC  FROM FE
                                                 +'/'   +'NONE'
                                                 +'/'   +'NONE'
                                                 +'/'   +'NONE'
                                                 +'/'   +'NONE'
                                                 +'/'   +'NONE'
                                                 +'/'   +'NONE'
                                                 +'/'   +'NONE'
                                                 +'/'   +'NONE'
                                                 //lOGGED-IN INFO FOR LIVE CONNECTION
                                                 +'/'   +spittedActiveInfo[0]
                                                 +'/'   +spittedActiveInfo[1]
                                                 +'/'   +spittedActiveInfo[2]                                    
                                                 
                                                 +'/'   +Math.random()  
                                                 
                                                 +'" width="470px" height="485px" ></iframe>';
                                     
                                                 
                                      var formCreditcard        =financeForms.form_cc_payment(formCreditcardContent);
                                      var formCreditcardFinal   =new Ext.spectrumforms.mixer(formCreditcardConfig,[formCreditcard],['form']);
                                      var formCreditcardWinConfig=
                                      {
                                          title       : 'CreditCard Payment Gateway',
                                          final_form  : formCreditcardFinal
                                      }
                                      var formCreditcardWin=new Ext.spectrumwindow.finance(formCreditcardWinConfig);
                                      formCreditcardWin.show();                           
                                      formCreditcardWin.on("close",function()
                                      {
                                          formCreditcardWin.Hide();
                                          formPaymentWin.Hide();
                                          App.updatePanel();
                                          me.getStore().load();                                                                                               
                                          me.handleButtons();
                                      })
                                  }               
                                  if(formValues["payment_type"]=="3") //HOSTED-CHECKOUT [INTERAC]         PAYMENT
                                  {
                                      var formInteracContent  =financeForms.form_intrac_payment(
                                      '<iframe frameborder=0 src="'+spittedActiveInfo[3]+'/index.php/finance/json_get_io_payment_form'
                                       +'/'   +App.TOKEN
                                       //GENERAL
                                       +'/'   +record.currency_type_id
                                       +'/'   +record.currency_type_name
                                       +'/'   +formValues["amount"]
                                       +'/'   +record.invoice_owing
                                       //INVOICE-SPECIFIC  FROM BE
                                       +'/'    +record.invoice_id
                                       //DEPOSIT-SPECIFIC  FROM FE
                                       +'/'   +'NONE'
                                       +'/'   +'NONE'
                                       +'/'   +'NONE'
                                       +'/'   +'NONE'
                                       +'/'   +'NONE'
                                       +'/'   +'NONE'
                                       +'/'   +'NONE'
                                       +'/'   +'NONE'
                                       //lOGGED-IN INFO FOR LIVE CONNECTION
                                       +'/'   +spittedActiveInfo[0]
                                       +'/'   +spittedActiveInfo[1]
                                       +'/'   +spittedActiveInfo[2]                                       
                                       +'/'   +Math.random()  
                                       
                                       +'" width="100%" height="600">'
                                      +'</iframe>');
                                      var formInteracFinal  =new Ext.spectrumforms.mixer({width:800,height:600},[formInteracContent],['form']);
                                      var formInteracWinConfig=
                                      {
                                          title       : 'INTERAC Gateway',
                                          final_form  : formInteracFinal
                                      }
                                      var formInteracWin=new Ext.spectrumwindow.finance(formInteracWinConfig);
                                      formInteracWin.show();
                                      formInteracWin.on("close",function()
                                      {
                                          formPaymentWin.Hide();
                                          me.getStore().load();
                                          me.handleButtons();
                                          App.updatePanel(); 
                                      });
                                  }  
                               }                          
                          }
                      ]
               }   
               
               var formPayment          =financeForms.form_payment(parseInt(record.currency_is_system),DevOrLive);
               var formPaymentFinal     =new Ext.spectrumforms.mixer(formPaymentConfig,[formPayment],['form']);
               
               var formPaymentWinConfig =
               {
                  title       : 'Payment Options',
                  final_form  : formPaymentFinal
               }
               var formPaymentWin=new Ext.spectrumwindow.finance(formPaymentWinConfig);
               formPaymentWin.show();
               //load records
               var G2=Math.random();
               Ext.define('model_'+G2,{extend: 'Ext.data.Model'});
               formPayment.loadRecord(Ext.ModelManager.create(
               {
                  'payable_amount'   : record.invoice_owing,
                  'amount'           : record.invoice_owing
               }, 'model_'+G2));
        }
        //Template-Related Functions
        ,send_to_recipients:function()
        {
        	var orgLevelStore   =new simpleStoreClass().make(['org_type_id','org_type_name'],"index.php/finance/json_org_levels_store/TOKEN:"+App.TOKEN+'/',{test:true}); 
        	 
             var me                 =this;
             if(me.getSelectionModel().getSelection().length==0)
             {
                Ext.MessageBox.alert({title:"Error",msg:"Please select an Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                return;
             }
             var record             =me.getSelectionModel().getSelection()[0].data;
            
             var entityGen          =Math.random(); 
             var entityGridConfig   =
             {  
                    generator       : entityGen,
                    owner           : me,
                    url             : "index.php/finance/json_getHierarchicalEntities/TOKEN:"+App.TOKEN,
                    
                    title           : '',
                    selModel        : Ext.create('Ext.selection.CheckboxModel', 
                    {
                        mode        :'MULTI'
                    }),
                   
                    orgLevelStore   :orgLevelStore,
                    autoLoad        :false,
                    collapsible     :false,
                    startCollapsed  :false,
                    //customized Components
                    rowEditable     :false,
                    cellEditable    :true,
                    groupable       :true,
                    bottomPaginator :true,
                    searchBar       :true           
             }
             
             var entitiesGrid           = Ext.create('Ext.spectrumgrids.entity',entityGridConfig);
             var formDescription        = financeForms.form_description();
             var formDescriptionConfig  =
             {
                    width       : 900,
                    height      : 400,
                    collapsible :false,
                    bottomItems:   
                    [
                        '->'                
                         ,{                  
                                        text    : 'Save & Submit',
                                        width   : 100,
                                        tooltip : 'Save & Submit',
                                        iconCls : 'disk',
                                        handler : function()
                                        {  
                                        	var draftRecord =me.getSelectionModel().getSelection()[0].data;
                                            if(formDescriptionFinal.getForm().isValid())
                                            {
                                                var recipient_eids          ='';
                                                var recipient_enames        ='';
                                                var recipient_custom_nums   ='';
                                                
                                                var entity_grid_records     =entitiesGrid.getSelectionModel().getSelection();
                                                if(entity_grid_records.length==0)
                                                {
                                                    Ext.MessageBox.alert({title:"Error",msg:"Please select at least one recipient", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    return;
                                                }                                
                                                for(var i=0;i<entity_grid_records.length;i++)
                                                {
                                                    recipient_eids              +=entity_grid_records[i].data.entity_id+',';
                                                    recipient_custom_nums       +=entity_grid_records[i].data.custom_empty+',';
                                                    recipient_enames            +="("+(parseInt(i)+1).toString()+") <i>"+entity_grid_records[i].data.entity_name+"</i>"
                                                                                +"   ("+(entity_grid_records[i].data.custom_empty==''?'---':entity_grid_records[i].data.custom_empty)+")<br/>";
                                                }                                                                  
                                                recipient_custom_nums   =recipient_custom_nums.substring(0,recipient_custom_nums.length-1);
                                                recipient_eids          =recipient_eids.substring(0,recipient_eids.length-1);                     
                                                recipient_enames        =recipient_enames.substring(0,recipient_enames.length-5);
                                                                      
                                                Ext.MessageBox.confirm('Confirm Submitting', "one Copy of Invoice Template Named <b>"+draftRecord.invoice_description+"</b> will be Sent to Each Entities Listed :<br/><br/>"+recipient_enames
                                                        +"<br/><br/><input id='keep_draft_cb' type='checkbox' 'checked' /><b> Keep Template ?</b>", function(answer)
                                                {     
                                                    if(answer=="yes")
                                                    {
                                                        var keep_draft_cb=document.getElementById("keep_draft_cb").checked;
                                                            
                                                        var post={}
                                                        post["invoice_id"]              =record.invoice_draft_id;
                                                        post["recipient_eids"]          =recipient_eids;
                                                        post["recipient_custom_nums"]   =recipient_custom_nums;
                                                        post["description"]             =formDescriptionFinal.getForm().getValues()["description"];
                                                        post["just_save"]               ="false";
                                                        post["due_date"]                =formDescriptionFinal.getForm().getValues()['due_date'];
                                                        post["keep_draft_cb"]           =keep_draft_cb;
                                                        post["tax_applies"]             =((me.applied_tax==true)?1:0)
                                                        
                                                        var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                        Ext.Ajax.request(
                                                        {
                                                            url     : 'index.php/finance/json_close_invoice/TOKEN:'+App.TOKEN,
                                                            params  : post,
                                                            success : function(response)
                                                            {
                                                                box.hide();
                                                                var res=YAHOO.lang.JSON.parse(response.responseText);
                                                                 if(res.result=="1")
                                                                 {
                                                                     box.hide();
                                                                     Ext.MessageBox.alert({title:"Status",msg:"Invoices Sent Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                     formDescriptionWin.Hide();
                                                                     me.getStore().load();
                                                                     App.updatePanel();
                                                                 }
                                                                 if(res.result=="-2")
                                                                 {
                                                                     box.hide();
                                                                     Ext.MessageBox.alert({title:"Invalid Price",msg:"Charge Item Price does not match with Deposit", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                                 }
                                                            }
                                                            //sb: added error message on failure: task 1539
                                                            ,failure:function(r)
                                                            {
																box.hide();
																App.error.xhr(r);
                                                            }
                                                        });
                                                    }
                                                    
                                                });
                                            }      
                                         }                          
                                    } 
                         ,'-'
                         ,{
                                        text    : 'Save',
                                        tooltip : 'Save',
                                        iconCls : 'disk',
                                        handler :function()
                                        {
                                        	var draftRecord =me.getSelectionModel().getSelection()[0].data;
                                            if(formDescriptionFinal.getForm().isValid())
                                            {
                                                var recipient_eids          ='';
                                                var recipient_enames        ='';
                                                var recipient_custom_nums   ='';
                                                
                                                var entity_grid_records     =entitiesGrid.getSelectionModel().getSelection();
                                                if(entity_grid_records.length==0)
                                                {
                                                	//this is NOT an error
                                                    Ext.MessageBox.alert({title:"Cannot send yet",msg:"Please select at least one recipient", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    return;
                                                }
                                                for(var i=0;i<entity_grid_records.length;i++)
                                                {
                                                    recipient_eids              +=entity_grid_records[i].data.entity_id+',';
                                                    recipient_custom_nums       +=entity_grid_records[i].data.custom_empty+',';
                                                    recipient_enames            +="("+(parseInt(i)+1).toString()+") <i>"+entity_grid_records[i].data.entity_name+"</i>"
                                                                                +"   ("+entity_grid_records[i].data.custom_empty+")<br/>";
                                                }                                                                  
                                                recipient_custom_nums   =recipient_custom_nums.substring(0,recipient_custom_nums.length-1);
                                                recipient_eids          =recipient_eids.substring(0,recipient_eids.length-1);                     
                                                recipient_enames        =recipient_enames.substring(0,recipient_enames.length-5);
                                                                      
                                                Ext.MessageBox.confirm('Confirm Submitting', "one Copy of Invoice Template Named <b>"+draftRecord.invoice_description+"</b> will be Saved to Each Entities Listed :<br/><br/>"+recipient_enames, function(answer)
                                                {     
                                                    if(answer=="yes")
                                                    {
                                                        var post={}
                                                        post["invoice_id"]              =record.invoice_draft_id;
                                                        post["recipient_eids"]          =recipient_eids;
                                                        post["recipient_custom_nums"]   =recipient_custom_nums;
                                                        post["description"]             =formDescriptionFinal.getForm().getValues()["description"];
                                                        post["just_save"]               ="true";
                                                        post["due_date"]=formDescriptionFinal.getForm().getValues()['due_date'];
                                                        post["tax_applies"]             =((Ext.getCmp('invoice_item_apply_taxes_checkbox').checked==true)?1:0)
                                                        
                                                        var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                        Ext.Ajax.request(
                                                        {
                                                            url     : 'index.php/finance/json_close_invoice/TOKEN:'+App.TOKEN,
                                                            params  : post,
                                                            success : function(response)
                                                            {
                                                                 box.hide();
                                                                 var res=YAHOO.lang.JSON.parse(response.responseText);
                                                                 if(res.result=="1")
                                                                 {
 
                                                                     Ext.MessageBox.alert({title:"Status",msg:"Invoices Sent Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                     formDescriptionWin.Hide();
                                                                     me.getStore().load();
                                                                     App.updatePanel();
                                                                 }
                                                                 if(res.result=="-2")
                                                                 {                     
                                                                     Ext.MessageBox.alert({title:"Invalid Price",msg:"Charge Item Price does not match with Deposit", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                                 }
                                                            }
                                                            ,failure:function(r)
                                                            {
																box.hide();//hide on fail
																App.error.xhr(r);
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        }
                                    }
                    ]
             }
                       
             var formDescriptionFinal       =new Ext.spectrumforms.mixer(formDescriptionConfig,[entitiesGrid,formDescription],['grid','form']);
             var formDescriptionWinConfig   =
             {
                title       : 'Recipient List',
                final_form  : formDescriptionFinal
             }
             var formDescriptionWin=new Ext.spectrumwindow.finance(formDescriptionWinConfig);
             formDescriptionWin.show();
              
             //Set Default Due Date to ToDay
             var today              = new Date();
             Ext.getCmp("duedate_calendar").setValue(today);
             
             //Select and Fire-up first First item in LEVELS Dropdown List
             Ext.getCmp("transaction_level_filter").store.on("load",function()
             {
                 var rec    =Ext.getCmp("transaction_level_filter").store.getAt(0);
                 Ext.getCmp("transaction_level_filter").select(rec);    
             });
        }
        ,getTotalTax:function()  
        {
            var me=this;
            var totalTax=0;
            if(Ext.getCmp('invoice_item_apply_taxes_checkbox').checked==true)
            {
                for(var i=0;i<me.a_o_tax_info.length;i++)
                    totalTax+=parseFloat(me.a_o_tax_info[i].rate)
                return totalTax;
            }   
            else return 0;
        }
        ,ApplyTaxes:function(checkboxOption,grid)
        {       
                var me=this;                                                                    
                var sum=0;
                var Items=grid.getStore().data.items;
                for(var i =0 ;i<Items.length;i++)
                     sum+=(Items[i].data.charge_price*Items[i].data.quantity);
               
                var currency    =" ("+me.getSelectionModel().getSelection()[0].data.currency_type_name+")";
                if(checkboxOption==true)
                {
                    /*
                    Ext.getCmp('row5').setText('row5','Price : '        , sum.toFixed(2)+' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);               
                    Ext.getCmp('row4').setText('row4','Federal Tax: '   ,(sum*parseFloat(me.config.a_o_f_tax)).toFixed(2)+' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);
                    Ext.getCmp('row3').setText('row3','Regional Tax: '  ,(sum*parseFloat(me.config.a_o_p_tax)).toFixed(2)+' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);
                    Ext.getCmp('row2').setText('row2','Local Tax: '     ,(sum*parseFloat(me.config.a_o_l_tax)).toFixed(2)+' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);   
                    Ext.getCmp('row1').setText('row1','Total Price : '  ,(sum*(parseFloat(1)+me.getTotalTax())).toFixed(2)+' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);
                    */
                    
                    var content     =
                    '<table style="width:100%;font-family:verdana;font-size:9">'
                     
                    +'<tr>'
                    +'<td style="text-align:left;vertical-align:top;width:130">'
                    +'Total'+currency+' :'
                    +'</td>'
                    +'<td style="text-align:right;vertical-align:top;width:70">'
                    +Math.round(sum*100)/100
                    +'</td>'
                    +'</tr>';
                    
                    for(var i=0;i<me.a_o_tax_info.length;i++)
                    {
                        content+='<tr>'
                        +'<td style="text-align:left;vertical-align:top;width:130">'
                        +me.a_o_tax_info[i].label+currency+' :'
                        +'</td>'
                        +'<td style="text-align:right;vertical-align:top;width:70">'
                        +Math.round(sum*parseFloat(me.a_o_tax_info[i].rate)*100)/100
                        +'</td>'
                        +'</tr>';
                    }
                    
                    content+='<tr>'
                    +'<td style="text-align:left;vertical-align:top;width:130">'
                    +''
                    +'</td>'
                    +'<td style="text-align:right;vertical-align:top;width:70">'
                    +'====='
                    +'</td>'
                    +'</tr>'
                    
                    +'<tr>'
                    +'<td style="text-align:left;vertical-align:top;width:130">'
                    +'Total Rate '+currency+' :'
                    +'</td>'
                    +'<td style="text-align:right;vertical-align:top;width:70">'
                    +'$ '+Math.round(sum*(parseFloat(1)+me.getTotalTax())*100)/100
                    +'</td>'
                    +'</tr>'
                    
                    +'</table>'
                    
                    
                    Ext.getCmp("invoice_item_tax_panel").setValue(content);
                                                                                                                                                      
                    //var x = new Ext.financeToolbar(
                    //{
                    //      id          : 'row2',
                    //      xtype       : 'financeToolbar',
                    //      dock        : 'bottom',
                    //      Text        : 'row2',
                    //      generator   : 'row2',
                    //      comp_name   : 'row2',
                    //});
                    //me.add( x );
                    //this.addAfter( a, c );
                    //me.doLayout();
                    
                }          
                else
                {   
                    /*
                    Ext.getCmp('row5').setText('row5','Price : '        ,sum.toFixed(2) +' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);
                    Ext.getCmp('row4').setText('row4','Federal Tax: '   ,'0.00'         +' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);         
                    Ext.getCmp('row3').setText('row3','Regional Tax: '  ,'0.00'         +' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);
                    Ext.getCmp('row2').setText('row2','Local Tax: '     ,'0.00'         +' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);
                    Ext.getCmp('row1').setText('row1','Total Price : '  ,sum.toFixed(2) +' $'+me.owner.getSelectionModel().getSelection()[0].data.currency_type_name);
                    */
                    var content     =
                    '<table style="width:100%;font-family:verdana;font-size:9">'
                     
                    +'<tr>'
                    +'<td style="text-align:left;vertical-align:top;width:130">'
                    +'Total'+currency+' :'
                    +'</td>'
                    +'<td style="text-align:right;vertical-align:top;width:70">'
                    +Math.round(sum*100)/100
                    +'</td>'
                    +'</tr>'
                    
                    +'<tr>'
                    +'<td style="text-align:left;vertical-align:top;width:130">'
                    +''
                    +'</td>'
                    +'<td style="text-align:right;vertical-align:top;width:70">'
                    +'====='
                    +'</td>'
                    +'</tr>'
                    
                    +'<tr>'
                    +'<td style="text-align:left;vertical-align:top;width:130">'
                    +'Total Rate '+currency+' :'
                    +'</td>'
                    +'<td style="text-align:right;vertical-align:top;width:70">'
                    +'$ '+Math.round(sum*(parseFloat(1)+me.getTotalTax())*100)/100
                    +'</td>'
                    +'</tr>'
                    
                    +'</table>'
                    
                    Ext.getCmp("invoice_item_tax_panel").setValue(content);
                }               
        }
        ,reloadInvoiceGrid:function()
        {
            var me=this;
            var record  =me.getSelectionModel().getSelection()[0].data;
            me.getStore().load();   
            me.getStore().on("load",function()
            {   
                var recIndex;
                if(record.invoice_status_id==1)                                              
                    recIndex=me.getStore().find( "invoice_draft_id", record.invoice_draft_id, 0, true, false, false);    
                else
                    recIndex=me.getStore().find( "invoice_id", record.invoice_id, 0, true, false, false);    
                    
                me.getSelectionModel().select( recIndex, true, false); 
            });
        }
        ,cancelInvoice:function(record)
        {
            var me=this;
            Ext.MessageBox.confirm('Invoice Cancellation Action', "Are you sure ?", function(answer)
            {     
                   if(answer=="yes")
                   {
                        var post={}                                
                        post["invoice_id"]  =record.invoice_id;

                        var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                        Ext.Ajax.request(
                        {
                            url     : 'index.php/finance/json_cancel_invoice/TOKEN:'+App.TOKEN, 
                            params  : post,
                            success : function(response)
                            {
                                   box.hide();
                                   var res=YAHOO.lang.JSON.parse(response.responseText);
                                   
                                   if(res.result=="1")
                                   {   
                                       Ext.MessageBox.alert({title:"Status",msg:"Invoice Cancelled Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                       me.getStore().load();   
                                       me.handleButtons();
                                   }
                                   if(res.result=="-1")
                                   {   
                                       Ext.MessageBox.alert({title:"Status",msg:"ERROR cancelling Invoice #"+res.result, icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                       me.getStore().load();   
                                       me.handleButtons();
                                   }
                                   if(res.result=="-2")
                                   {                                                                            
                                       Ext.MessageBox.alert({title:"Error",msg:"No Enough Wallet Balance to Complete Cancellation[2]", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                       return;
                                   }
                                   App.updatePanel();
                            }
                        });                                            
                   }
            });
        }
});
