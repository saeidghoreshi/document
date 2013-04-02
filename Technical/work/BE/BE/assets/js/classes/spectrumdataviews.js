if(!App.dom.definedExt('Ext.spectrumdataviews')){//workaround for IE and sometimes chrome
Ext.define('Ext.spectrumdataviews',
{
        extend: 'Ext.form.Panel', 
        initComponent: function(config) 
        {
            this.callParent(arguments);
        },
        config  :null,   
        constructor     : function(config) 
        {   
            var me=this
            
            if(config.renderTo!=null)Ext.getDom(config.renderTo).innerHTML='';  
            Ext.define('model_'+config.generator,{extend: 'Ext.data.Model',fields: config.fields});
            
            config.store= Ext.create('Ext.data.Store', 
            {   
                model       : 'model_'+config.generator,
                autoDestroy : true,
                autoSync    : true,
                autoLoad    : true,
                proxy       : 
                {
                    type        : 'rest',
                    url         : config.url,
                    extraParams : config.extraParams,
                    reader      : 
                    {
                            type            : 'json',
                            root            : 'root'
                    }
                }   
            });
            config.dataview = Ext.create('Ext.view.View', 
            {
                    store           : config.store,   
                    tpl             : config.tpl,
                    id              : 'similar',
                    itemSelector    : 'div.phone',
                    overItemCls     : 'phone-hover',
                    multiSelect     : (config.multiSelect==null)?true:config.multiSelect,
                    autoScroll      : true,
                    listeners       : config.clickselectEvent
                    
            });
            config.layout   = 'fit';
            config.items    = config.dataview;
            //added for intervals task 1445, max height for 1024 768 resolution. will do nothing if height is 
            //set to a valid number otherwise
            if(!config.height || config.height > App.MAX_GRID_HEIGHT) {config.height = App.MAX_GRID_HEIGHT;}
            
            config.width    = "100%"; 
            
            config.dockedItems =
            [
                {
                    dock: 'top',
                    xtype: 'toolbar',
                    items:config.topItems
                }
                ,{
                    dock: 'bottom',
                    xtype: 'toolbar',
                    items:[config.bottomLItems,'->',config.bottomRItems]
                }
            ]
            
            me.config=config;
            this.callParent(arguments); 
        },
        get_id_list:function()
        {
            var result=[];    
            var store=this.config.dataview.getStore();
            
            for(var i=0;i<store.data.items.length;i++)
                result.push(store.data.items[i].data.id);
            return result;
        }                                 
});}
