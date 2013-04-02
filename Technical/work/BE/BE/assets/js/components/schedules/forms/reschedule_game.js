
var gfe = 'Spectrum.forms.game.edit';
if(!App.dom.definedExt(gfe)){
Ext.define(gfe,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
       
    constructor     : function(config)
    { 
    	if(!config.id)
		config.id='reschedule_form';
		//if(config.id){id=config.id;}
		this.window_id=config.window_id;
		 
		if(!config.title)config.title= '';
		//config.autoHeight= true;,resizable:false,bodyPadding: 10,width: '100%',
	    config.defaults= {anchor: '100%'};
	    
	    //defaultType: 'textfield',//person_birthdate
	    config.items=
	    [
		{	xtype:'textfield',hidden:true,	name:'game_id',		id:'form_resc_game_id'	}
		,{	xtype:'textfield',hidden:true,	name:'swap_game_id',	value:-1,	id:'swap_game_id'	}
		,{	xtype:'textfield',hidden:true,	name:'home_id'	}
		,{	xtype:'textfield',hidden:true,	name:'schedule_id',id:'form_schedule_id'	}
		,{	xtype:'textfield',hidden:true,	name:'away_id'	}
		,{	xtype:'textfield',hidden:true,	name:'end_time'  	}     
		,{	xtype:'textfield',hidden:true,	name:'venue_id'  	}     
		,{	xtype:'textfield',hidden:true,	name:'new_venue_id'  ,id:'form_new_venue_id',value:null	}     

		,{ xtype:'fieldset',	title:'Current Game Details',	collapsible: true, id:'form_resc_data',
		items:
		  [ 		    		
		    	{	xtype:'displayfield', fieldLabel:'Home Team',  name:'home_name',	value:''}
		    	,{	xtype:'displayfield',	fieldLabel:'Away Team',name:'away_name',	value:''		}
		    	//, style: "text-align: right",		    
		    	,{	xtype:'displayfield',fieldLabel:'Location',	name:'venue_name',	value:''	}
		    	,{	xtype:'displayfield',	fieldLabel:'Date',name:'fancy_game_date',	value:''	}
		    	,{	xtype:'displayfield',	fieldLabel:'Time',name:'start_time',	value:''	}
		   ]}//end of data fieldset
		   ,{ 
		   	   xtype:'fieldset',	
		   	   title:'Select one of four rescheduling options',
		   	   width:'100%',	
		   	   collapsible: false,
		   	   id:'form_resc_basic',
				items:
				[
		        {
		        	xtype: 'button',	
		        	iconCls:'add',   
		        	id : 'btn_reschedule_newtime', 
		        	scope:this, 
		        	text:'Input a new time and location for this game',   
		        	handler:function(o)
	                {
	                	//get the venues for this schedule
	                	var url='index.php/schedule/json_schedule_venues_fac/'+App.TOKEN;	
						var post='schedule_id='+Ext.getCmp('form_schedule_id').getValue();
						YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,failure:App.error.xhr,success:function(o)
						{
							var i,v,venues=YAHOO.lang.JSON.parse(o.responseText);
							var menu_venfac=new Array();
							for(i in venues)
							{
								v=venues[i];
								var comboName= v['facility_name']+" : "+v['venue_name']; 
								var vid=v['venue_id'];
								menu_venfac.push({text:comboName,value:vid,handler:function(o,e)
								{
									//menu click
									var name = o.text;
									var id   = o.value;
									var btn=Ext.getCmp('btn_resc_form_venue');
									btn.setText(name);
									Ext.getCmp('form_new_venue_id').setValue(id);
									
									

								}});
								
							}

							var v_btn=Ext.getCmp('btn_resc_form_venue');//btn_org_select_form	
							v_btn.setDisabled(false);	
							v_btn.menu=Ext.create('Spectrum.btn_menu',{items:menu_venfac});		
							
	                		Ext.getCmp('form_resc_basic').hide();
	                		Ext.getCmp('form_resc_input').show();
						}},post);
	                }
		        }
		        
		    	,{  xtype:'displayfield',   name:'blank_1',  value:'' }
		        ,{
		        		
		        	xtype: 'button',	
		        	iconCls:'clock_red',   
		        	scope:this, 
		        	text:'Postpone this game for rescheduling later',  
		        	id : 'btn_reschedule_postpone',  
		        	handler:function(o)
	                {
						
						Ext.MessageBox.prompt('Game Postponement Confirmation',
						'To postpone this game, enter a memo and then press "OK".',function(btn_id,input)
						{
							if (btn_id != 'ok') return;
							var game_id=Ext.getCmp('form_resc_game_id').getValue();
							var url='index.php/games/post_unschedule_game/'+App.TOKEN;
							var post='game_id='+game_id+"&note="+escape(input);
							var callback={failure:App.error.xhr,scope:this,success:function(o)
							{
								var r=o.responseText;
								if(isNaN(r) || r<=0)
								{
									Ext.MessageBox.show({title:"Error",msg:r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
								}
								else
								{
									Ext.MessageBox.show({title:"Success",msg:'',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK});
									this.finish();
								}
							}};
							YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
						},this);
	                }
		        }
		    	,{  xtype:'displayfield',   name:'blank_2',  value:'' }
		        ,{	xtype: 'button',	
		        	iconCls:'magnifier',  
		        	text:'Find an existing game to swap with',  
		        	id : 'btn_reschedule_swap',  
		        	handler:function(o)
	                {
	                	Ext.getCmp('form_resc_basic').hide();
	                	Ext.getCmp('form_resc_swap').show();
	                }
		        }
		        ,{  xtype:'displayfield',   name:'blank_3',  value:'' }
		    	,{	
		    		xtype: 'button',	
		    		iconCls:'delete',  
		    		scope:this,
		    		//flex:1,
		    		//width:'100%',
		        	text:'Cancel this game completely',  
		        	id : 'btn_reschedule_cancel',  
		        	handler:function(o)
	                {
 
	                	Ext.MessageBox.confirm('Game Cancellation Confirmation',
						'Once a game is cancelled, it will not show up on standings or schedules anywhere.  Proceed?'
						,function(btn_id)
						{
 
							if (btn_id != 'ok' && btn_id != 'yes') return;
							var game_id=Ext.getCmp('form_resc_game_id').getValue();
							var url='index.php/games/post_delete/'+App.TOKEN;
							var post='game_id='+game_id;//+"&note="+escape(input);
							var callback={scope:this,failure:App.error.xhr,success:function(o)
							{
								var r=o.responseText;
								if(isNaN(r) || r<=0)
								{
									Ext.MessageBox.show({title:"Error",msg:r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
									
									App.error.xhr(o);
								}
								else
								{
									Ext.MessageBox.show({title:"Success",
										msg:'Game cancelled.',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK});
									//App.forms.winReschedule.hide();
									this.finish();
								}
							}};
							YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
						},this);
	                	
	                }
		        }
		   ]}//end of basic options fieldset

		    
		    ,{	
		    	xtype:'fieldset'	,
		    	hidden:true,
		    	title:'Input ',
		    	id:'form_resc_input',
		    	width:'100%',
		        items:
				[
					
		    	{	xtype:'textfield',	name:'input_memo',fieldLabel:"Memo"	,width:'100%'}
		    	,{	xtype:'datefield',format: 'Y/m/d',	name:'input_date',fieldLabel:"New Date",allowBlank:false	}
				,{xtype : 'fieldcontainer',   fieldLabel:"Time", layout:'hbox',    defaults: {hideLabel: true},
			    items:
			    [		
					 {xtype:'numberfield',name : 'new_hour', fieldLabel: 'hr',width:55, minValue: 0, maxValue:12,step: 1
					 	,value:6,allowBlank:false}
		    		 ,{	xtype:'displayfield',	name:'timecolon',	value:'&nbsp;:&nbsp;'	}
					 ,{xtype:'numberfield',name : 'new_min',  fieldLabel: 'min',width:55, minValue: 0, maxValue:59,step: 1
						,value:00,allowBlank:false}
		    		 ,{	xtype:'displayfield',	name:'sep',	value:'&nbsp;&nbsp;'	}
					,{xtype: 'combobox', width:55,   name:'new_ampm',value:'pm',  typeAhead: true,  store: [['am','am'],['pm','pm']],allowBlank:false}
				]}
				,{
	        		xtype: 'button',
	        		width:'100%',
		        
	        		iconCls:'bullet_arrow_down',
	                id : 'btn_resc_form_venue',
	                
	                //hidden:true,
	                menu:null,
	                //iconAlign: 'top',
	                text:'Select Location'
		        }
		        
		    	,{  xtype:'displayfield',   name:'blank_ven',  value:'' }
				,{
					xtype:"button",
					text   : 'Back',
					id:'form_resc_input_back',
					iconCls:'arrow_left',
					handler: function() 
					{
	    				Ext.getCmp('form_resc_input').hide();
	                	Ext.getCmp('form_resc_basic').show();
					}
				}
				
				//,'->'
				,{	
					xtype:"button",	
					text   : 'Save',
					id:'form_resc_input_save',
					iconCls:'disk',	
					scope:this,
					handler: function() 
					{
	    				 var form = this.getForm();
						 //dont check valid:  some things can be blank
 
	        			var form_data=new Array();
					    Ext.iterate(form.getValues(), function(key, value) 
					    {

					        form_data.push(key+"="+escape(value));
							
					    }, this);
						var post= form_data.join('&');
						 
						var callback={scope:this,failure:App.error.xhr,success:function(o)
						{
							var r=o.responseText;
							
							if(isNaN(r) || r < 0 )
							{
								Ext.MessageBox.alert("Error",r);
							}
							else
							{
								Ext.MessageBox.alert("Success",'');
								this.finish();
							}
							//
						}};
						var url="index.php/games/post_update_game/"+App.TOKEN;
						YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
						}
					
					}
				
				]

		    }//end of input fieldset
		    
		    
		    
		    ,{	
		    	xtype:'fieldset'	,
		    	hidden:true,title:'Swap ',
		    	id:'form_resc_swap',
		    	width:'100%',
		    	items:
				[
					
		    	{	xtype:'textfield',	name:'input_memo',fieldLabel:"Memo"	,width:'100%'}

	       		, 
	       		{
		        	xtype : 'fieldcontainer',
			        layout:'hbox',
			        items:
			        [
		    		{	xtype:'datefield',format: 'Y/m/d',	name:'search_date' ,id:'form_search_date',fieldLabel:"Search",allowBlank:false	}

		    		,{ 
		    			xtype:"button",
		    			iconCls:'magnifier',
		    			scope:this,
		    			handler:function()
		    			{
							var value=Ext.getCmp('form_search_date').getValue();
							
							var d=value ? Ext.Date.dateFormat(value, 'Y-m-d') : null;
							
							
							if(d==null)return;
							var s=Ext.getCmp('form_schedule_id').getValue();//
							var url='index.php/games/post_search_games/'+App.TOKEN;
							var post="search_date="+d+"&schedule_id="+s;
							YAHOO.util.Connect.asyncRequest('POST',url,{
								scope:this,failure:App.error.xhr,
								success:function(o)
							{
								var i,g,games=YAHOO.lang.JSON.parse(o.responseText);
								if(!games.length)
								{
									
									Ext.MessageBox.show({title:"No Games Found",msg:'Try a different date',icon:Ext.MessageBox.WARNING,buttons:Ext.MessageBox.OK});
									//Ext.MessageBox.alert({title:'',msg:'',icon:Ext.MessageBox.INFO});
									
									var g_btn=Ext.getCmp('btn_resc_form_game');
									g_btn.setDisabled(true);	
									return;
								}
								var g_menu=new Array();
								for(i in games)
								{
									g=games[i];
									var comboName= g['home_name']+" vs "+g['away_name'] +"; "+g['venue_name']+" @ "+g['game_date']+", "+g['start_time']; 
									var gid=g['game_id'];
									g_menu.push({text:comboName,value:gid,handler:function(o,e)
									{
										//menu click
										var name = o.text;
										var id   = o.value;
										var btn=Ext.getCmp('btn_resc_form_game');
										btn.setText(name);
										Ext.getCmp('swap_game_id').setValue(id);

									}});
									
								}
								var g_btn=Ext.getCmp('btn_resc_form_game');
								g_btn.setDisabled(false);	
								g_btn.menu=Ext.create('Spectrum.btn_menu',{items:g_menu});
		
							}},post);
							
		    			}
		    		}
		    		]
				}//end of hbox				
		    	,{
		    		xtype: 'button',
		    		width:'100%',
		    		disabled:true,
		    		iconCls:'bullet_arrow_down',
		    		id : 'btn_resc_form_game',
	                menu:null,
	                text:'Select Game'
		        }
		    	,{  xtype:'displayfield',   name:'blank__',  value:'' }
		   		,{
		   			xtype:"button",
		   			text   : 'Back',
		   			id:'form_resc_swap_back',
		   			iconCls:'arrow_left',
		   			handler: function() 
					{
	                	Ext.getCmp('form_resc_swap').hide();
						Ext.getCmp('form_resc_basic').show();
					}
				}
				,{  xtype:'displayfield',   name:'blank__save_',  value:'' }
		   		,{  
		   			xtype:"button",
		   			text   : 'Save',
		   			id:'form_resc_swap_save',
		   			iconCls:'disk',
		   			scope:this,
		   			handler: function() 
					{
						//post wlil validate and accept or reject the swap
						var url='index.php/games/post_swap_games/'+App.TOKEN;
						var swapid=Ext.getCmp('swap_game_id').getValue();
						if(!swapid||swapid==-1)
						{
							//Ext.MessageBox.alert({title:'Incomplete',msg:'No game selected',icon:Ext.MessageBox.Warning});
							Ext.MessageBox.show({title:"Incomplete",msg:'No game selected',icon:Ext.MessageBox.WARNING,buttons:Ext.MessageBox.OK});
							return;
							
						}
						var post="swap_game_id="+swapid+"&game_id="+Ext.getCmp('form_resc_game_id').getValue();
						YAHOO.util.Connect.asyncRequest('POST',url,
						{scope:this,failure:App.error.xhr,
						success:function(o)
						{
							var r=o.responseText;
							if(isNaN(r)||r<1)
							{
								//Ext.MessageBox.alert({title:'Error',msg:r,icon:Ext.MessageBox.ERROR});
								Ext.MessageBox.show({title:"Error",msg:'Save problem:'+r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK})
								//alert(r);
							}
							else
							{
								Ext.MessageBox.show({title:"Success",msg:'Saved',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK})
								//App.forms.winReschedule.hide();
								this.finish();
							}
						}},post);
						
	                	//Ext.getCmp('form_resc_swap' ).hide();
						//Ext.getCmp('form_resc_basic').show();  
					}
				}
		    ]}

		];//global form items end    


		this.callParent(arguments); 
	}
	,finish:function()
	{
		var w=Ext.getCmp(this.window_id);
		if(w)w.hide();
		
	}
});
}