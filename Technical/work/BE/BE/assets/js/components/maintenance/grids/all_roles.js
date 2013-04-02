var orgrolesModel = 'OrgRolesAllowed';//define model if it does not exist yet


var gridClass = 'Spectrum.grids.all_roles';
if(!App.dom.definedExt(gridClass)){
Ext.define(gridClass,
{
    extend: 'Ext.spectrumgrids', //base class
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
 
    constructor     : function(config)
    {  
 
    	
    	//first define custom paramters for spectrumgrids
		config.searchBar=false;
		config.bottomPaginator=false;
		
    	if(!config.dockedItems) config.dockedItems=[];//nothing there, but script breaks without this line
    	
		//now manage actual parameters
		
		if(!config.id)
		{//randomize id if no id given
			config.id='all_roles_grid__';
		}
		if(Ext.getCmp(config.id))//if id is given, check if it has been already used
		{
			Ext.getCmp(config.id).destroy();
		}
		config.collapsible=false;
  
		config.store =Ext.create( 'Ext.data.Store',
    	{
    		model       :orgrolesModel
    		,autoDestroy:false, 
    		autoSync :false,
            autoLoad    :true	,
 
            proxy       : 
            {   
                type        : 'rest',
                url         : (config.url==null)?'index.php/permissions/json_getroles/'+App.TOKEN:config.url,
                reader      : 
                {
                        type            : 'json'//,
                        //root            : 'root',
                       // totalProperty   : 'totalCount'
                },
                extraParams :{org_type:config.org_type} 
            }    /*
            ,listeners:
            {
				load:{scope:this,fn:function(store,g,opt)
				{ //on load select the first row 
					//this.getSelectionModel().selectRow(0);
					this.getSelectionModel().selectRange(0,0,true);
				}}	
            }*/
	    });

		 
        if(!config.title)config.title='Roles';
        
        //if(config.selModel==null)config.selModel=Ext.create('Ext.selection.CheckboxModel',{mode:"MULTI"});

        config.columns=
        [
            {text   : 'role_id',width:30,sortable : false,dataIndex: 'role_id'},
            {text   : 'Role',flex: 1    ,sortable : false,dataIndex: 'role_name'}
        ];

        this.callParent(arguments);
	}
	
})}