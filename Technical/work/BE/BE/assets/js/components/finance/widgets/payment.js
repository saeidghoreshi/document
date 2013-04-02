//if(!App.dom.definedExt('Ext.paymentcc')){
/*Ext.define('Ext.paymentcc',
{
    extend          :'Ext.Panel',
    alias           :'widget.ccpayment',

    config          :null,
    initComponent   :function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config) 
    {                      
        this.config=config;   
        this.callParent(arguments); 
    }, 
    afterRender : function()
    {
        alert('');
        this.initialize();      
        alert('');
        this.callParent(arguments);  
    },
    afterComponentLayout : function(w, h)
    {                                   
        this.callParent(arguments);             
    },  
    //form:null,             
    initialize:function()
    {       
        alert('');
        var me=this;
        //this.body.dom
        var config=
        {
           width   : 300,
           height  : 300,
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
                    }                          
               }
           ]
       }     
       alert('');   
       alert(YAHOO.lang.dump(financeForms.form_invoice_item_description())); 
       me.form=financeForms.form_invoice_item_description();
       alert(YAHOO.lang.dump(me.form));
    },
    getForm:function()
    {
        var me=this;
        return me.form;  
    }
}); */
//}

Ext.define('Ext.cc',
{
    extend          :'Ext.Panel',
    alias           :'widget.vv',

    config          :null,
    constructor     : function(config) 
    {                           
        this.config=config;   
        this.callParent(arguments); 
    }, 
    initComponent   :function(config) 
    {                           
        this.initialize();      
        this.callParent(arguments);
    },
    afterRender : function()
    {                           
        this.callParent(arguments);  
    },
    afterComponentLayout : function(w, h)
    {                                   
        this.callParent(arguments);             
    },             
    initialize:function()
    {       
        var me=this;
        me.form=financeForms.form_invoice_item_description();
    },
    getForm:function()
    {
        return this.form;
    }
   
              
});
