if(!App.dom.definedExt('Ext.spectrumgrids.limits')){
	Ext.define('Ext.spectrumgrids.limits',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    },
 
    constructor : function(config) 
    {   
        var me=this;
        config.columns  =
        [    
            {
                text        : ""  ,
                dataIndex   : 'name',
                flex        :1
            }
        ];
        
        
        
        
        
        if(config.fields==null)     config.fields       = ['id','name','checked'];
        if(config.sorters==null)    config.sorters      = ['id','name','checked'];
        if(config.pageSize==null)   config.pageSize     = 5;
        if(config.url==null)        config.url          = "";
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
 
        if(config.groupField==null) config.groupField   = "name";
        
        this.override_edit          =config.override_edit;
        this.override_selectiochange=config.override_selectionchange;
        this.override_itemdblclick  =config.override_itemdblclick;
        this.override_collapse      =config.override_collapse;
        this.override_expand        =config.override_expand;
        
        this.config=config;
        this.callParent(arguments); 
    }                       
 
});}