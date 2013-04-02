

var fclass = 'Spectrum.forms.teams.create';
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
    	this.grid_id=config.grid_id;
    	this.season_id=config.season_id;
		var id='cteam_form';//+Math.random();
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
	        ,{xtype: 'textfield',flex : 1,id:'createteam_new_team_name',name : 'team_name',fieldLabel: 'New Team Name',allowBlank: false}

	        	
	        ,{xtype:'grid',collapsible: true,title:'Has this team played in a previous season?',id : 'dg_team_search',store:[],height: 270,
				listeners:
				{
					selectionchange: function(sm, selectedRecord) 
    				{
						if (selectedRecord.length) 
						{
		    				var team_id=selectedRecord[0].get('team_id');
		    				
		    				Ext.getCmp('field_team_id').setValue(team_id);
		    				if(Ext.getCmp('cb_use_team').checked)//if use this team then load the name
		    					{Ext.getCmp('createteam_new_team_name').setValue(selectedRecord[0].get('team_name'));}
					    }
					}
				}
				,columns: 
				[
					{text   : 'Team',flex: 1,sortable : true,dataIndex: 'team_name'}
					,{text   : '',flex: 1,sortable : true,hidden:true,dataIndex: 'team_id'}
				]
				,tbar:
				[
					{xtype:'button',menu:[],id:'btn_team_seasons',text:'Select a season'} 
					,'->'
					,{xtype:'checkbox',fieldLabel:'Use this team',name:'use_team',id:'cb_use_team',checked:false}
				]

			}//end of grid

	    ];
	    if(!config.dockedItems) config.dockedItems=new Array();
	    var savebtn={
 				xtype:'spectrumbutton',
 				text   : 'Save',
 				iconCls:'disk',
 				scope:this,
 				handler: function() 
		        {

		            if(!Ext.getCmp('cb_use_team').checked )
		            {

						//if no, then unselect the team internaly. do not use selected team
						Ext.getCmp('field_team_id').setValue(-1);
		            }//otherwise, well, team id is saved there alraeady, from teh listeners.selectionchange event
		            
		            var form = this.getForm();

		            if (!form.isValid()) {return;}
		                                     
		            var data=[];
		            Ext.iterate(form.getValues(), function(key, value) 
		            {
						if(key=='team_name')
							value=escape(value);
		                data.push(key+"="+value);
		            }, this);
		            data.push('season_id='+this.season_id);
		            var post = data.join("&");
		            
		            
		            //will create, or change name
		            var url='index.php/teams/post_create/'+App.TOKEN;
		            var callback={scope:this,failure:App.error.xhr,success:function(o)
		            {
		                var r=o.responseText;
						if(isNaN(r))
						{
							//if not a number
							App.error.xhr(o);
						}
						else if (r<=0)
						{
							//if is a number. to stop the '200:OK' boxes showing up 
							Ext.MessageBox.show({
								title:'Unknown Error #'+r,
								msg:'Please try again, or use the "Submit a bug" button',
								icon:Ext.MessageBox.INFO
								,buttons:Ext.MessageBox.OK});

						
						}
						else
						{
							Ext.MessageBox.show({title:'Success',msg:'Team created',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK,
							fn:function()
							{						
								var new_text='';
								var delay_ms=10;
								Ext.getCmp('createteam_new_team_name').focus(new_text,delay_ms);//when they confirm, sernd focus back to textbox for usability
								//set text to blank, and delay by ten ms
								
							}});
							var grid=Ext.getCmp(this.grid_id);
							if(grid) grid.refresh();
							
						}
						
		            }};

		            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);

		        }
		            
	        };
	    //http://docs.sencha.com/ext-js/4-0/#!/api/Ext.panel.Panel-cfg-buttons
	    
	   // config.bottomItems=[savebtn];
	    
	     config.dockedItems.push(
	    {
	    	xtype:'toolbar',
	    	dock:'bottom',
	    	ui:'footer',
	    	items:
	    	[
	    	 { xtype: 'component', flex: 1 },//to make other buttons float ->
 			 savebtn
 			 ]       
		});

	    	
        this.callParent(arguments);    	
        //now build seasons menu
        var season_url='index.php/season/json_active_league_seasons/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',season_url,{scope:this,success:function(o)
		{
			var name,icon, id,seasons=YAHOO.lang.JSON.parse(o.responseText);
			//this.seasons_menu=new Array();	
			seasons=seasons['root'];
			var seasons_filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;

        		Ext.getCmp('btn_team_seasons').setText(name);
        		//.log(id);

				var post="season_id="+id;

				var callback={success:function(o)//o_teams.display_teams
				{
				    var json=YAHOO.lang.JSON.parse(o.responseText);
					if( typeof json['root'] != 'undefined' ) json=json['root'];//get aroid paginator stuff that we dont need
					
					Ext.getCmp('dg_team_search').store.loadData(json,false);//show teams in grid
				},
				failure:App.error.xhr};
				var url='index.php/teams/json_season_teams/'+App.TOKEN;

				YAHOO.util.Connect.asyncRequest("POST",url,callback,post);
			};
			for(i in seasons)
			{
				name=seasons[i]['season_name']+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				id  =seasons[i]['season_id'];
				icon='';
				if(seasons[i]['isactive']=='t')
				{
					icon='tick';
					foundActive={text:name,value:id};
				}
				else
				{
					icon='cross';
				}
        		seasons_filter.push({text:name,value:id,handler:itemClick,iconCls:icon});
			}
			Ext.getCmp('btn_team_seasons').menu=Ext.create('Spectrum.btn_menu',{items:seasons_filter});

		}});    	
	}

});
}
