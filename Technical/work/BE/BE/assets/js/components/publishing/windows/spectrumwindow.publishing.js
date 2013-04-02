Ext.define('Ext.spectrumwindow.publishing',    
{
    extend: 'Ext.spectrumwindows', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    },              
    config                 :null,
    constructor : function(config) 
    {                 
        var me=this;
        
        
       this.config=config;
        this.callParent(arguments); 
    }
        
});
