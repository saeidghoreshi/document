Ext.define('Ext.spectrumgrids.invoiceItem',    
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
            config.columns  =
            [    
                {
                    text        : "Item"
                    ,dataIndex  : "charge_type_name"
                    ,flex       : 1
                }
                ,{
                    text        : "Qty"
                    ,xtype      :'templatecolumn'
                    ,tpl        :'<div style="text-align:right;font-weight:bold;color:black;">{quantity}</div>'
                    ,width      : 60
                }
                ,{
                    text        : "Rate"
                    ,xtype      :'templatecolumn'
                    ,tpl        :'<div style="text-align:right;font-weight:bold;color:black;">{charge_price}</div>'
                    ,width      : 60
                }
                ,{
                    text        : "Amount"
                    ,xtype      :'templatecolumn'
                    ,tpl        :'<div style="text-align:right;font-weight:bold;color:black;">{sub_amount}</div>'
                    ,width      : 60
                }
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            config.topItems.push
            (                
                 'Items List'
            );
            config.bottomLItems.push
            (
                 //Add new Items
                 {
                        id      : 'invoice_items_add_btn',
                        iconCls : 'add',
                        xtype   : 'button',
                        text    : 'Add Item',
                        pressed : true,
                        tooltip : 'Add New Item',
                        handler : function()
                        {
                            var chargeTypeStore=new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_charge_types/TOKEN:"+App.TOKEN+'/',{test:true});                          
                            var _config=
                            {
                                width   : 400,
                                height  : 200,
                                bottomItems:   
                                [
                                    '->'
                                    ,{   
                                         xtype   :"button",
                                         text    :"Add Item",
                                         iconCls :'table_save',
                                         pressed :true,
                                         width   :70,
                                         tooltip :'Add Item',
                                         handler :function()
                                         {   
                                             var values=form.getForm().getValues();
                                             if(isNaN(parseInt(values["quantity"])))
                                             {
                                                 Ext.MessageBox.alert({title:"Error",msg:"Quantity Must be a digit", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                 return;
                                             }
                                             if(isNaN(parseFloat(values["charge_price"])))
                                             {
                                                 Ext.MessageBox.alert({title:"Error",msg:"Charge Price Must be in Money Format", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                 return;
                                             }
                                             
                                             if(isNaN(parseFloat(values["charge_cost"])) && values["charge_cost"]!='0')
                                             {
                                                Ext.MessageBox.alert({title:"Error",msg:"Charge Cost Must be in Money Format", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                return;
                                             }
                                                   
                                             var record=me.owner.getSelectionModel().getSelection()[0].data;                  
                                             if (form.getForm().isValid()) 
                                             {   
                                                 values["tax_applies"]     =(Ext.getCmp("invoice_item_apply_taxes_checkbox").checked==true)?1:0;
                                                 
                                                 
                                                 var URL='';
                                                 
                                                 if(config.invoice_status_id==1)
                                                 {
                                                     values["invoice_id"]   =me.owner.getSelectionModel().getSelection()[0].data.invoice_draft_id;
                                                     URL='index.php/finance/json_add_invoice_item_draft/TOKEN:'+App.TOKEN;
                                                 }
                                                    
                                                 else 
                                                 {
                                                     values["invoice_id"]   =me.owner.getSelectionModel().getSelection()[0].data.invoice_id;
                                                     URL='index.php/finance/json_add_invoice_item/TOKEN:'+App.TOKEN;
                                                 }
                                                              
                                                 
                                                 var post=form.getForm().getValues();
                                                 Ext.Ajax.request(
                                                 {
                                                    url     : URL,
                                                    params  : values,
                                                    success : function(response)
                                                    {
                                                         var res=YAHOO.lang.JSON.parse(response.responseText);
                                                         if(res.result!="-1")
                                                         {   
                                                             me.getStore().load();  
                                                             me.reloadInvoiceGrid();
                                                             win.Hide();
                                                         }
                                                    }
                                                 });
                                             }   
                                         }                          
                                    }
                                ]
                            } 
                            var form=financeForms.form_invoice_items(chargeTypeStore);
                            var final_form=new Ext.spectrumforms.mixer(_config,[form],['form']);
                            
                            var win_cnf=
                            {
                                title       : 'Add New Item',
                                final_form  : final_form
                            }
                            var win=new Ext.spectrumwindow.finance(win_cnf);
                            win.show();
                        }
                 },
                 //Delete Items
                 {
                        id      : 'invoice_items_del_btn',
                        iconCls : 'delete',
                        xtype   : 'button',
                        text    : 'Delete Selected Item',
                        pressed : true,
                        tooltip : 'Delete Selected Item',
                        handler : function()
                        {
                            if(me.getSelectionModel().getSelection().length==0)
                            {
                                Ext.MessageBox.alert({title:"Error",msg:"Please select an Item", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            var record=me.getSelectionModel().getSelection()[0].data;
                            var post={}
                            post["invoice_item_id"] =record.invoice_item_id;
                            post["tax_applies"]     =(Ext.getCmp("invoice_item_apply_taxes_checkbox").checked==true)?1:0;
                            
                            var URL='';                
                            if(config.invoice_status_id==1)
                               URL='index.php/finance/json_delete_invoice_item_draft/TOKEN:'+App.TOKEN;
                            else 
                               URL='index.php/finance/json_delete_invoice_item/TOKEN:'+App.TOKEN;
                            
                            Ext.Ajax.request(
                            {
                                url     : URL,
                                params  : post,
                                success : function(response)
                                {
                                     var res=YAHOO.lang.JSON.parse(response.responseText);
                                     if(res.result=="1")
                                     {   
                                         me.getStore().load();  
                                         me.reloadInvoiceGrid();
                                         
                                         /*var x=function()
                                         {                                                               
                                            if(typeof (Ext.getCmp('invoice_item_apply_taxes_checkbox')) != 'undefined')
                                                me.ApplyTaxes(Ext.getCmp('invoice_item_apply_taxes_checkbox').checked);
                                            me.getStore().removeListener('load', x);
                                         }
                                         me.getStore().on("load",x);
                                         */
                                     }
                                     if(res.result=="-1")
                                        Ext.MessageBox.alert({title:"Error",msg:"Total Paid Is More Than Invoice", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                }
                            });
                        }
                 }     
            );   
            config.bottomRItems.push
            (
            );
               
            var URL='';
            if(config.invoice_status_id==1)
                URL="/index.php/finance/json_get_invoice_items_draft/TOKEN:"+App.TOKEN;
            else
                URL="/index.php/finance/json_get_invoice_items/TOKEN:"+App.TOKEN;
            
            //config.autoLoad             =false;
            if(config.fields==null)     config.fields       = ['invoice_item_id','invoice_id','charge_type_id','charge_type_name','invoice_item_description','charge_price','sub_amount','charge_cost','quantity','isactive','created_by','created_by_name','created_on_display'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = URL;
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 300;
            if(config.groupField==null) config.groupField   = "";
            
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
            
            var x=function()
            {                                                               
                if(typeof (Ext.getCmp('invoice_item_apply_taxes_checkbox')) != 'undefined')
                    me.ApplyTaxes(Ext.getCmp('invoice_item_apply_taxes_checkbox').checked);
                me.getStore().removeListener('load', x);
                //me.getStore().events['load'].clearListeners();--it clear all [not good]
            }
            me.getStore().on("load",x);
            
                 
                                        
            if(!this.override_edit)
            {   
                this.on("edit",function(e)
                {
                    var record=e.record.data;
                    
                    var invoice_item_id =record.invoice_item_id;
                    var charge_price    =record.charge_price;
                    var charge_cost     =record.charge_cost;
                    var quantity        =record.quantity;
                       
                    if(isNaN(parseFloat(charge_price)))
                    {
                        Ext.MessageBox.alert({title:"Error",msg:"Charge Price Need to be a Numeric Amount", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        e.record.reject();   
                        return;
                    }
                    if(isNaN(parseFloat(charge_cost)))
                    {
                        Ext.MessageBox.alert({title:"Error",msg:"Charge Cost Need to be a Numeric Amount", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        e.record.reject();   
                        return;
                    }
                    if(isNaN(parseInt(quantity)))
                    {
                        Ext.MessageBox.alert({title:"Error",msg:"Quantity Need to be a Integer Value", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        e.record.reject();   
                        return;
                    }
                    
                    
                    e.record.commit();   
                    var URL='';
                    if(me.config.invoice_status_id==1)//Draft
                        URL='index.php/finance/json_update_draftinvoice_item/TOKEN:'+App.TOKEN;
                    else 
                        URL='index.php/finance/json_update_invoice_item/TOKEN:'+App.TOKEN;                            
                        
                    var post={}
                    post["invoice_item_id"] =invoice_item_id;
                    post["charge_price"]    =charge_price;
                    post["charge_cost"]     =charge_cost;
                    post["quantity"]        =quantity;
                    
                    var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                    Ext.Ajax.request(
                    {
                       url     : URL,
                       params  : post,
                       success : function(response)
                       {
                           box.hide();
                           var res=YAHOO.lang.JSON.parse(response.responseText);
                           //Reload Invoice Grid
                           me.reloadInvoiceGrid();
                       }
                    });    
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
        },
        ApplyTaxes:function(checkboxOption)
        {       
                var me=this;                                                                    
                
                var sum=0;
                var Items=me.getStore().data.items;
                for(var i =0 ;i<Items.length;i++)
                     sum+=(Items[i].data.charge_price*Items[i].data.quantity);
                     
                var invoiceRecord   =me.owner.getSelectionModel().getSelection()[0].data;
                
                var currency    =" ("+invoiceRecord.currency_type_name+")";
                if(checkboxOption==true && me.config.a_o_tax_info !=null)
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
                    
                    for(var i=0;i<me.config.a_o_tax_info.length;i++)
                    {
                        content+='<tr>'
                            +'<td style="text-align:left;vertical-align:top;width:130">'
                            +me.config.a_o_tax_info[i].label+currency+' :'
                            +'</td>'
                            +'<td style="text-align:right;vertical-align:top;width:70">'
                            +Math.round(sum*parseFloat(me.config.a_o_tax_info[i].rate*100))/100
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
                    //    xtype       : 'financeToolbar',
                    //  dock        : 'bottom',
                    //Text        : 'row2',
                    //generator   : 'row2',
                    //comp_name   : 'row2',
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
                    +'$ '+Math.round(sum*100)/100
                    +'</td>'
                    +'</tr>'
                    
                    +'</table>'
                    
                    
                    Ext.getCmp("invoice_item_tax_panel").setValue(content);
                }   
                                
        },
        getTotalTax:function()  
        {
            var me=this;
            var totalTax=0;
            if(Ext.getCmp('invoice_item_apply_taxes_checkbox').checked==true)
            {
                for(var i=0;i<me.config.a_o_tax_info.length;i++)
                    totalTax+=parseFloat(me.config.a_o_tax_info[i].rate)
                return totalTax;
            }   
            else return 0;
        },
        reloadInvoiceGrid:function()
        {
            var me      =this;
            var record  =me.owner.getSelectionModel().getSelection()[0].data;
            me.owner.getStore().load();   
            
            var x=function()
            {   
                var recIndex;
                if(me.config.invoice_status_id==1)                                              
                    recIndex=me.owner.getStore().find( "invoice_draft_id", record.invoice_draft_id, 0, true, false, false);    
                else
                    recIndex=me.owner.getStore().find( "invoice_id", record.invoice_id, 0, true, false, false);    
                    
                me.owner.getSelectionModel().select( recIndex, true, false); 
                
                //Apply Tax @ InvoiceItem Screen                                                            
                if(typeof (Ext.getCmp('invoice_item_apply_taxes_checkbox')) != 'undefined')
                    me.ApplyTaxes(Ext.getCmp('invoice_item_apply_taxes_checkbox').checked);
                me.owner.getStore().removeListener('load', x);
            }
            me.owner.getStore().on("load",x);
        }
});
