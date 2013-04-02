Ext.define('Ext.clsForms',
{
        extend  : 'Ext.form.Panel', 
        alias   : 'widget.clsForms',
        initComponent: function(config) 
        {
            this.callParent(arguments);
        },
           
        constructor     : function(config)
        {   
            config.dockedItems=[]
            if(config.bottomItems!=null)
                config.dockedItems.push
                ({
                    xtype   : "toolbar",
                    dock    : "bottom",
                    items   : config.bottomItems
                });
            if(config.topItems!=null)
                config.dockedItems.push
                ({
                    xtype   : "toolbar",
                    dock    : "top",
                    items   : config.topItems
                });
            this.callParent(arguments); 
        }
});
