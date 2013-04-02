Ext.define('Ext.financeToolbar',
{
    extend: 'Ext.form.Panel',
    alias: 'widget.financeToolbar',
    config:null,
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor : function(config) 
    {   
        var me=this
        config.bodyStyle    = 'padding: 0px; background-color: #f2f2f2';
        config.items        = 
                            [
                                {
                                            xtype           : 'fieldcontainer',
                                            fieldLabel      : '',
                                            combineErrors   : true,
                                            msgTarget       : 'side',          
                                            labelStyle      : 'padding:0',
                                            layout          : 'hbox',        
                                            
                                            items           :
                                            [
                                                {
                                                        id      : 'Item_1'+config.comp_name+config.generator,
                                                        xtype   : 'label',
                                                        style   : 'margin-top:2px;margin-bottom:2px;margin-right:2px;margin-left:2px;color:Teal',
                                                        flex    : 1,
                                                        text    : config.Text
                                                } 
                                                ,{
                                                        id      : 'Item_2'+config.comp_name+config.generator,
                                                        xtype   : 'label',
                                                        style   : 'margin-top:2px;margin-bottom:2px;margin-right:2px;margin-left:2px;text-align:right',
                                                        flex    : 1,
                                                        text    : config.Text
                                                } 
                                            ]
                                }
                            ];
        this.config=config;   
        this.callParent(arguments); 
    }, 
    setText:function(comp_name,value1,value2)
    {
        var me=this;               
        Ext.getCmp('Item_1'+comp_name+me.config.generator).setText(value1);
        Ext.getCmp('Item_2'+comp_name+me.config.generator).setText(value2);
        //Ext.getCmp('Item_'+comp_name+me.config.generator).setValue(value);
    },
    setVisible:function(comp_name,option)
    {
        var me=this;               
        if(option==true)
            Ext.getCmp('Item_'+comp_name+me.config.generator).show();
        else
            Ext.getCmp('Item_'+comp_name+me.config.generator).hide();
    },
    
    afterRender : function()
    {   
        
        this.callParent(arguments);  
    },
    
    afterComponentLayout : function()
    {   
        this.callParent(arguments);             
    },             
    initialize:function()
    {       
        var me=this;
        //this.body.dom
    } 
});
