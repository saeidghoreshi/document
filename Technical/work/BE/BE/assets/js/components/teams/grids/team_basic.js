//used by seasons drag and drop component  LEFT HAND SIDE for assignments

var gridTeamsClsss = 'Ext.spectrumgrids.team';
if(!App.dom.definedExt(gridTeamsClsss)){
	Ext.define(gridTeamsClsss,    
	{
        extend: 'Ext.spectrumgrids', 
        initComponent: function(config) 
        {       
            this.callParent(arguments);
        }
        ,constructor : function(config) 
        {   
            var me=this;
            config.columns= 
            [
                {
                    dataIndex   : 'team_name'
                    , header    : 'Team Name'
                    , sortable  : true
                    ,flex       :1
                }              
            ];
            config.dockedItems =
            [
                {
                    dock: 'top',
                    xtype: 'toolbar',
                    items:[]
                } 
                //Bottom Action toolbar
                ,{
	                dock: 'bottom',
	                xtype: 'toolbar',
	                items: []
                } 
            ];     
            if(config.fields==null)     config.fields       = ['team_id','team_name'];
            if(config.sorters==null)    config.sorters      = ['team_id','team_name'];
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = '';
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 300;

            
            this.config=config;
            this.callParent(arguments); 
        }                       
        

});
}
