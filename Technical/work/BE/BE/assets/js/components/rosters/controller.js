
//.log('fff');


var rosterPersons_teamlevel= function(){this.construct();};
rosterPersons_teamlevel.prototype=
{
 
     grid               :null,
     season_list        :[],
 
     
     team_id            :null,
     season_id :null,
     season_name :null,
     
     construct:function()
     {       
         this.build_season_menu();
     }, 
     
	build_season_menu:function()
	{
		var url='index.php/season/json_seasons_assignedto_ao_team/'+App.TOKEN; 

		var callback={scope:this,failure:App.error.xhr,success:function(o)
		{
 
			var result=YAHOO.lang.JSON.parse(o.responseText);                                       
			
			var active_season_id=null;//result.active_season_id;
			var name,id,active_season_name=null;//result.active_season_name;
		     
			for(i in result)
			{
				if(!result[i].season_id) continue;
				
				name =result[i].season_name +" "+result[i].effective_range_start+" "+result[i].effective_range_end;
				id=result[i].season_id;
				//.log(result);
				if(result[i].isactive=='t')
				{
          	  		  //default value of button if found
          	  		  active_season_id=id;
					  active_season_name=name;
					 if(!this.team_id)  this.team_id=result[i].team_id;
				}
			     //build the menu for the button that we will pass to the grid
				this.season_list.push(
				{ 
					value:id, 
					text:name ,
					handler: function(o,e)
				    {
 
				         var season_name = o.text;
				         var season_id   = o.value;
				                                                            
				         Ext.getCmp('rosterPersons_teamlevel_season_btn').setText(season_name);     

				         RosterPersons_teamlevel.grid.season_id=season_id;
				         RosterPersons_teamlevel.grid.store.proxy.extraParams.season_id =season_id;
						 RosterPersons_teamlevel.grid.store.load();
				    },
					iconCls:result.iconCls
			    });
		    }// end forif 
		    //now build the grid
 
		    if(active_season_name)
		    {
				// Ext.getCmp('rosterPersons_teamlevel_season_btn').setText(active_season_name);        
				this.season_name= active_season_name;
			    this.season_id=active_season_id;
		    }

         	this.build_grid();
		   }};//end of callvback
		YAHOO.util.Connect.asyncRequest('GET',url,callback);	
							                      
     },
     build_grid:function()
     {               
     	//.log('build_grid grid with team '+this.team_id+'and season'+this.season_id);       
        
        var season_list_menu=Ext.create('Spectrum.btn_menu',{items:this.season_list});
        if(!this.season_name) this.season_name="Select a Season";
        var season_btn=
        {
            text:this.season_name
            ,id:'rosterPersons_teamlevel_season_btn'
            //,iconCls:'plugin'
            ,menu:season_list_menu
            ,width  :350
                    
        }
        

        var config=
        {
        	id:'team_level_roster',
            renderTo        : "teams_rosterperson_teamlevel",
            title           : 'Your Team Roster',
            extraParams : {season_id:this.season_id,team_id:this.team_id},
            season_id:this.season_id,
            team_id:this.team_id,
            collapsible     : false,
            hide_approve:true,//since we are a team, we do not approve or reject we only make changes
            dockedItems     :[{dock: 'top',xtype: 'toolbar',items:[ season_btn ]} ]                     

        }
         
        this.grid = Ext.create('Ext.spectrumgrids.rosterperson',config);
 
       this.grid.show();
     }                  
}

var RosterPersons_teamlevel=new rosterPersons_teamlevel();
