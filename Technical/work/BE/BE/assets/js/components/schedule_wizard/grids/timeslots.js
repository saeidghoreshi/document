 
var timeslotid='Spectrum.grids.s_timeslots';
if(!App.dom.definedExt(timeslotid))//{    
Ext.define(timeslotid,
{ 
    //extend: 'Ext.grid.Panel', 
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    
    refresh:function()
    {
    	this.getStore().load();
    	this.disableBtns(true);
 
	},
	disableBtns:function(bool)//was setDisabled_timeslots
	{
    	 var row_buttons=['btn_update_dateset','btn_delete_dateset','btn_dateset_venues','btn_dateset_dates','btn_rules_dateset'];
     	 var btn_id;
     	 for(i in row_buttons)if(row_buttons[i])
     	 {//toggle the disabled property of each
			 btn_id=row_buttons[i];
			 if(Ext.getCmp(btn_id))
				Ext.getCmp(btn_id).setDisabled(bool);			 
     	 }		
		
	},
    constructor     : function(config)
    {  
    	var timeslotModel='SchTimeslot';
    	var col_number = 45, col_time = 80;

 		config.title= 'Timeslots';
 
		config.store = Ext.create('Spectrum.store',{url:'index.php/schedule/json_datesets/'+App.TOKEN,model:timeslotModel});
 
		config.margin = '0 10 0 0';// 
		
		if(!config.plugins)config.plugins = [];
		config.plugins.push(
			Ext.create('Ext.grid.plugin.RowEditing', {clicksToEdit: 2,clicksToMoveEditor: 1,autoCancel: false})
		);
		if(!config.listeners)config.listeners = {};

		config.listeners.edit = function(e)
		{
			var post={};
			
    		post.set_name= escape(e.record.data['set_name']);
    		post.dateset_pk=e.record.data['dateset_pk'];
 
    		post.start_time= escape(Ext.Date.dateFormat(e.record.data['start_time'], 'g:i A'));
    		post.end_time  = escape(Ext.Date.dateFormat(e.record.data['end_time'],   'g:i A'));
 
			var tsRecord=e.record; 
    		Ext.Ajax.request(
    		{
				url:'index.php/schedule/post_dateset_info/'+App.TOKEN
				,scope:this
				,params:post
				,method:"POST"
    			,success:function(o)
     			 {
     	 			 var r=o.responseText;
     	 			 if(isNaN(r) || r<0 )
     	 			 {
     	 	 			 tsRecord.reject();
     	 	 			 App.error.xhr(o);
     	 			 }
     	 			 else
     	 			 {
     	 		 		tsRecord.commit();
					 }				 
     			 }
     			 ,failure:App.error.xhr
    		});
 
		};
		config.listeners.selectionchange =
		{
			scope:this
			,fn:function()
			{
    			this.disableBtns(false);
    		}
    	};
		config.columns = 
		[
		   {dataIndex:'set_name',flex:1,  text:'Name' ,editor: { allowBlank: true }  }
		   //,{dataIndex: 'hexcol',flex:1,  text:''  }//xtype: colorpicker
		   ,{dataIndex: 'start_time',width:col_time, text:'Starts' 
	       		,renderer: Ext.util.Format.dateRenderer('g:i A') 
	       		,editor:{xtype:'timefield',format:'g:i A',increment:5}
		   }
		   ,{dataIndex: 'end_time',width:col_time,  text:'Ends' 
	       		,renderer: Ext.util.Format.dateRenderer('g:i A') 
	       		,editor:{xtype:'timefield',format:'g:i A',increment:5}
		   }
		   ,{text: 'Fields',width:col_number,  dataIndex:'venue_count' }
		   ,{text: 'Days',  width:col_number,  dataIndex:'date_count'		}
		   ,{text: 'Games', width:col_number,  dataIndex:'est_games'		}
		];
		var buttons ;
		if(!config.bbar) buttons = [];
		else
		{
			buttons = config.bbar;
			config.bbar=null;	
		}
	    config.bbar =
	    [
            {
                tooltip:'Create Timeslot'
                ,iconCls:'add'
                ,scope:this
                ,handler:function()
			    {
					var window_title='Create Timeslot';
					var window_id='timeslot_window';
 					
 					if(Ext.getCmp(window_id))Ext.getCmp(window_id).destroy();
					var rmin = (o_sch_wizard ) ?o_sch_wizard.rules_global.record['len_minutes'] : 90;
					 
					 var form= Ext.create('Spectrum.form.timeslot', 
					 {
			 			 id:'timeslot_form'
			 			 ,window_id:window_id
			 			 ,title: ''
			 			 ,game_length : rmin
			 			  
					 });
		 
					var window=Ext.create('Ext.spectrumwindows', 
					{
						title: window_title
						,id:window_id
						,width: 350
						,height: 200
						,items: form  //the contents of window  are this form
					});
					  
					window.on('hide',this.refresh,this);
					window.show();
			    }
			}
		    ,{
		        tooltip:'Create using Templates'
		        ,iconCls:'application_cascade'
		        ,scope:this
		        ,handler:function()
			    {
		        	//o_sch_wizard.games.abort();
					//o_sch_wizard.timeslots.form_templates();
					var window_id='templates_window';
					var fid = 'templates_form';
 
 					if(Ext.getCmp(window_id))Ext.getCmp(window_id).destroy();
 					if(Ext.getCmp(fid))Ext.getCmp(fid).destroy();
 					
					var form= Ext.create('Schedule.form.timeslot_template', {window_id:window_id,id:fid}	);
					var window=Ext.create('Ext.spectrumwindows', 
					{
						title: 'Create Timeslots'
						,id:'templates_window'
						,id:window_id
						,width: 600
						,height: 300
						,items: form//the contents of window  are this form
					});
					window.on('hide',this.refresh,this);
					window.show();
			    }
			}
		    ,'-'
		    ,'Double click row to edit'
		    ,'->'
		    ,buttons//was config.bbar
			,'-'
		    ,{
		        tooltip:"Rules for this Dateset"
		        ,iconCls:'book_key'
		        ,id:'btn_rules_dateset'
		       // ,disabled:true
		       ,scope:this
		        ,handler:function()
			    {
					var rowsSelected = Ext.getCmp(o_sch_wizard.timeslots.grid_id).getSelectionModel().getSelection();
        			if(rowsSelected.length==0) {  return;}
        			 
					var rec=rowsSelected[0];
					var post={};
					post.dateset_pk= rec.get('dateset_pk');
					Ext.Ajax.request(
					{
						url:'index.php/schedule/json_dateset_rules/'+App.TOKEN
						,scope:this
						,params:post
						,success:function(o)
						{
							var r={};
							try{
								r = Ext.JSON.decode(o.responseText);
							}
							catch(e)
							{
								App.error.xhr(o);
								return;
							}
							var record = {ds_teardown:'0:00',ds_warmup:'0:00','is_active':true,ds_min_btw:"0:00",ds_max_btw:"0:00"};
							
							if(r.ds_teardown || r['ds_teardown'])  var record = r; //otherwise keep defaults
							 
							record.dateset_pk=post.dateset_pk;
							var window_id='sch_ts_rulesformwindow';
							 
							var r_form = Ext.create('Spectrum.form.timeslot_rules',
							{
								id:'sch_ts_rulesform'
								,record:record
								,dateset_pk:post.dateset_pk
								,window_id:window_id
							});
							 
	 						if(Ext.getCmp(window_id))Ext.getCmp(window_id).destroy();
							var window = Ext.create('Ext.spectrumwindows', 
							{
								title: 'Timeslot Rules',
								id:window_id,
								width: 300
								,height: 310, 
								items: r_form
							}); 
							window.on('hide',this.refresh,this);
							window.show();
 
						}
						,failure:App.error.xhr
					})	;	 
				}
			}
		    ,{
		        tooltip:"Edit"
		        ,iconCls:'pencil'
		        ,id:'btn_update_dateset'
		        //,disabled:true
		        ,scope:this
		        ,handler:function()
			    {
					var rowsSelected = this.getSelectionModel().getSelection();
        			if(rowsSelected.length==0) {  return;}
					var rec = rowsSelected[0]; 
					
					var window_title='Edit Timeslot';
					var window_id='timeslot_window';
        			 
 					if(Ext.getCmp(window_id))Ext.getCmp(window_id).destroy();
					var rmin = (o_sch_wizard ) ?o_sch_wizard.rules_global.record['len_minutes'] : 90;
					 
					 var form= Ext.create('Spectrum.form.timeslot', 
					 {
			 			 id:'templates_window'
			 			 ,window_id:window_id
			 			 ,title: ''
			 			 ,game_length : rmin
					 });
		 
 					if(Ext.getCmp(window_id))Ext.getCmp(window_id).destroy();
 					
					form.loadRecord(rec);
					var window=Ext.create('Ext.spectrumwindows', 
					{
						title: window_title
						,id:window_id
						,width: 350
						,height: 200
						,items: form  //the contents of window  are this form
					});
					  
					window.on('hide',this.refresh,this);
					window.show();
			    }
			}
		    ,'-'
		    ,{
		        tooltip:'Delete'
		        ,iconCls:'delete'
		        ,id:'btn_delete_dateset'
		        //,disabled:true
		        ,scope:this
		        ,handler:function()
			    {
					var rowsSelected = this.getSelectionModel().getSelection();
        			if(rowsSelected.length==0) {  return;}
					var msg ='Is it ok to delete this timeslot, and all attached fields and dates?  This may cause the number '
        					+'of created games to go down.';
        			Ext.MessageBox.confirm('Delete?',msg,function(btn_id)
					{
						if(btn_id!='ok' & btn_id!='yes') {return;}
						var post={};//'dateset_pk='+;
						post.dateset_pk=rowsSelected[0].get('dateset_pk');
						Ext.Ajax.request(
						{
							scope:this
							,params:post
							,url:'index.php/schedule/post_dateset_delete/'+App.TOKEN
							,success:function(o)
							{
					 			this.refresh();
							}
							,failure:App.error.xhr,scope:this
						});
						 
					},this);	
			    }
		    }
		];
    	this.callParent(arguments);
	}//end constructor
	
}); 