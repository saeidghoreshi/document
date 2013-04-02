var orgrolesModel = 'OrgRolesAllowed';//define model if it does not exist yet


var gridClass = 'Spectrum.grids.org_roles';
if(!App.dom.definedExt(gridClass)){
Ext.define(gridClass,
{
    extend: 'Ext.spectrumgrids', //base class
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    org_type:null,
    constructor     : function(config)
    {  
    	this.org_type=config.org_type;
    	
    	//first define custom paramters for spectrumgrids
		config.searchBar=false;
		config.bottomPaginator=false;
		
    	if(!config.dockedItems) config.dockedItems=[];//nothing there, but script breaks without this line
    	
		//now manage actual parameters
		
		if(!config.id)
		{//randomize id if no id given
			config.id='org-roles-grid--';
		}
		if(Ext.getCmp(config.id))//if id is given, check if it has been already used
		{
			Ext.getCmp(config.id).destroy();
		}
		config.collapsible=false;
		//Ryan Added
        var gen=Math.random();
        Ext.define('model_'+gen,{extend: 'Ext.data.Model',fields: ['role_name','role_id','assignment_id']});
        
		config.store =Ext.create( 'Ext.data.Store',
    	{
    		model       :'model_'+gen,autoDestroy:false,autoSync :false,
            autoLoad    :(config.autoLoad==null)?true:config.autoLoad,
            remoteSort  :true,
            pageSize    :(config.pageSize==null)?100:config.pageSize ,
            proxy       : 
            {   
                type        : 'rest',
                url         : (config.url==null)?'index.php/permissions/json_allowed_roles_by_org/'+App.TOKEN:config.url,
                reader      : 
                {
                        type            : 'json',
                        root            : 'root',
                        totalProperty   : 'totalCount'
                },
                extraParams :{org_type:config.org_type} 
            }    
	    });

		if(config.height==null)config.height= 270;
        if(config.title==null)config.title='Available Roles';
        
        if(config.selModel==null)config.selModel=Ext.create('Ext.selection.CheckboxModel',{mode:"MULTI"});

        config.columns=
        [
            {text   : 'Role',flex: 1,sortable : true,dataIndex: 'role_name'}
        ];

        this.callParent(arguments);
	}
	
})}