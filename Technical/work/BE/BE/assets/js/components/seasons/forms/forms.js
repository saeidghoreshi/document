var registrationFormsClass= function(){this.construct();};
registrationFormsClass.prototype=
{
    construct:function()
    {
                             
    },       
    form_registeredTeamsDetails:function(html)
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
                                     xtype       : 'component',
                                     html        : html,      
                                     margins     : '5 5 5 5'
                                 }
                            ]
         });                                                          
         return  form;
    },
    
    //Custom Form
    form_custom_form:function()
    {   
        
         var me=this;
         
         var form=new Ext.form.Panel(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,    
                            items: 
                            [
                                {              
                                    xtype       : 'textfield',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'field_title',
                                    id          : 'field_title',
                                    emptyText   : '',
                                    fieldLabel  : 'Custom Field title',
                                    labelWidth  : 130,
                                    width       : 100,
                                    allowBlank  : false,
                                    margins     : '0 0 0 10'
                                },
                                {  
                                    xtype       : 'combo',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'slave_org_type_id',
                                    emptyText   : '',
                                    fieldLabel  : 'Applies To',
                                    labelWidth  : 130,    
                                    forceSelection: false,
                                    editable    : false,
                                    displayField: 'name',
                                    valueField  : 'value',
                                    queryMode   : 'local',     
                                    flex        : 1,
                                    allowBlank  : false,
                                    store       : Ext.create('Ext.data.Store', {
                                         fields: ['value', 'name'],
                                         data : [
                                             {"value":"", "name":""},
                                             {"value":"8"   , "name":"Player Registration"},
                                             {"value":"6"   , "name":"Team Registration"}        
                                             ]
                                     }),
                                     margins     : '0 0 0 10'                                                                                                      
                                }
                            ]
         });                                                          
         return  form;
    }
}
var registrationForms=new registrationFormsClass();
