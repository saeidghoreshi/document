var maintenanceFormsClass= function(){this.construct();};
maintenanceFormsClass.prototype=
{
    construct:function()
    {
                             
    },       
    formMenuItem    :function(menuItemType,menuWindowType,saveNewWCHandler,saveNewELHandler)
    {   
         var me=this;
         var form=new Ext.form.Panel(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults : 
                            {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },    
                            items:
                            [
                                //Row 1
                                {
                                            xtype           : 'fieldcontainer',
                                            fieldLabel      : '',
                                            combineErrors   : true,
                                            msgTarget       : 'side',          
                                            labelStyle      : 'font-weight:bold;padding:0',
                                            layout          : 'hbox',        
                                            fieldDefaults   : {labelAlign: 'top'},
                                            items: 
                                            [            
                                                {
                                                    xtype       : 'textfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'menu_label',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Label',
                                                    labelWidth  : 100,
                                                    width       : 185,
                                                    allowBlank  : false
                                                },
                                                {
                                                    xtype       : 'textfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'menu_image',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Image',
                                                    labelWidth  : 100,
                                                    width       : 185,
                                                    allowBlank  : true,
                                                    margin      : '0 0 0 5'
                                                }
                                            ]
                                },
                                //Row 2
                                {
                                            xtype           : 'fieldcontainer',
                                            fieldLabel      : '',
                                            combineErrors   : true,
                                            msgTarget       : 'side',          
                                            labelStyle      : 'font-weight:bold;padding:0',
                                            layout          : 'hbox',        
                                            fieldDefaults   : {labelAlign: 'top'},
                                            items: 
                                            [            
                                                {
                                                    xtype       : 'numberfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'menu_group',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Group',
                                                    labelWidth  : 100,
                                                    width       : 100,
                                                    allowBlank  : true
                                                },
                                                {
                                                    xtype       : 'checkboxfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'menu_default',
                                                    fieldLabel  : 'Default',
                                                    labelWidth  : 100,
                                                    margin      : '0 0 0 5'
                                                },
                                                {
                                                    xtype       : 'numberfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'menu_rowspan',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Row Span',
                                                    labelWidth  : 100,
                                                    width       : 100,
                                                    allowBlank  : true,
                                                    margin      : '0 0 0 5'
                                                },
                                                {
                                                    xtype       : 'numberfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'menu_colspan',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Col Span',
                                                    labelWidth  : 100,
                                                    width       : 100,
                                                    allowBlank  : true,
                                                    margin      : '0 0 0 5'
                                                }
                                                
                                            ]
                                },
                                //Row 3
                                {
                                            xtype           : 'fieldcontainer',
                                            fieldLabel      : '',
                                            combineErrors   : true,
                                            msgTarget       : 'side',          
                                            labelStyle      : 'font-weight:bold;padding:0',
                                            layout          : 'hbox',        
                                            fieldDefaults   : {labelAlign: 'top'},
                                            items: 
                                            [            
                                                {  
                                                    xtype       : 'combo',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'menu_type_id',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Type',
                                                    labelWidth  : 150,    
                                                    forceSelection: false,
                                                    editable    : false,
                                                    displayField: 'type_name',
                                                    valueField  : 'type_id',
                                                    queryMode   : 'local',     
                                                    flex        : 1,
                                                    allowBlank  : false,
                                                    store       : menuItemType,
                                                    listeners   :
                                                    {
                                                        buffer  :100,
                                                        change  :function(obj,selectedId)
                                                        {
                                                            if(selectedId==1)
                                                            {
                                                                Ext.getCmp("define_new_w_c").show();
                                                                Ext.getCmp("define_new_e_l").hide();
                                                                return;
                                                            }                                       
                                                            if(selectedId==2)
                                                            {
                                                                Ext.getCmp("define_new_w_c").hide();
                                                                Ext.getCmp("define_new_e_l").show();
                                                                return;
                                                            }   
                                                            Ext.getCmp("define_new_w_c").hide();
                                                            Ext.getCmp("define_new_e_l").hide();
                                                            
                                                        }
                                                    }
                                                }
                                            ]
                                },
                                //Row 4  Window::Controller Define New
                                {
                                    id          : 'define_new_w_c',
                                    xtype       : 'fieldset',
                                    title       : 'Controller::Window',
                                    collapsible : false,
                                    margin      : '3 3 3 3 ',
                                    hidden      :true,
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
                                                 id          : 'new_old_cb',
                                                 name        : 'new_old_cb' ,
                                                 xtype       : 'checkbox',
                                                 fieldLabel  : 'Create New Controller::Window ?',
                                                 checked     : false,
                                                 width       : 200,
                                                 listeners   : 
                                                 {
                                                     change: 
                                                     {
                                                        buffer:1,
                                                        fn: function(a,checked)
                                                        {
                                                            if(checked==true)
                                                            {
                                                                Ext.getCmp("window_controller").setDisabled(false);
                                                                Ext.getCmp("window_method").setDisabled(false);
                                                                Ext.getCmp("window_type_id").setDisabled(true);
                                                            }
                                                            else
                                                            {
                                                                Ext.getCmp("window_controller").setDisabled(true);
                                                                Ext.getCmp("window_method").setDisabled(true);
                                                                Ext.getCmp("window_type_id").setDisabled(false);                                                                
                                                            }   
                                                        }
                                                     }       
                                                 }
                                            },
                                            {  
                                                id          : 'window_type_id',
                                                xtype       : 'combo',
                                                labelStyle  : 'font-weight:bold;padding:0',
                                                name        : 'window_type_id',
                                                emptyText   : '',
                                                fieldLabel  : 'Window',
                                                labelWidth  : 150,    
                                                forceSelection: false,
                                                editable    : false,
                                                displayField: 'type_name',
                                                valueField  : 'type_id',
                                                queryMode   : 'local',     
                                                flex        : 1,
                                                allowBlank  : true,
                                                store       : menuWindowType
                                            },
                                            {
                                                id          : 'window_controller',
                                                xtype       : 'textfield',
                                                labelStyle  : 'font-weight:bold;padding:0',
                                                name        : 'window_controller',
                                                emptyText   : '',
                                                fieldLabel  : 'Define New Controller',
                                                labelWidth  : 100,
                                                flex        :1,
                                                disabled    : true,
                                                allowBlank  : false
                                            },
                                            {
                                                id          : 'window_method',
                                                xtype       : 'textfield',
                                                labelStyle  : 'font-weight:bold;padding:0',
                                                name        : 'window_method',
                                                emptyText   : '',
                                                fieldLabel  : 'Define New Method',
                                                labelWidth  : 100,
                                                flex        :1,
                                                disabled    : true,
                                                allowBlank  : false
                                            },
                                            {
                                                 id         : 'save_new_wc_btn',   
                                                 xtype      : 'button',
                                                 iconCls    : 'table_save',
                                                 text       : 'Save',
                                                 tooltip    : 'Save New Controller::Method',
                                                 handler    : saveNewWCHandler
                                            }
                                    ]
                                },
                                //Row 5  External Link Define New
                                {
                                    id          : 'define_new_e_l',
                                    xtype       : 'fieldset',
                                    title       : 'ExternalLink',
                                    collapsible : false,
                                    hidden      :true,
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
                                                    xtype       : 'textfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'link_href',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Define New External Link',
                                                    labelWidth  : 100,
                                                    flex        :1,
                                                    allowBlank  : false
                                                },
                                                {
                                                     xtype      : 'button',
                                                     iconCls    : 'table_save',
                                                     text       : 'Save',
                                                     tooltip    : 'Save New External Link',
                                                     handler    : saveNewELHandler
                                                 }
                                    ]
                                }
                            ]
         }
         );                                                        
         return  form;
    },
    formNewMenuItem :function()
    {   
         var me=this;
         var form=new Ext.form.Panel(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            //bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults : 
                            {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
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
                                            items: 
                                            [            
                                                {
                                                    xtype       : 'textfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'menu_label',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Item Title',
                                                    labelWidth  : 100,
                                                    flex        :1,
                                                    allowBlank  : true
                                                }
                                            ]
                                }
                            ]
         }
         );                                                        
         return  form;
    }
}
var maintenanceForms=new maintenanceFormsClass();
