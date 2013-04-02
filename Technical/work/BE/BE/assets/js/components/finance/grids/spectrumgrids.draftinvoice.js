Ext.define('Ext.spectrumgrids.draftinvoice',    
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
        
        ,a_o_f_tax              :null
        ,a_o_p_tax              :null
        ,a_o_l_t                :null
        
        ,applied_tax            :null
        ,constructor : function(config) 
        {   
            var me=this;                             
            config.columns  =
            [    
                {
                    text        : "From"
                    ,dataIndex  : "invoice_master_ename"
                    ,width      : 100
                }
                ,{
                    text        : "Title"
                    ,dataIndex  : "invoice_description"
                    ,width      : 300
                }
                ,{
                    text        : "Amount"
                    ,dataIndex  : "invoice_amount"
                    ,width      : 70
                }
                ,{
                    text        : "Currency"
                    ,dataIndex  : "currency_type_name"
                    ,width      : 70
                }
                ,{
                    text        : "Issued Date"
                    ,dataIndex  : "date_issued_display"
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
            
            var currencyTypeStore   =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_currencytypes/TOKEN:"+App.TOKEN+'/',{test:true});        
            
            config.topItems.push
            (
                
            ); 
            
            config.bottomLItems.push
            (
                //Generate New Draft Invoice 
                 {
                        iconCls :'add',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Create Invoice Draft',
                        handler : function()
                        {
                            var _config=
                            {
                                width   : 300,
                                height  : 150,
                                bottomItems:   
                                [
                                    '->'
                                    ,{   
                                         xtype   :"button",
                                         text    :"Save",
                                         iconCls :'table_save',
                                         pressed :true,
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
                                                         if(res.result!="-1")
                                                         {
                                                             win.Hide();
                                                             Ext.MessageBox.alert({title:"Status",msg:"Draft Saved Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                             me.getStore().load();
                                                         }           
                                                         
                                                     },
                                                     failure: function(form, action){alert(action.response.responseText);}
                                                 }); 
                                             
                                             }   
                                         }                          
                                    }
                                ]
                            }          
                            var form=financeForms.form_invoice(currencyTypeStore);
                            var final_form=new Ext.spectrumforms.mixer(_config,[form],['form']);
                            var win_cnf=
                            {
                                title       : 'Build New Invoice Draft',
                                final_form  : final_form
                            }
                            var win=new Ext.spectrumwindow.finance(win_cnf);
                            win.show();
                        }
                    } 
            );       
            config.bottomRItems.push
            (
                //Send to recipients and close the invoice
                {
                        id      : 'spectrumgrids_invoice_send_btn',
                        iconCls : 'application_cascade',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Pick Recipients & Generate the Invoice',
                        hidden  : true,
                        handler : function()
                        {
                            me.send_to_recipients();
                        }   
                    }   
                //View invoice Items list
                ,{
                        id      : 'spectrumgrids_invoice_viewitems_btn',
                        iconCls : 'application_add',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Invoice Items',
                        hidden  : true,
                        handler : function()
                        {
                            me.view_items();
                        }
                    }   
                //Delete or void  invoice 
                ,{
                        id      : 'invoice_delete_void_btn',
                        iconCls : 'delete',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Delete Invoice',
                        hidden  : true,
                        handler : function()
                        {
                             if(me.getSelectionModel().getSelection().length==0)
                             {
                                Ext.MessageBox.alert({title:"Error",msg:"Please select an Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                             }
                             var record=me.getSelectionModel().getSelection()[0].data;
                             
                             //htmlDescription
                             var htmlDescription='';
                             var hasDel=false;
                             var hasVoid=false;
                             
                             //Delete
                             if(((record.invoice_status_id!=1&& record.invoice_paid==0)||record.invoice_status_id==1) && me.a_o_id==record.invoice_master_oid )
                                hasDel=true;
                             else                                            
                                hasDel=false;
                             
                             //Void
                             if((record.invoice_status_id==2 || record.invoice_status_id==3 || record.invoice_status_id==4) && me.a_o_id==record.invoice_master_oid )
                                hasVoid=true;
                             else 
                                hasVoid=false;
                                
                             if(hasDel==false   && hasVoid==false) htmlDescription='Sorry, No Action Permitted';
                             if(hasDel==false   && hasVoid==true)  htmlDescription='Only Void Action Permitted at This Point';
                             if(hasDel==true    && hasVoid==false) htmlDescription='Only Delete Action Permitted at This Point';
                             if(hasDel==true    && hasVoid==true)  htmlDescription='Both Void and Delete Actions Permitted at This Point';
                             //**************************************
                             
                
                             var action_config=
                             {
                                width   : 300,
                                height  : 75,
                                bottomItems:   
                                [
                                    {   
                                         id      : 'invoice_delete_btn',
                                         xtype   :"button",
                                         text    :"Delete",
                                         iconCls :'delete',
                                         pressed :true,
                                         width   :70,
                                         tooltip :'Delete Invoice',
                                         handler :function()
                                         {                     
                                             Ext.MessageBox.confirm('Delete Action', "Are you sure ?", function(answer)
                                             {     
                                                    if(answer=="yes")
                                                    {
                                                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                         var post={}
                                                         post["invoice_id"]=record.invoice_id;
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
                                                                        Ext.MessageBox.alert({title:"Status",msg:"Invoice got Deleted Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                        me.getStore().load();
                                                                        Ext.getCmp("invoice_delete_void_btn").hide();   
                                                                        void_delete_win.Hide();
                                                                  }
                                                                  if(res.result=="-1")
                                                                  {   
                                                                        Ext.MessageBox.alert({title:"Error",msg:"Error Deleting Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                                        me.getStore().load();   
                                                                        Ext.getCmp("invoice_items_delete_void_btn").hide();
                                                                  }
                                                             }
                                                         });
                                                    } 
                                             });
                                         }                          
                                    }
                                    ,'->'
                                    ,{   
                                         id      : 'invoice_void_btn',   
                                         xtype   :"button",
                                         text    :"Void",
                                         iconCls :'vector_delete',
                                         pressed :true,
                                         width   :70,
                                         tooltip :'Void Invoice',
                                         handler :function()
                                         {             
                                             Ext.MessageBox.confirm('Void Action', "Are you sure ?", function(answer)
                                             {     
                                                    if(answer=="yes")
                                                    {
                                                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                         var post={}
                                                         post["invoice_id"] =record.invoice_id;
                                                         post["action"]     ="void";
                                                         Ext.Ajax.request(
                                                         {
                                                             url     : 'index.php/finance/json_void_invoice/TOKEN:'+App.TOKEN, 
                                                             params  : post,
                                                             success : function(response)
                                                             {
                                                                    box.hide();
                                                                    var res=YAHOO.lang.JSON.parse(response.responseText);
                                                                    if(res.result=="1")
                                                                    {   
                                                                        Ext.MessageBox.alert({title:"Status",msg:"Invoice got Voided Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                        me.getStore().load();   
                                                                        Ext.getCmp("invoice_delete_void_btn").hide();   
                                                                        void_delete_win.Hide();
                                                                    }
                                                                    
                                                                    if(res.result=="-1")
                                                                    {   
                                                                        Ext.MessageBox.alert({title:"Error",msg:"Error Voiding Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                                        me.getStore().load();   
                                                                        Ext.getCmp("invoice_items_delete_void_btn").hide();   
                                                                    }
                                                             }
                                                         });                                            
                                                    }
                                             });                             
                                         }                          
                                    }
                                ]
                             }  
                             var void_delete_form=financeForms.form_cancel_delete_invoice(currencyTypeStore,htmlDescription);
                             var void_delete_final_form=new Ext.spectrumforms.mixer(action_config,[void_delete_form],['form']);
                             
                             var win_cnf=
                             {
                                title       : 'Select Action',
                                final_form  : void_delete_final_form
                             }
                             var void_delete_win=new Ext.spectrumwindow.finance(win_cnf);
                             void_delete_win.show();
                             
                             
                             //Delete or void button or both
                             //Delete
                             if(((record.invoice_status_id!=1&& record.invoice_paid==0)||record.invoice_status_id==1) && me.a_o_id==record.invoice_master_oid )
                                 Ext.getCmp("invoice_delete_btn").show();
                             else                                            
                                 Ext.getCmp("invoice_delete_btn").hide();
                             
                             //Void
                             if((record.invoice_status_id==2 || record.invoice_status_id==3 || record.invoice_status_id==4) && me.a_o_id==record.invoice_master_oid )
                                Ext.getCmp("invoice_void_btn").show();
                             else 
                                Ext.getCmp("invoice_void_btn").hide();
                        }
                    }   
               
            );    
            
            
            if(config.fields==null)     config.fields       = ['invoice_id','invoice_master_oid','invoice_master_eid','invoice_master_ename','invoice_slave_oid','invoice_slave_eid','invoice_slave_ename','invoice_number','custom_invoice_number','custome_invoice_number','invoice_description','invoice_amount','invoice_paid','invoice_owing','date_issued_display','date_due_display','invoice_status_id','invoice_status_name','currency_type_id','currency_type_name','created_by','created_by_name','created_on_display','invoice_description'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 100000;
            if(config.url==null)        config.url          = "/index.php/finance/json_get_draft_invoices/TOKEN:"+App.TOKEN;
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
             
            if(config.groupField==null) config.groupField   = "invoice_status_name";
            
            this.override_edit          =config.override_edit;
            this.override_selectiochange=config.override_selectionchange;
            this.override_itemdblclick  =config.override_itemdblclick;
            this.override_collapse      =config.override_collapse;
            this.override_expand        =config.override_expand;
            
            this.config=config;
            this.callParent(arguments); 
            
            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
            Ext.Ajax.request(
            {
                url: 'index.php/permissions/json_get_active_org_and_type/TOKEN:'+App.TOKEN,
                params: {test:'test'},
                success: function(response)
                {
                    box.hide();
                    var res=YAHOO.lang.JSON.parse(response.responseText);
                    me.a_o_t_id    =res.result.org_type_id;
                    me.a_o_id      =res.result.org_id;
                    me.a_o_name    =res.result.org_name;
                    me.a_o_f_tax   =res.result.org_f_tax;
                    me.a_o_p_tax   =res.result.org_p_tax;
                    me.a_o_l_tax   =res.result.org_l_tax;
                }
            }); 
        }
        ,afterRender: function() 
        {  
            var me=this;
            if(!this.override_edit)
            {   
                this.on("edit",function(e){}); 
            }
            if(!this.override_selectionchange)                              
            {   
                this.on("selectionchange",function(sm,records)
                {   
                    if(records ==undefined || records =='undefined' || records.length==0)return false;
                    
                    var rec=records[0].raw;
                    
                    if(rec.invoice_status_id!=1/* Not draft*/)
                        Ext.getCmp("spectrumgrids_invoice_send_btn").hide();
                    else 
                    {
                        if(rec.invoice_amount!=0 )
                            Ext.getCmp("spectrumgrids_invoice_send_btn").show();
                        else 
                            Ext.getCmp("spectrumgrids_invoice_send_btn").hide();
                    }
                    
                    if(rec.invoice_status_id==2 /*paid*/|| rec.invoice_status_id==1/*draft*/)
                    {
                        Ext.getCmp("spectrumgrids_invoice_resend_btn").hide();
                        Ext.getCmp("spectrumgrids_invoice_pay_btn").hide();
                    }                                                      
                    else 
                    {
                        Ext.getCmp("spectrumgrids_invoice_resend_btn").show();
                        if(rec.invoice_master_oid== me.a_o_id)
                            Ext.getCmp("spectrumgrids_invoice_pay_btn").hide();
                        else Ext.getCmp("spectrumgrids_invoice_pay_btn").show();
                    }
                    
                    
                    if(rec.invoice_status_id==3)// pending
                        Ext.getCmp("spectrumgrids_invoice_resend_btn").show();
                    else
                        Ext.getCmp("spectrumgrids_invoice_resend_btn").hide();
                        
                    Ext.getCmp("spectrumgrids_invoice_viewitems_btn").show();
                    
                    //delete_void button
                    if(me.a_o_id==rec.invoice_master_oid)
                        Ext.getCmp("invoice_delete_void_btn").show();
                    else
                        Ext.getCmp("invoice_delete_void_btn").hide();                        
                        
                        
                        
                    ///////////////
                    var URL='';
                    if(rec.invoice_status_id==1)
                        URL='index.php/finance/json_get_applied_tax_status_invoice_draft/TOKEN:'+App.TOKEN;
                    else 
                        URL='index.php/finance/json_get_applied_tax_status_invoice/TOKEN:'+App.TOKEN;
                    var post={}
                    post["invoice_id"]=rec.invoice_id;
                    Ext.Ajax.request(
                    {
                        url     : URL, 
                        params  : post,
                        success : function(response)
                        {
                             var res=YAHOO.lang.JSON.parse(response.responseText);
                             me.applied_tax=(res.result==1)?true:false;
                        }
                    });    
                });
                
                
                this.on("itemclick",function(sm,records)
                {   
                    var rec=me.getSelectionModel().getSelection()[0].data;
                      
                    if(rec.invoice_status_id!=1)// Not draft
                        Ext.getCmp("spectrumgrids_invoice_send_btn").hide();
                    else 
                    {                            
                        if(rec.invoice_amount!=0 )
                            Ext.getCmp("spectrumgrids_invoice_send_btn").show();
                        else 
                            Ext.getCmp("spectrumgrids_invoice_send_btn").hide();
                    }
                    
                    if(rec.invoice_status_id==2 /*paid*/|| rec.invoice_status_id==1/*draft*/ )
                    {
                        Ext.getCmp("spectrumgrids_invoice_resend_btn").hide();
                        Ext.getCmp("spectrumgrids_invoice_pay_btn").hide();
                    }                                                      
                    else 
                    {
                        if(rec.invoice_,master_oid== me.a_o_id)
                        {
                            Ext.getCmp("spectrumgrids_invoice_resend_btn").show();
                            Ext.getCmp("spectrumgrids_invoice_pay_btn").hide();
                        }
                            
                        else 
                        {
                            Ext.getCmp("spectrumgrids_invoice_resend_btn").hide();
                            Ext.getCmp("spectrumgrids_invoice_pay_btn").show();
                        }
                    }
                        
                    if(rec.invoice_status_id==3)// pending
                        Ext.getCmp("spectrumgrids_invoice_resend_btn").show();
                    else
                        Ext.getCmp("spectrumgrids_invoice_resend_btn").hide();
                    
                    Ext.getCmp("spectrumgrids_invoice_viewitems_btn").show();
                    //delete_void button
                    if(me.a_o_id==rec.invoice_master_oid)
                        Ext.getCmp("invoice_delete_void_btn").show();
                    else
                        Ext.getCmp("invoice_delete_void_btn").hide();                        
                        
                        
                    ///////////////
                    var URL='';
                    if(rec.invoice_status_id==1)
                        URL='index.php/finance/json_get_applied_tax_status_invoice_draft/TOKEN:'+App.TOKEN;
                    else 
                        URL='index.php/finance/json_get_applied_tax_status_invoice/TOKEN:'+App.TOKEN;
                    var post={}
                    post["invoice_id"]=rec.invoice_id;
                    
                    Ext.Ajax.request(
                    {
                        url     : URL, 
                        params  : post,
                        success : function(response)
                        {
                             var res=YAHOO.lang.JSON.parse(response.responseText);
                             me.applied_tax=(res.result==1)?true:false;
                        }
                    });    
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
