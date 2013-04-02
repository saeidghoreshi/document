//used by seasons drag and drop component RIGHT HAND SIDE 

var groupedTeam = 'Ext.spectrumgrids.teamdivision';
if(!App.dom.definedExt(groupedTeam)){

	Ext.define(groupedTeam,    
	{
        extend: 'Ext.spectrumgrids', 
        initComponent: function(config) 
        {       
            this.callParent(arguments);
        }
        
        ,config                 :null
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
            if(config.fields==null)     config.fields       = ['team_id','team_name', 'division_id','division_name','division_fullname'];
            if(config.sorters==null)    config.sorters      = ['team_id','team_name', 'division_id','division_name','division_fullname'];
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = '';
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
 
            if(config.groupField==null) config.groupField   = "division_fullname";

            this.callParent(arguments); 
        }                       

	});
}