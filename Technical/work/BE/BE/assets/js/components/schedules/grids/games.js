var formatDate=function(value)
 {
     if(value=='')return'';
	 return value ? Ext.Date.dateFormat(value, 'M d, Y') : null;
 }
var model_id='ScheduledGame';
//else console.info(model_id+" EXISTS");
    
    
 var games_grid_class =  'Spectrum.grids.games';
 if(!App.dom.definedExt(games_grid_class)){
Ext.define(games_grid_class,
{
    //extend: 'Ext.grid.Panel', 
    extend:'Ext.spectrumgrids',
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
	season_id:-1,
	schedule_id:-1,
	refresh:function()
	{

		this.store.proxy.extraParams.schedule_id=this.schedule_id;
		this.store.proxy.extraParams.season_id  =this.season_id;//not used?
		this.store.loadPage(1);
	},
    constructor     : function(config)
    { 
    	config.rowEditable=true;
		config.searchBar=false;
		config.bottomPaginator=true;
		
    	if(config.schedule_id)
        	this.schedule_id=config.schedule_id;
        if(config.season_id)
        	this.season_id=config.season_id;//){season_id=config.season_id;}
        

        
		 //.log('games.grid  given sch,'+schedule_id+' season  '+season_id);
		if(!config.id) config.id='games_grid_id_';//default id that can overwrite
 
        
        if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
        
        
	    
		if(!config.title){config.title='Scheduled Games';}

		if(typeof config.collapsible == 'undefined') config.collapsible= true;
		/*config.store= Ext.create('Spectrum.store',
		{
			url:'index.php/schedule/json_games_scores/'+App.TOKEN
			,model:model_id
			,paginator:true
			, extraParams:{season_id:this.season_id,schedule_id:this.schedule_id}
		});
*/
	    config.store =Ext.create( 'Ext.data.Store',
    	{
    		model:model_id,autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:false,pageSize:100 ,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/games/json_games_scores/'+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{season_id:this.season_id,schedule_id:this.schedule_id}
            }    

	    });
		config.width="100%";
		 
	    config.listeners=
	    {
	        selectionchange: function(sm, rows) 
    		{
    			if(!rows.length)return;
    			var row=rows[0];
    			//o_schedules.row_game=row;
    			
    			var hs=row.get('home_score');
    			var as=row.get('away_score');

    			if(hs!=='' || as!=='')
    			{
					//.log('one of the scores exists, so do not allow reschedule.');
					Ext.getCmp('btn_bbar_resc').setDisabled(true);	
					Ext.getCmp('btn_bbar_swapteams').setDisabled(true);	
					Ext.getCmp('btn_bbar_clear').setDisabled(false);	
					//o_schedules.setDisabled_games(true);//disable btns
					
    			}
    			else
    			{
					//.log(' scores exists, so do not allow reschedule.');
    				//scores exist!!! alow erase
					Ext.getCmp('btn_bbar_resc').setDisabled(false);	
					Ext.getCmp('btn_bbar_swapteams').setDisabled(false);	
					Ext.getCmp('btn_bbar_clear').setDisabled(true);	
    			}
			}
			,edit:function(e)
			{
				var hs = e.record.data['home_score'];
				var as = e.record.data['away_score'];
				var id = e.record.data['game_id'];

				var url='index.php/statistics/post_valid_score/'+App.TOKEN;
				if(  hs===''||  isNaN(hs) ||as==='' || isNaN(as))
				{
					e.record.reject();
					return;
				}
				var post='game_id='+id+"&home_score="+hs+"&away_score="+as;
				
				var callback={failure:App.error.xhr,success:function(o)
    			{	
    				 var r=o.responseText;
   
					 e.record.commit(); 
     	 			   			
    			}};
				YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
			}
		};//end of listenres
		config.columns=
	    [
        	{text     : 'Home',flex:1,sortable : true,dataIndex: 'home_name'}	
			,{text:'Score',sortable : true,width:60,	dataIndex: 'home_score',
			    editor: { allowBlank: true ,maskRe:/\d/ ,validateOnBlur:true  }
					
			}	
			,{text     : 'Away',flex:1,sortable : true,dataIndex: 'away_name'    }	   
			,{text:'Score',sortable : true,width:60,	dataIndex: 'away_score',
			    editor: { allowBlank: true ,maskRe:/\d/ ,validateOnBlur:true  }
			}	 
			,{text     : 'Location',flex:1,		           sortable : true,dataIndex: 'venue_name'    }	 
			,{text     : 'Date',width:120,		           sortable : true,dataIndex: 'game_date'  
							,renderer: Ext.util.Format.dateRenderer('D M j, Y')   }
			,{text     : 'Time',width:70,	               sortable : true,dataIndex: 'start_time'	    }	 
		];
			
		        ////////////////buttons 
		if(!config.dockedItems)config.dockedItems=new Array();
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items://tbar
	        [
	        	//filters go here if any
	        ]
		});
		if(config.bbar)
			var buttons=config.bbar;	
		else var buttons=new Array();
		config.bbar=null;//otherwise we get two copies
		config.dockedItems.push(
		{dock: 'bottom',xtype: 'toolbar',
			items://bbar
	        [
				{
					tooltip:'Add New Games',
					id:'btn_sch_addgames',
					scope:this,
					iconCls:'add',
					handler:function()
	                { 
	                	var f=Ext.create('Spectrum.forms.game.create',{
	                		schedule_id:this.schedule_id
	                		,season_id :this.season_id });
	                	var win=Ext.create('Spectrum.windows.game.create',{items:f});

						win.on('hide',function(o)
						{
							//get the grid  using the grid id, which is stored in a hidden field
							//var g=Ext.getCmp(Ext.getCmp('_games_grid_this_cmp_id_').getValue());
							if(typeof this.refresh == 'function')
							{
								this.refresh();
							}
						},this);
						win.show();
					}
				}
				,'-'
				,'Double click a row to add or modify scores'
				,'->'
				,buttons//all user input buttons
				
				
				
				,{
					id:'btn_bbar_swapteams',
					scope:this,
					disabled:true,
					iconCls:'arrow_refresh',
					text:'',
					tooltip:'Swap Home and Away',
						handler:function()
					{
						var rowsSelected = this.getSelectionModel().getSelection();
						if(!rowsSelected.length){return;}//in case no rows selected
						
						var msg='This may cause the number of home and away games for each teams to become more unbalanced. ';
						Ext.MessageBox.show({title:'Swap Teams',icon: Ext.MessageBox.QUESTION,msg:msg,scope:this,buttons: Ext.Msg.YESNO,fn:function(btn_id)
						{
							if(btn_id !='yes' && btn_id != 'ok') {return;}
							var rowsSelected = this.getSelectionModel().getSelection();
							var row_game=rowsSelected[0];
							var post='game_id='+row_game.get('game_id');
							var url='index.php/games/post_swap_teams/'+App.TOKEN;
							YAHOO.util.Connect.asyncRequest('POST',url,{failure:App.error.xhr,scope:this,success:function(o)
							{
								var r=o.responseText;

								this.refresh();
							}},post);
							
						
						}});
					}
				}
				,{
					id:'btn_bbar_clear',
					scope:this,
					disabled:true,
					iconCls:'cross',
					text:'',
					tooltip:'Delete the score for this game',
					handler:function()
					{
						//var rowsSelected = Ext.getCmp(Ext.getCmp('_games_grid_this_cmp_id_').getValue()).getSelectionModel().getSelection();
						var rowsSelected = this.getSelectionModel().getSelection();
						if(!rowsSelected.length){return;}//in case no rows selected
						var msg='This will delete the score for this game, and update the standings.';
						Ext.MessageBox.show({title:'Delete these scores?',icon: Ext.MessageBox.QUESTION,msg:msg,scope:this,buttons: Ext.Msg.YESNO,fn:function(btn_id)
						{
							if(btn_id !='yes' && btn_id != 'ok') {return;}
							var rowsSelected = this.getSelectionModel().getSelection();
							var row_game=rowsSelected[0];
							var post='game_id='+row_game.get('game_id');
							var url='index.php/statistics/post_delete_valid_scores/'+App.TOKEN;
							YAHOO.util.Connect.asyncRequest('POST',url,{failure:App.error.xhr,scope:this,success:function(o)
							{
								var r=o.responseText;
								try
								{
									r=YAHOO.lang.JSON.parse(r);
									this.refresh();
								}
								catch(e)
								{
									App.error.xhr(o);//do not have permsision
								}

								
							}},post);
						}});
					}
				}
				,"-"
				,{
					id:'btn_bbar_resc',
					scope:this,
					disabled:true,
					iconCls:'date',
					text:'',
					tooltip:'Reschedule or cancel this game',	
					handler:function()
					{
						var window_id='window_reschedule_';
						var form=Ext.create('Spectrum.forms.game.edit',{window_id:window_id});
 
						var rowsSelected = this.getSelectionModel().getSelection();
						if(!rowsSelected.length){return;}//in case no rows selected
						var row_game=rowsSelected[0];
						
						form.loadRecord(row_game);
						var win=Ext.create('Spectrum.windows.game.edit',{items:form,id:window_id});

						
						win.on('hide',function(o)
						{	
							//refresh the data, if a refresh functoin was defined
 
							if(typeof this.refresh == 'function')
							{
								this.refresh();
							}
						},this);
						win.show();
					}
				}
			]
		});//end of bbar

        this.callParent(arguments);
	}
	
	
}); }
