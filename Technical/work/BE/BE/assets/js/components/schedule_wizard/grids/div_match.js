var gclass='Spectrum.grids.s_matches';
if(!App.dom.definedExt(gclass))//{    
Ext.define(gclass,
{ 
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    
    refresh:function()
    {
    	this.getStore().load();
 
	},
 
	constructor     : function(config)
    {  		
    	var model_id='WizardMatches';
		config.collapsible=false;
		config.margins='0 0 0 0';
		config.store =config.store= Ext.create('Spectrum.store',{url:'index.php/schedule/json_matches/'+App.TOKEN,model:model_id});
 
		config.width="100%";
		config.height= 300;
		  //  ,stateful: true
		if(!config.plugins)config.plugins=new Array();
		config.plugins.push( 
			Ext.create('Ext.grid.plugin.RowEditing', {clicksToEdit: 2,clicksToMoveEditor: 1,autoCancel: false})
		);
		
		config.columns=
		[
		   {dataIndex: 'first_div_name',flex:1,    text:'Division'  }
		   ,{dataIndex: 'f_total_teams',width:60,  text:'# Teams'  }
		   
		   ,{dataIndex: 'second_div_name',flex:1,  text:'vs. Division'  }
		   ,{dataIndex: 's_total_teams',width:60,  text:'# Teams'  }
		   ,{dataIndex:'games_per_round',width:90, text:'Games / Round'}
		   ,{dataIndex: 'match_rounds', width:120, text:'# Rounds',
				    editor: { xtype:'numberfield',minValue:0,allowBlank: true} 
			}
			,{dataIndex:'enforce_rounds',width:120, text:'Enforce Rounds',renderer:o_sch_wizard.formatBool,//return value =='t' ? 'Yes' : 'No';
				editor: {xtype: 'combobox', typeAhead: true,triggerAction: 'all',selectOnTab: true,
		              store: [['t','Yes'],['f','No']]}
			}
			,{dataIndex: 'date_count',    width:120, text:'# Dates'}
			,{dataIndex: 'enforce_dates',    width:120,  text:'Enforce Dates',renderer:o_sch_wizard.formatBool,//return value =='t' ? 'Yes' : 'No';
				editor: {xtype: 'combobox', typeAhead: true,triggerAction: 'all',selectOnTab: true,
		              store: [['t','Yes'],['f','No']]}
			}
			,{dataIndex:'est_games',width:80,text:"# Games"}
		];
		var buttons=[];
		if(config.bbar)
		{
			//splice buttons into the middle
			buttons=config.bbar;
			config.bbar=null;
		}
		config.bbar=
		[
		    {
		    	iconCls:'add'
		    	,tooltip:'Add new match'
		    	,id:'btn_match_add'
		    	,scope:this
		    	,handler:function(o)
		    	{
		    		
	    			var window_id="matches_window";
 
					if(Ext.getCmp(window_id))
					{
						Ext.getCmp(window_id).destroy();
					}
					//Spectrum.form.div_match
					var form= Ext.create('Spectrum.form.div_match', 
					{
						id:'matches_form'
						,window_id:window_id
						,title: ''
					});
					var window=Ext.create('Ext.spectrumwindows', 
					{
						title: 'Create a Division Match'
						,id:window_id
		 
						,width: 700
						,height: 400
		 
						,items: form//the contents of window  are this form
					});
					window.on('hide',this.refresh,this);
					window.show();
		    	}
		    }
		    ,'->'
		    ,buttons
		    ,'-'
		    ,{
		    	iconCls:"delete"
		    	,tooltip:"Delete Match"
		    	//,disabled:true
		    	,id:'btn_match_delete'
		    	,scope:this
		    	,handler:function(o)
		    	{
		    		var rows=this.getSelectionModel().getSelection();
		    		if(!rows.length){return;}
					var msg ='Is it ok to delete this match, and all attached rules and dates?  This may cause the number '
        					+'of created games to go down.';
        			Ext.MessageBox.confirm('Delete?',msg,function(btn_id)
					{
						if(btn_id!='ok' & btn_id!='yes') {return;}
						//o_sch_wizard.matches.match_pk= ;
						var post={};
						post.match_pk=rows[0].get('match_pk');
						 
						Ext.Ajax.request({
							url:'index.php/schedule/post_delete_match/'+App.TOKEN
							,scope:this
							,method:"POST"
							,params:post
							,success:function(o)
							{
								//o_sch_wizard.matches.get();
								this.refresh();
							}
							,failure:App.error.xhr
						});
					},this);	
				}
			}
		];
		if(!config.listeners)config.listeners={};
		config.listeners.edit={scope:this,fn:function(e)
		{
			e.record.commit(); 
 			var post={};
    		post.match_pk        =e.record.data['match_pk'];
    		post.enforce_dates   =e.record.data['enforce_dates'];
    		post.enforce_rounds  =e.record.data['enforce_rounds']; 
    		post.match_rounds    = e.record.data['match_rounds'];
    		  
    		Ext.Ajax.request(
    		{
    			url:'index.php/schedule/post_update_match/'+App.TOKEN
    			,success:function(o)
     			 {
     	 			// var r=o.responseText;			  
					 this.refresh();
     			 }
     			 ,failure:App.error.xhr
     			 ,scope:this
     			 ,params:post
    		});
 
		}}
    	this.callParent(arguments);
	}//end constructor
	
}); 		
 
 