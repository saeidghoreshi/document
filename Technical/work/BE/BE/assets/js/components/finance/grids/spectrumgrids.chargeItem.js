Ext.define('Ext.spectrumgrids.chargeItem',    
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
                    text        : "Charge Code"
                    ,dataIndex  : "type_descr"
                    ,width      : 200
                }
                ,{
                    text        : "Description"
                    ,dataIndex  : "type_name"
                    ,flex       : 1
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
                //Generate New charge Item
                 {
                        iconCls :'add',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Add Charge Type',
                        handler : function()
                        {
                            var _config=
                            {
                                width   : 300,
                                height  : 200,
                                bottomItems:   
                                [
                                    '->'
                                    ,{   
                                         xtype   :"button",
                                         text    :"Save",
                                         iconCls :'table_save',
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
                                                     url     : 'index.php/finance/json_new_charge_item/TOKEN:'+App.TOKEN,
                                                     waitMsg : 'Processing ...',
                                                     params  : post,
                                                     success : function(form, action)
                                                     {
                                                         var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                         if(res.result!="-1")
                                                         {
                                                             win.Hide();
                                                             Ext.MessageBox.alert({title:"Status",msg:"Charge Item Saved Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
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
                            var form        =financeForms.form_chargeitems();
                            var final_form  =new Ext.spectrumforms.mixer(_config,[form],['form']);
                            var win_cnf     =
                            {
                                title       : 'Charge Items Management',
                                final_form  : final_form
                            }
                            var win=new Ext.spectrumwindow.finance(win_cnf);
                            win.show();
                        }
                 } 
            );       
            config.bottomRItems.push
            (   
                //delete charge Type
                {   
                    xtype   :"button",
                    text    :"",
                    iconCls :'delete',
                    tooltip :'Delete',
                    handler :function()
                    {  
                       if(me.getSelectionModel().getSelection().length==0)
                       {
                           Ext.MessageBox.alert({title:"Error",msg:"Please select A Charge Type", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                           return;
                       }
                       var record=me.getSelectionModel().getSelection()[0].data;
                       var post={}
                       post["charge_type_id"]=record.type_id;
                       
                       var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                       Ext.Ajax.request(
                       {
                           url     : 'index.php/finance/json_delete_charge_type/TOKEN:'+App.TOKEN, 
                           params  : post,
                           success : function(response)
                           {
                                box.hide();
                                var res=YAHOO.lang.JSON.parse(response.responseText);
                                if(res.result=="1")
                                {                           
                                    Ext.MessageBox.alert({title:"Status",msg:"Charge Type Deleted successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                    me.getStore().load();   
                                }
                           }
                       });    
                    }                          
               }
            );   
            
            if(config.fields==null)     config.fields       = ['type_id','type_name','type_descr'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = "/index.php/finance/json_get_charge_types/TOKEN:"+App.TOKEN+"/";
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
             
            if(config.groupField==null) config.groupField   = "";
            
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
                this.on("selectionchange",function(sm,records){});                             
                this.on("itemclick",function(sm,records){}); 
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
