if(!App.dom.definedExt('Ext.spectrumgrids.photoGallery')){
Ext.define('Ext.spectrumgrids.photoGallery',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    },
    
    config      :null,
    
    constructor : function(config) 
    {   
        var me  =this;
        
        config.columns      =
        [    
            {
                text        : '',
                dataIndex   : 'thumbnail',
                width       : 100
            },
            {
                text        : 'Content Name',
                dataIndex   : 'content_name',
                width       : 100
            },
            {
                text        : 'Type',
                dataIndex   : 'content_type_name',
                width       : 100
            },
            {
                text        : 'Last Action',
                dataIndex   : 'content_action_name',
                width       : 100
            },
            {
                text        : 'Content Status',
                dataIndex   : 'content_status_name',
                width       : 100
            },
            { 
                text        : 'Content Description',
                xtype       : 'templatecolumn',
                tpl         : '<div style="margin:10px;padding:10px">{content_description}</div>',
                flex        : 1 
            },
            { 
                text        : 'Action History Description',
                xtype       : 'templatecolumn',
                tpl         : '<div style="margin:10px;padding:10px">{action_history_description}</div>',
                flex        : 1 
            }    
            
        ];
        //Fresh top/bottom components list
        if(!config.topItems)     config.topItems=[];
        if(!config.bottomLItems) config.bottomLItems=[];
        if(!config.bottomRItems) config.bottomRItems=[];
               
               
        
        config.topItems.push
        ( 
            
        );
        config.bottomLItems.push
        (
            //DONE
            
        );
        
        config.bottomRItems.push
        (
            
        );
        
        config.statusStore.on("load",function()
        {
                config.statusStore.add
                (
                    {"type_id":false,"type_name":"All"}
                ); 
        });
        
        //Get Organizational Info
        
                                                           
        if(config.fields==null)     config.fields       = ['acc_id','content_action_name','content_status_id','content_status_name','action_history_description','content_id','content_name','file_name','owned_by','content_description','content_type_id','content_type_name','thumbnail'];
        if(config.sorters==null)    config.sorters      = config.fields;
        if(config.pageSize==null)   config.pageSize     = 10;
        if(config.url==null)        config.url          = "index.php/contentgallery/json_get_photoGalleries/TOKEN:"+App.TOKEN;
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
        if(config.height==null)     config.height       = "100%";
        
        this.callParent(arguments);  
     },
     
     afterRender: function() 
     {  
            var me=this;
             
            
            if(!me.override_edit)
            {   
                me.on("edit",function(e)
                {
                    var record=e.record.data;
                }); 
            }      
            this.callParent(arguments);         
     }
});
}




