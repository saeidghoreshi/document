var gfc = 'Spectrum.forms.game.create';
if(!App.dom.definedExt(gfc)){
Ext.define(gfc,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
       
    constructor     : function(config)
    {   

		if(!config.id) config.id='game_form';
		
     	 if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}

		var schedule_id=-1;
		if(config.schedule_id){schedule_id=config.schedule_id;}
		var season_id=-1;
		if(config.season_id){season_id=config.season_id;}
		 //.log('create game form given sch,'+schedule_id+' season  '+season_id);


		 config.layout= 'column';   // Specifies that the items will now be arranged in columns
		 config.fieldDefaults= {labelAlign: 'left',msgTarget: 'side'};
		 config.defaults=     {anchor: '100%'};
		 config.items=		//form on thelefft
		 [
			{
				columnWidth: 0.5
				,margin: '0 0 0 4'
				,xtype: 'fieldset'
				,title:'Details'
				,defaults: {width: 300,labelWidth: 60}
				,defaultType: 'textfield'
	            ,items: 
	            [
	                //hidden data items. some have values based on config input
					{  xtype:'textfield',hidden:true,value:-1,	name:'schedule_id',	id:'form_g_schedule_id' ,value:schedule_id	}
					,{ xtype:'textfield',hidden:true,value:-1,	name:'season_id',	id:'form_g_season_id'	,value:season_id}
					,{ xtype:'textfield',hidden:true,value:-1,	name:'home_id',		id:'form_g_home_id'	}
					,{ xtype:'textfield',hidden:true,value:-1,	name:'away_id',		id:'form_g_away_id'	}
					,{ xtype:'textfield',hidden:true,value:-1,	name:'venue_id',	id:'form_g_venue_id'	}
					,{ xtype:'textfield',hidden:true,value:-1,	name:'search_date',	id:'form_g_search_date'	}
					,{ xtype:'displayfield',hidden:false,fieldLabel:'Schedule',name:'schedule_name',  	id:'form_g_schedule_name'	}
					,{ xtype:'button',iconCls:'bullet_arrow_down',id:'fg_home_btn',menu:null,text:"Home Team"} 
					//this will be a dropdown, menu loaded later
					,{ xtype:'button',iconCls:'bullet_arrow_down',id:'fg_away_btn',menu:null,text:"Away Team"}
					
					,{ xtype:'button',iconCls:'bullet_arrow_down',id:'fg_venue_btn',menu:null,text:"Select a Venue"}
				
					,{ xtype:'datefield',name:'game_date',format:'Y/m/d',allowBlank:false,fieldLabel:"Date",
		    			listeners:
		    			{
		    				select:{scope:this,fn: function(combo, value)
							{
								var d=value ? Ext.Date.dateFormat(value, 'Y-m-d') : null;
								Ext.getCmp('form_g_search_date').setValue(d);
								if(d)//if not null
									this.getCurrentGames();
								
	
							}}
	                    }
					}
					,{ xtype:'timefield',name:'start_time',value:'06:00 PM',increment:5,allowBlank:false,fieldLabel:"Start"}
					,{ xtype:'timefield',name:'end_time'  ,value:'08:00 PM',increment:5,allowBlank:false,fieldLabel:"End"}
					,{ xtype:'button',id:'find_venue_btn',iconCls:'map',hidden:true,text:"View more venues (disabled)"
							,scope:this,handler:function(o,e)
					{
						alert('TODO:find more venues');
					}}	
					,{ xtype:'button',iconCls:'disk',id:'fg_form_save',text:"Save",scope:this,handler:function(o,e)
					{

						var form = this.getForm();
 
	        			var form_data=new Array();
	        			var error=false;
					    Ext.iterate(form.getValues(), function(key, value) 
					    {
					    	if(value==-1)
					    	{
 
					    		error=true;
					    		 
					    	}
					        form_data.push(key+"="+escape(value));
							
					    }, this);
					    if(error)//since isValid just messes up the entire layout
					    {
					    	Ext.MessageBox.show({
								title:'Form Incomplete'
								,msg:'Enter all required fields'
								,icon:Ext.MessageBox.WARNING,
								buttons:Ext.MessageBox.OK});
							
							return;
					    	return;
					    }
						var post= form_data.join('&');
							
						var url='index.php/games/post_create/'+App.TOKEN;
						 
						var callback=
						{
							scope:this,
							failure:App.error.xhr,
							success:function(o)
							{
								var r=o.responseText;
								
								if(isNaN(r)||r<=0)
								{
									Ext.MessageBox.show({title:'Could not schedule this game.',msg:r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
								}
								else
								{
									Ext.MessageBox.show({title:'Success',msg:'Game created. ',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK});								
									this.getCurrentGames();//and show the games on RHS
								}
							}
						};
						YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
						
						
					}}	
				]
			
			}
			
			,{//table on right
            columnWidth: 0.50,xtype: 'gridpanel',store: [],height: 400,title:'Games by Date',id:'fg_games_by_date',
            columns: 
            [
                {text   : 'Home',flex: 1,sortable  : true,dataIndex: 'home_name'}
                ,{text  : 'Away',flex: 1,sortable  : true,dataIndex: 'away_name'}
                ,{text  : 'On',flex: 1,sortable    : true,dataIndex: 'venue_name'}
                ,{text  : 'Time',width:50,sortable : true,dataIndex: 'start_time'}
             ]   
			}//end of grid veritcal panel

		]//end of field vertical

		this.callParent(arguments); 
		this.buildMenus(schedule_id,season_id);
    }
    ,getCurrentGames:function()
    {
		var d = Ext.getCmp('form_g_search_date').getValue();
		var id =Ext.getCmp('form_g_venue_id').getValue();	
					
		if(!d||d==-1){return;}
		
 
		var post="venue_id="+id+"&search_date="+d;
		var callback={success:function(o)
		{
			var g=YAHOO.lang.JSON.parse(o.responseText);
			Ext.getCmp('fg_games_by_date').store.loadData(g,false);
			Ext.getCmp('fg_games_by_date').setTitle("Games on selected venue and "+d);
		}};
		var url='index.php/games/json_games_by_venue_date/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
 
    }
	,buildMenus:function(schedule_id,season_id)    
    {
		 //load venues
		 var post='schedule_id='+schedule_id;
		 var url='index.php/schedule/json_schedule_venues_fac/'+App.TOKEN;
		 
		 YAHOO.util.Connect.asyncRequest('POST',url,{
		 	 scope:this,
		 	 failure:App.error.xhr,
		 	 success:function(o)
			 {
				 var i,v,venues=YAHOO.lang.JSON.parse(o.responseText);
				 
				var menu_venfac=new Array();
				for(i in venues)
				{
					v=venues[i];
					var comboName= v['facility_name']+" : "+v['venue_name']; 
					var vid=v['venue_id'];
					menu_venfac.push({text:comboName,value:vid,scope:this,handler:function(o,e)
					{
						//menu click
						var name = o.text;
						var id   = o.value;
						var btn=Ext.getCmp('fg_venue_btn');
						btn.setText(name);
						Ext.getCmp('form_g_venue_id').setValue(id);
						
						this.getCurrentGames();
						

					}});
					
				}

				var v_btn=Ext.getCmp('fg_venue_btn');//btn_org_select_form	
				v_btn.setDisabled(false);	
				v_btn.menu=Ext.create('Spectrum.btn_menu',{items:menu_venfac });	
 
			 }},post);

		 //now load the teams buttons
		 
		 var team_post='season_id='+season_id;
		 //get all teams registered for this season
		 var team_url='index.php/teams/json_season_teams/'+App.TOKEN;
		 YAHOO.util.Connect.asyncRequest('POST',team_url,{
		 	 scope:this,
		 	 failure:App.error.xhr,
		 	 
		 	 success:function(o)
			 {
				 var i,t,teams=YAHOO.lang.JSON.parse(o.responseText);
				 if(teams['root'])teams=teams['root'];//needed to skip over paginator data,since this is not a datagrid
				 var home_menu=new Array();
				 var name,id,away_menu=new Array();
				 //create two menus 
				 for(i in teams)
				 {
					 t=teams[i];
					 id=t['team_id'];
					 name=t['team_name'];
					 home_menu.push({text:name,value:id,scope:this ,handler:function(o,e)
					 {
						var name = o.text;
						var id   = o.value;
						Ext.getCmp('fg_home_btn').setText(name);						
						Ext.getCmp('form_g_home_id').setValue(id);
					 }});
					 away_menu.push({text:name,value:id,scope:this, handler:function(o,e)
					 {
						var name = o.text;
						var id   = o.value;
						Ext.getCmp('fg_away_btn').setText(name);						
						Ext.getCmp('form_g_away_id').setValue(id);
					 }});
					 
				 }
				 //load menus into buttonsfg_home_btn
				var h_btn=Ext.getCmp('fg_home_btn');//
				h_btn.setDisabled(false);	
				h_btn.menu=Ext.create('Spectrum.btn_menu',{   items:home_menu });	
 
				 
				var a_btn=Ext.getCmp('fg_away_btn');//	
				a_btn.setDisabled(false);	
				a_btn.menu=Ext.create('Spectrum.btn_menu',{   items:away_menu });	
				
			 }}, team_post );
	}
});
}


