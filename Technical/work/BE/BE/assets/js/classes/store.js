/**
* 
* use paginator:true if paginator is being used, this takes care of root and totalCount for you, pageSize defaults to 100
* paginator:false for regular grids without paginators
* 
* Only Required Params: url and model. model must be valid, no error checking here
* 
* 
*/
if(  !App.dom.definedExt('Spectrum.store'))//dont define it twice
{
Ext.define('Spectrum.store',
{                            
    extend  :'Ext.data.Store', 
 
    initComponent: function(config) {this.callParent(arguments);}, 
    
    constructor     : function(config)
    {    
    
    	if(config.paginator)
    	{
    		var reader ={type: 'json',root: 'root',totalProperty: 'totalCount'};
    		if(!config.pageSize)config.pageSize=100;
		}
		else
		{
			//if paginator is not being used
			var reader={type: 'json'};
		}
 
		if(!config.extraParams)config.extraParams={};
    	config.autoDestroy=false;
    	config.autoSync =false;
	    config.autoLoad =true;
	    config.remoteSort=false; 
	    config.proxy=
	    {   
	        type: 'rest'//required
	        ,url:config.url//constructor url with no error checking
		    ,noCache:false//defaulttrue: this must be false
		    //it forces the _dc= into the right spot so it wont  cause 404 errors
	        ,reader: reader// determiend dynamicaly by paginator flag above
	        ,extraParams:config.extraParams//input params if any
	    };
    	

    	    	
    	this.callParent(arguments); 
	}
    
    
}

);
	
}
	