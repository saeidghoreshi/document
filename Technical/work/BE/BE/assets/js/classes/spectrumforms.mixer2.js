Ext.define('Ext.spectrumforms.mixer2',
{
        extend: 'Ext.spectrumforms', 
        initComponent: function(config) 
        {
            this.callParent(arguments);
        },
           
        constructor     : function(config,form_list)
        {                            
            config.border       = false;
            config.layout       = "border";
            //config.frame         =true;
            
            if(config.bodyStyle==null)
                config.bodyStyle    = 'padding: 0px; background-color: #DFE8F6';
            
            if(config.width==null)config.ptop="50%";
            if(config.height==null)config.pbottom="50%";
            if(form_list.length==2)
            {
                config.items    =
                [   
                    {
                        bodyStyle   : 'padding: 0px; background-color: #DFE8F6',
                        region      : 'south',
                        height      : config.pbottom,
                        split       : true,
                        collapsible : false,
                        animCollapse: false,
                        border      : false,
                        items       : form_list[1]
                    }                                   
                    ,{
                        region      : 'center',
                        layout      : 'fit',
                        border      : false,
                        width       : config.ptop,
                        autoHeight  : true,
                        items       : form_list[0]
                    }
                    
                ];    
            }
            this.callParent(arguments); 
        }
});
