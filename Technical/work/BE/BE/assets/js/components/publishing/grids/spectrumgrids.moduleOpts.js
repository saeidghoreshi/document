if(!App.dom.definedExt('Ext.spectrumgrids.moduleOpts')){
Ext.define('Ext.spectrumgrids.moduleOpts',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
     
    ,constructor : function(config) 
    {   
        var me=this;
        if(config.columns==null)
        config.columns  =
        [    
            {
                text        : ""  ,
                dataIndex   : 'name',
                flex        :1
            }
        ];   
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        config.bottomLItems.push
        (
            "Selected Options"
        );            
        
        if(config.fields==null)     config.fields       = ['w_m_id','m_opt_id','opt_name','opt_value'];
        if(config.sorters==null)    config.sorters      = config.fields;
        if(config.pageSize==null)   config.pageSize     = 5;
        if(config.url==null)        config.url          = "";
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
        if(config.height==null)     config.height       = 300;
        if(config.groupField==null) config.groupField   = "name";
 
        
        this.config=config;
        this.callParent(arguments); 
    }                       
 
});}