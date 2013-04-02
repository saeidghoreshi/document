

var fclass = 'Spectrum.forms.teams_managers';
if(!App.dom.definedExt(fclass)){
Ext.define(fclass,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    
    constructor     : function(config)
    {  
    	//this.grid_id=config.grid_id;
    	this.team_id=config.team_id;
		var id='cteammanagers_form';//+Math.random();
    	if(!config.id){config.id=id;};
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		 config.bodyPadding=10;
		 config.width=600;
		 //{id:id,title: '',autoHeight: true,resizable:false,bodyPadding: 10,width: 600,//height:250,
	     config.fieldDefaults={labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
	     config.defaults= {anchor: '100%'};
	     config.items=
	     [
		    {xtype: 'hidden',name : 'team_id',id:'field_team_id',value:-1}
	        ,{xtype: 'hidden',flex : 1,name : 'team_name',fieldLabel: 'Team'}
	      , {xtype:'grid',id : 'team_mg_grid',//store:[]
		            store:Ext.create( 'Ext.data.Store',{remoteSort:false,autoDestroy: false,loadMask:false,model:'User',data: []})
								//,proxy: {type: 'localstorage'}} )
		            ,height: 200,width:'100%',title:'Team Managers',loadMask:false,
		            columns: 
		            [
		                {text   : 'First Name',flex: 1,sortable : true,dataIndex: 'person_fname'}
		                ,{text   : 'Last Name',flex: 1,sortable : true,dataIndex: 'person_lname'}
		                //,{text   : 'Birthdate',sortable : true,dataIndex: 'person_birthdate'}
		                //,{text   : 'Email',flex: 1,    sortable : true,dataIndex: 'email'}
		            ],
		            listeners:
		            {
						selectionchange:{scope:this,fn: function(sm, selectedRecords) 
    					{
    						 
							if (!selectedRecords.length) {return;} 
							this.loadRecord(selectedRecords[0]);
						}}
 
					}
 
	        }
	        ,{xtype: 'displayfield',flex : 1,name : 'email',fieldLabel: 'Email'}
			,{xtype: 'displayfield',flex : 1,name : 'address_street',fieldLabel: 'Postal'}
			,{xtype: 'displayfield',flex : 1,name : 'postal_value',fieldLabel: 'Postal'}
			,{xtype: 'displayfield',flex : 1,name : 'address_city',fieldLabel: 'City'}
			,{xtype: 'displayfield',flex : 1,name : 'country_abbr',fieldLabel: 'Country'}
			,{xtype: 'displayfield',flex : 1,name : 'home_display',fieldLabel: 'H Phone'}
	        ,{xtype: 'displayfield',flex : 1,name : 'mobile_display',fieldLabel: 'M Phone'}
	        ,{xtype: 'displayfield',flex : 1,name : 'work_display',fieldLabel: 'W Phone'}
	        	        	

	        	        	

	    ];

	    	
        this.callParent(arguments); 
      	this.get_managers();  
    },
    get_managers:function()
    {   	
 			//.log('json_team_managers_andcontact');
        //now build seasons menu
        var url='index.php/teams/json_team_managers_andcontact/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,failure:App.error.xhr,success:function(o)
		{
			var tm=YAHOO.lang.JSON.parse(o.responseText);
			Ext.getCmp('team_mg_grid').getStore().loadData(tm);
			
			if(tm.length)
				Ext.getCmp('team_mg_grid').getSelectionModel().select(0,false,false) ;
 
		}},"team_id="+this.team_id);    	
	}
	
 

});
}
