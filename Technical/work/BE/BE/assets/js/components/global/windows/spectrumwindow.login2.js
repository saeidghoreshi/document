Ext.define('Ext.spectrumwindow.login2',    
{
        extend: 'Ext.spectrumwindow', 
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
