var fsclass = 'Spectrum.forms.teams.season';
if(!App.dom.definedExt(fsclass)){
Ext.define(fsclass,
{
    //extend: 'Ext.form.Panel', 
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    //team_id:-1,
    //team_name:'',
    constructor     : function(config)
    {  
    	if(config.window_id) this.window_id=config.window_id;
    	
    	
    	this.team_ids_array=config.team_ids_array;
		var id='seasonwindowteam_form';//+Math.random();
    	if(!config.id){config.id=id};
    	this.team_id=config.team_id;
    	//this.team_name=config.team_name;
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}

	    config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false}
	    //.log(config);
	    //nsole.log('form given team id:',config.team_id);
	    config.defaults= {anchor: '100%'};
	    config.items=
	    [
	
		    {xtype: 'hidden',name : 'team_id'}

	        ,{xtype: 'displayfield',flex : 1,name : 'team_name',fieldLabel: 'Team'}

	        ,{xtype:'grid',id:'team_season_assign_grid',height:300,
		        store:[] 
		        /*Ext.create( 'Ext.data.Store',
    			{
					remoteSort:false ,autoLoad :true,
					model:miniSeason,pageSize:100,
		            proxy: 
		            {   
		            	type: 'rest', url: 'index.php/season/json_seasons_available_forteam/TOKEN:'+App.TOKEN
		                ,reader:{type: 'json',root: 'root',totalProperty   : 'totalCount'}
		                ,extraParams:{team_id:config.team_id} 
		            }    
			    })*/
			    ,selModel:Ext.create('Ext.selection.CheckboxModel',{mode:"MULTI"})
			    ,columns:
			    [
			    	{dataIndex: 'season_name',text:'Season Name',flex:1, sortable : true}
			    	,{dataIndex: 'isactive_icon', header: 'Active', width: 60, sortable : true}
			    	,{dataIndex: 'effective_range_start'
                        , header: 'Start'
                        , sortable : true
                        , width: 100}
                    ,{dataIndex: 'effective_range_end'
                        , header: 'End'
                        , sortable : true
                        , width: 100}    
			    ]
	        
	        }
	        
	        //;
	    ];
	    config.bottomItems=
	    [
	    	'->',
 			{text   : 'Save',scope:this,handler: function() 
	        {
 
				var grid=Ext.getCmp('team_season_assign_grid');
	            var rows=grid.getSelectionModel().getSelection();
	            
	            var i,seasons=new Array();
	            //for i in has errors in IE
	            for(i=0;i<rows.length;i++)
	            {
					seasons.push(rows[i].get('season_id'));
	            }
	            if(!seasons.length)
	            {
					//error
					return;
	            }
	            var data=[];
	           // data.push('team_id='+this.team_id);
	           //array of teams, and array of seasons
	            data.push('team_ids='+YAHOO.lang.JSON.stringify(this.team_ids_array));
	            data.push("season_ids="+YAHOO.lang.JSON.stringify(seasons));
	            var post = data.join("&");
	            
	            
	            var url='index.php/teams/post_assign_team_to_seasons/'+App.TOKEN;
	            var callback={scope:this,failure:App.error.xhr,success:function(o)
	            {
					if(Ext.getCmp(this.window_id)) Ext.getCmp(this.window_id).hide();
	            }};
	            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
	            
	            return;

	        }}        
	    ];

	    	
        this.callParent(arguments);    	
        this.get_seasons(config.team_id);
	}//end constructor
	,get_seasons:function(team_id)
	{
		var url='index.php/season/json_seasons_available_forteam/TOKEN:'+App.TOKEN;
		
		var post='team_id='+team_id;
		var callback={scope:this,failure:App.error.xhr,success:function(o)
		{
			var s=o.responseText;
			Ext.getCmp('team_season_assign_grid').store.loadData(YAHOO.lang.JSON.parse(s));
			
		}};
		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
		
	}
	
});
}
