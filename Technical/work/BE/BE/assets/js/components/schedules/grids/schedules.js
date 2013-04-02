
//	else console.info(model_name+" Exists");	
  var model_name = 'Schedules';
formatBoolYN=function(value)
{
	 return value =='t' ? 'Yes' : 'No';
}
 
var gclass='Spectrum.grids.schedules';
if(!App.dom.definedExt(gclass)){ 
Ext.define(gclass,
{
    //extend: 'Ext.grid.Panel', 
    extend:'Ext.spectrumgrids',
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    refresh:function()
    {
    	this.store.loadPage(1);	
    }
    ,constructor     : function(config)
    {   		
		//three custom spectrumgrids params:
		
		config.rowEditable=true;
		config.searchBar=true;
		config.bottomPaginator=true;
		
		if(!config.id) config.id='schedule-manage-grid';

		if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
	
		
		////////////////buttons 
		if(!config.dockedItems)config.dockedItems=new Array();
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items:
	        [
	        	
	        	//filters go here if any
	        ]
		});
		
		var btn_rainout={tooltip:'Rainout Games',disabled:true,
			id:'btn_sch_rainout',scope:this,iconCls:'weather_rain',handler:function()
	        {
	        	
	        	//var grid_id=Ext.getCmp('_schedul_grid_id_').getValue();
				//var rowsSelected = Ext.getCmp(grid_id).getSelectionModel().getSelection();
				var rowsSelected = this.getSelectionModel().getSelection();
	            if(!rowsSelected.length){return;}
	            if(Ext.getCmp('rainout_form'))						
					{Ext.getCmp('rainout_form').destroy()};
									
				var window_id='rainout_window_';
				var form=Ext.create('Schedule.form.rainout',{id:'rainout_form',window_id:window_id});
				form.loadRecord(rowsSelected[0]);//load data BEFORE rendering in the form
			
				var win=Ext.create('Schedule.windows.rainout',{items:form,id:window_id});
	            
	            win.show();
	            win.on('hide',this.refresh,this);//reload data on hide
			}
		}
				
				
				
		var btn_del={tooltip:'Delete',disabled:true,id:'btn_sch_delete',scope:this,iconCls:'delete',handler:function()
	        {
	        	//var grid_id=Ext.getCmp('_schedul_grid_id_').getValue();
	           // alert('cannot delete if publisdhed, fix this');return;
	            //this.display_rosters(null);
                var rowsSelected = this.getSelectionModel().getSelection();
 
        		if(rowsSelected.length==0)
        		{  
        			//alert("error not selected selected");
        			return;    
				}
				
				
				var pub=rowsSelected[0].get('is_published');
				//cannot delete published schedule
				if(pub=='t'||pub=='true'||pub===true)
				{
					Ext.MessageBox.alert('Error',"Cannot delete a published schedule");
					return;
				}
				var msg='Delete this schedule?  Any and all attached games, game results, and statistics may also be deleted. ';
				Ext.MessageBox.show({title:"Delete?",msg:msg,buttons: Ext.Msg.YESNO,icon: Ext.MessageBox.QUESTION,scope:this,fn:function(btn_id)
				{
					if(btn_id!='ok'&&btn_id!='yes'){return;}//if no do nothing
					var rowsSelected = this.getSelectionModel().getSelection();//get again incase scope lost the local variable
					var post='schedule_id='+ rowsSelected[0].get('schedule_id');
					 var callback={scope:this,failure:App.error.xhr,success:function(o)
	                {
					    if(typeof this.refresh == 'function')
						{
							this.refresh();
						}
					}};
					var url='index.php/schedule/post_delete/'+App.TOKEN;
					YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
				}});

               
			}//end handler
			
		};
		
		var input_bbar=[];
		if(config.bbar){input_bbar=config.bbar;}
		config.bbar=null;
		
       var bbar=new Array();
       
       bbar.push(
       {xtype:'button',tooltip:"Import CSV",iconCls:'page_white_excel',scope:this,hidden:true,handler:function()
       {
		   var csvwindow= Ext.create('Ext.spectrumwindows',
		   {
		   	   title:'CSV schedule.  In beta for admin use',
			  items:
			  [
				  {xtype:'form',id:'_csv_schedule_form_',items:[
				  {xtype:'displayfield',value:'home_id, away_id, venue_id, date, starttime endtime'}
				  ,{xtype:'textfield',name:'name',fieldLabel:'schedule name',allowBlank:true}
				  ,{xtype:'textfield',name:'season_id',fieldLabel:'season_id',allowBlank:false}
				  ,{xtype:'textarea',name:'csv',fieldLabel:'paste csv',allowBlank:false}
				  ,{xtype:'button',iconCls:'disk',scope:this,handler:function()
				  {
				  	var form=Ext.getCmp('_csv_schedule_form_');//.getForm();

					  var url='index.php/schedule/import_schedule_csv/'+App.TOKEN;
					  
					  form.submit(
					  {
					  	  url:url
						  ,failure : function(f, action){App.error.xhr(action.response);	}
						  ,success : function(f,a){alert('done');}
					  })
					  
					  
				  }}
				  ] 

				  
				  }
			  ]
		   });
		   csvwindow.on('hide',this.refresh,this);
		   csvwindow.show();
       }});
       
       //bbar.push("Double click a row to edit");
       bbar.push('->');

       
	   for(i in input_bbar) 
       {
       	   if(input_bbar[i] && input_bbar[i].iconCls)//added for IE
		   		bbar.push(input_bbar[i]);//push input buttons: example: view games button
       } 
       
       bbar.push(btn_rainout);	
       
       bbar.push("-");
       bbar.push(btn_del);	
       bbar.push({xtype:'hidden',value:id,id:'_schedul_grid_id_'});
       
	   config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items: bbar});
	   
		////////////
		
		if(!config.title){config.title='Schedules';}
		
		if(typeof config.collapsible == 'undefined') config.collapsible= true;
		config.store= Ext.create('Spectrum.store',{url:'index.php/schedule/json_season_schedule_game_join/'+App.TOKEN,model:model_name,paginator:true});

	    config.width="100%";// 

		config.listeners=
		{
			selectionchange:function(sm, selectedRecord)
			{
				if (selectedRecord.length) 
		    	{  //enable row buttons
		    		var bool=false;
		    		var row_buttons=['btn_sch_delete','btn_sch_rainout'];//btn_sch_postponed
     				 var btn,btn_id;
     				 for(i in row_buttons)
     				 {  //toggle the disabled property of each
						 btn_id=row_buttons[i];
						 var btn=Ext.getCmp(btn_id) ;
						 if(btn_id &&btn && typeof btn.setDisabled == 'function') //this line added for IE
							 {btn.setDisabled(bool);	}		 
     				 }	
		    	}
			}
			
			,edit:function(e)
			{

				var name=e.record.data['schedule_name'];
    			var id = e.record.data['schedule_id'];
    			
    			var p = e.record.data['is_published'];
    			//.log(p);
    			var url='index.php/schedule/post_rename/'+App.TOKEN;
    			
    			var post="schedule_id="+id+"&name="+escape(name)+"&pub="+p;
    			
    			
    			var callback={scope:this,success:function(o)
    			{	
    				 var r=o.responseText;
     	 			 if(isNaN(r) || r<0 )
     	 			 {
     	 	 			 App.error.xhr(o);
     	 	 			 e.record.reject();
     	 			 }
     	 			 else
     	 			 {
						 e.record.commit(); 
     	 			 }
    			}};
    			YAHOO.util.Connect.asyncRequest('POST',url,callback,post);

			}
		};
		config.columns= 
	    [
        	{text      : 'Schedule Name',flex:1,sortable : true,dataIndex: 'schedule_name',
        		editor: { allowBlank: false }}		
			,{text     : 'Season',flex:1,sortable : true,dataIndex: 'season_name'}	    
			,{text     : '# Total Games',width:135,sortable : true,dataIndex: 'total_games'}
			,{text     : '# Scored Games',width:135,sortable : true,dataIndex: 'valid_count'}
			,{text     : 'Published',width:65,sortable : true,dataIndex: 'is_published',
			    renderer:formatBoolYN,
			    editor: {xtype: 'combobox', typeAhead: true,triggerAction: 'all',store: [['t','Yes'],['f','No']]}
			   // editor: { allowBlank: false }//plain string is default
			}
		];
        this.callParent(arguments);
	}
}
);

}

