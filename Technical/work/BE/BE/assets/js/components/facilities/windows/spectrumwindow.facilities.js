Ext.define('Ext.spectrumwindow.facilities',    
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