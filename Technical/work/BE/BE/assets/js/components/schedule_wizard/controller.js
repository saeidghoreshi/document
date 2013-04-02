 

var c_SCHEDULER_WIZARD = function(){this.construct()};
c_SCHEDULER_WIZARD.prototype = 
{   

    hideCls:'dghidden',//o_sch_wizard.hideCls

    tsRecord:null,
    loaded:null,

    construct:function()
    { //the constructor for this object
    	App.hidePanel();
        Ext.QuickTips.init();
    },
    
    //init is called after seasons.get is successfull. inside seasons.display
	init:function()
	{		
		//build all the required components.  most start off hidden. 
 
		o_sch_wizard.timeslots.grid();
		 
		o_sch_wizard.matches.grid();//.
		o_sch_wizard.games.grid();//.
		
		o_sch_wizard.toolbar.init();//.l

		o_sch_wizard.start.form();//.
		//enable this button
		Ext.getCmp('btn_start_form_ready').setDisabled(false);
		 
		o_sch_wizard.saves.get();// lads into the grid thats inside start.form
		 
	},
	
    toolbar:
    {
		btn_id_scheduler:'wiz_tb_btn_scheduler',
		btn_id_matches:'wiz_tb_btn_matches',
		btn_id_audit:'wiz_tb_btn_audit',
		btn_id_finalize:'wiz_tb_btn_final',
		btn_id_opt:'wiz_tb_btn_opt',
		btn_id_rules:'wiz_tb_btn_rules',
		disable_left:function(bool)
	    {
    		var btns=[o_sch_wizard.toolbar.btn_id_scheduler,o_sch_wizard.toolbar.btn_id_matches,o_sch_wizard.toolbar.btn_id_rules];
    		for(i in btns)
    		{
				var id=btns[i];
				if(Ext.getCmp(id))
					{Ext.getCmp(id).setDisabled(bool);}
    		}
	    },
    
	    disable_right:function(bool)
	    {
    		var btns=[o_sch_wizard.toolbar.btn_id_audit,o_sch_wizard.toolbar.btn_id_finalize];
    		for(i in btns)
    		{
				var id=btns[i];
				if(Ext.getCmp(id))
					{Ext.getCmp(id).setDisabled(bool);}
    		}
	    },
	    
	    audit_menu:null,
	    init:function()
	    {
			//so far this just sets up the audit menu
			
			var type_ids  =['tc','vc','dc','tt','td','tv','dm','dd','dv','vv','gm'];
			var i, type_names=['Team Balance','Venue Stats','Date Stats','Team Matchups','Team-Date','Team-Venue'
					,'Division Matchups','Division-Date','Division-Venue','Venue Distance','Games Missing'];
			o_sch_wizard.toolbar.audit_menu=new Array();
			//.log(' now make audit menu');
			for(i=0;i<type_ids.length;i++)
			{
				//IE workaround, use basic look not for in
				
				o_sch_wizard.toolbar.audit_menu.push({text:type_names[i],value:type_ids[i],handler:function(o,e)
				{
					//menu click
					var name = o.text;
					var id   = o.value;
					 
					o_sch_wizard.audit.type_id=id;
					o_sch_wizard.audit.type_name=name; 
					//o_sch_wizard.matches.hide();
					//o_sch_wizard.timeslots.hide();
					//o_sch_wizard.finalize.hide();
					o_sch_wizard.audit.get();
					
					
					o_sch_wizard.saves.save();
					
					
					//o_sch_wizard.audit.show();
					 
				}});
				
			}

	    }
    },

    start:
	{
		form_id:'wiz_start_form',
		grid_id:'wiz_load_grid',
		form:function()
		{ 
 			//defayults for the rules form so its not blank
 			var dflt={'min':'','max':'',len:'1:00',warmup:'0:00',teardown:'0:00',min_disabled:true,max_disabled:true
					,min_btw:"0:00",max_btw:"0:00"
					//items added for venue rules feature
					,facility_lock:false
					,venue_distance:500
			};
			   
  
			var startForm= Ext.create('Spectrum.form.new_schedule', 
			{ 
				id:o_sch_wizard.start.form_id,
 
				title: 'Welcome to the Scheduler Wizard',
				//along with built in items, use the rules form here as well,
 				items:Ext.create('Spectrum.form.sch_rules',{id:o_sch_wizard.rules_global.form_id,record:dflt}),
				renderTo:'wiz-start-form'
				//add a success trigger in the context of this controller
				,fireSuccess:function(r)
				{
					o_sch_wizard.start.session_id=r;
					//so save and create have consistent behaviour
					o_sch_wizard.saves.load();//load the new empty save file
				}
				 
			});
			//then make the grid on right hand side
			o_sch_wizard.saves.grid();
			
	    },
		hide:function()
	    {	
 
    		Ext.getCmp(o_sch_wizard.start.form_id).hide();
    		Ext.getCmp(o_sch_wizard.start.grid_id).hide();
			//whenever we hide, check to see if we need to load the current season
			
			o_sch_wizard.seasons.get_saved();
			
			Ext.getCmp(o_sch_wizard.toolbar.btn_id_audit).menu=Ext.create('Spectrum.btn_menu',{items:o_sch_wizard.toolbar.audit_menu});
			
			//if(Ext.getCmp(o_sch_wizard.start.form_id))
    			//Ext.getCmp(o_sch_wizard.start.form_id).destroy();//destroy is to make sure that global rules form doesnt conflict if its loaded in two places
	    },
	    show:function()
		{
 
		}
	},
    
    saves:
    {
    	get:function()
		{
			if(!Ext.getCmp(o_sch_wizard.start.grid_id))
			{
				o_sch_wizard.saves.grid();
			}
			Ext.getCmp(o_sch_wizard.start.grid_id).refresh();
			 
		},
 
		save:function()
		{
			Ext.Ajax.request({
				url:'index.php/schedule/post_file_update/'+App.TOKEN
				,params:{session_id:o_sch_wizard.start.session_id}
				
			});
			 
		},
		session_id:null,
		grid:function()
		{
			//create and render the grid for saved files
 
			var g=Ext.create('Spectrum.grids.schedule_saves',
			{ 
				id:o_sch_wizard.start.grid_id
				,renderTo:'wiz-load-grid'
				,bbar:
				[
				 {
            		xtype:'button'
            		,text:''
            		,iconCls:'book_open'
            		,id:"btn_start_form_load"
            		,tooltip:'Load'
            		,disabled:true
            		,scope:this
            		,handler:function(o,e)
					{	
						var rows=Ext.getCmp(o_sch_wizard.start.grid_id).getSelectionModel().getSelection();
 
						if(!rows.length)return;
						
						var row=rows[0];
						o_sch_wizard.start.session_id=row.get('session_id');
						 
						o_sch_wizard.saves.load();
             		}
	              }
				]
	        });
 
	            
		},
		load:function()
		{
			//load a saved file based on selected
			var url='index.php/schedule/post_file_load/'+App.TOKEN;
			
			var post= "session_id="+o_sch_wizard.start.session_id;
			Ext.Ajax.request({
				url:'index.php/schedule/post_file_load/'+App.TOKEN
				,params:{session_id:o_sch_wizard.start.session_id}
				,success:function(o)
				{
					var r=o.responseText;
					 
					o_sch_wizard.start.hide();
					o_sch_wizard.toolbar.disable_left(false);
					 
					o_sch_wizard.loaded=Ext.JSON.decode(r);
					o_sch_wizard.loaded.total_games=0;//none to start!! this is updated later
					o_sch_wizard.timeslots.get();
					o_sch_wizard.timeslots.show();
					
					o_sch_wizard.seasons.get_saved();
					
					o_sch_wizard.cal.load();
				}
				,failure:App.error.xhr
			});
 
		}
    },
    
    seasons:
	{
 
	    get_saved:function(o)
	    { 
			Ext.Ajax.request(
			{
				url:'index.php/schedule/json_current_season/'+App.TOKEN
				,method:"POST"
				,success:this.display_saved
				,failure:App.error.xhr
			});
	    },
	    display_saved:function(o)
	    {
	    	var season=Ext.JSON.decode(o.responseText);
	    	o_sch_wizard.seasons.season_id=season['season_id'];
	    	//send date range to calendar
			o_sch_wizard.cal.minDate=new Date(season['season_start'].substring(0,10));
			o_sch_wizard.cal.maxDate=new Date(season['season_end'  ].substring(0,10));

	    }
	},

    matches:
    {
    	match_pk:null,
		get:function()//was get_matches
	    {
			//o_sch_wizard.matches.disableBtns(true);
			o_sch_wizard.matches.match_pk=null;
			Ext.getCmp(o_sch_wizard.matches.grid_id).refresh();
 
	    },
 
	    grid_id:'wiz_matches_grid',
	    grid:function()
	    {
	    	//Spectrum.grids.s_matches
		    var grid = Ext.create('Spectrum.grids.s_matches', 
		    {
		    	title: 'Matches',
		    	renderTo: 'wiz-dg-matches',
		    	id:o_sch_wizard.matches.grid_id
		    	,bbar:
		    	[
		    		{
		    			iconCls:'date'
		    			,tooltip:'Edit dates'
		    			//,disabled:true
		    			,id:'btn_match_dates'
		    			,handler:function(o)
		    			{ 
		    				var rows=Ext.getCmp(o_sch_wizard.matches.grid_id).getSelectionModel().getSelection();
		    				if(!rows.length){return;}
							
							o_sch_wizard.matches.match_pk= rows[0].get('match_pk');
							
							o_sch_wizard.cal.save_mode='matches';
							//this will go to the calendar window
							o_sch_wizard.matches.get_dates();
		    			}
				    }
		    	]
 
			});
			 
	    },
	    get_dates:function()
	    {
	    	
		    var rows=Ext.getCmp(o_sch_wizard.matches.grid_id).getSelectionModel().getSelection();
		    if(!rows.length){return;}
			
			o_sch_wizard.matches.match_pk= rows[0].get('match_pk');
			var post={};
			post.match_pk=o_sch_wizard.matches.match_pk;
 
			Ext.Ajax.request(
			{
				url:'index.php/schedule/json_match_dates/'+App.TOKEN
				,params:post
				,success:function(o)
				{
					var dates=Ext.JSON.decode(o.responseText);
					 
					o_sch_wizard.cal.show();
					o_sch_wizard.cal.select(dates);
				}
				,failure:App.error.xhr
				
			});
 
	    },
 
	    hide:function()//was hide_matches
	    {
			Ext.get('wiz-screen-matches').addCls(o_sch_wizard.hideCls); 
			Ext.getCmp(o_sch_wizard.matches.grid_id).hide();
	    },
	    show:function()
		{
			o_sch_wizard.audit.hide();
			o_sch_wizard.timeslots.hide();
			o_sch_wizard.cal.hide();
			o_sch_wizard.finalize.hide();
			o_sch_wizard.venues.hide();
			Ext.get('wiz-screen-matches').removeCls(o_sch_wizard.hideCls); 
			Ext.getCmp(o_sch_wizard.matches.grid_id).show();
		}
    },
    timeslots:
    {
    	
		get:function()//was get_datesets
	    {
 
    		Ext.getCmp(o_sch_wizard.timeslots.grid_id).refresh();
	    },
	    grid_id:'wiz_timeslots_grid',
	    form_id:'timeslot_form',
	   // window_id:'timeslot_window',
	    row_dateset_pk:null,
 
	    
		grid:function()//was timeslot_grid
		{	 
		    var grid = Ext.create('Spectrum.grids.s_timeslots', 
		    {
		    	renderTo: 'wiz-dg-timeslots',
		    	id:o_sch_wizard.timeslots.grid_id,
		    	height:300,
		    	listeners:
		    	{
		    		selectionchange:function(sm,recs)
					{
						if(!recs.length){return;}
						var row=recs[0];
						var pk = row.get('dateset_pk');
						//save the pk of selected rwo
						o_sch_wizard.timeslots.row_dateset_pk=pk;
					}
				},
	            bbar:
	            [
		        	{
		        		tooltip:"Dates"
		        		,iconCls:'date'
		        		,id:'btn_dateset_dates'
		        		,disabled:true
		        		,handler:function()
		        		{
		        			o_sch_wizard.games.abort();
						    o_sch_wizard.cal.show();
		        			o_sch_wizard.cal.save_mode='timeslots';
							o_sch_wizard.timeslots.get_dates_internal();//will trigger external after
						}
					}
		        	,{
		        		tooltip:"Fields"
		        		,iconCls:'map'
		        		,id:'btn_dateset_venues'
		        		,disabled:true
		        		,handler:function()
		        		{
		        			o_sch_wizard.games.abort();
							
							var rowsSelected = Ext.getCmp(o_sch_wizard.timeslots.grid_id).getSelectionModel().getSelection();
        					if(rowsSelected.length==0) 
        					{  
        						return;
							}
							var rec=rowsSelected[0];
							o_sch_wizard.timeslots.hide();
							o_sch_wizard.venues.show();
							//o_sch_wizard.venues.facility_id=-1;
							o_sch_wizard.venues.dateset_pk=rec.get('dateset_pk');
							o_sch_wizard.venues.timeslot_name=rec.get('set_name');
							o_sch_wizard.venues.get();
							
							//o_sch_wizard.games.hide(true);
							
							//o_sch_wizard.venues.hide(false);
							//o_sch_wizard.venues.save();
		        			
						}
					}
			    ]
			});
 			
 			grid.getStore().on('load',o_sch_wizard.timeslots.check_overflow);
 			
 			
	    },
 
		
		get_dates_internal:function()
		{
			var rowsSelected = Ext.getCmp(o_sch_wizard.timeslots.grid_id).getSelectionModel().getSelection();
        	if(rowsSelected.length==0) {  return;}
        	var post={};
        	post.dateset_pk=rowsSelected[0].get('dateset_pk')
 
			Ext.Ajax.request(
			{
				url:'index.php/schedule/json_ds_dates/'+App.TOKEN
				,params:post
				,success:function(o)
				{
					var dates=Ext.JSON.decode(o.responseText);
					//o_sch_wizard.cal.initial_dates=dates;
			        o_sch_wizard.cal.select(dates);
					o_sch_wizard.timeslots.get_dates_external();
				}
				,failure:App.error.xhr
			});
		},
		
		get_dates_external:function()
		{
			var rowsSelected = Ext.getCmp(o_sch_wizard.timeslots.grid_id).getSelectionModel().getSelection();
        	if(rowsSelected.length==0) {  return;}
        	var post={};
        	post.dateset_pk=rowsSelected[0].get('dateset_pk')
			Ext.Ajax.request(
			{
				url:'index.php/schedule/json_ds_dates_used_external/'+App.TOKEN
				,params:post
				,success:function(o)
				{
					var dates=Ext.JSON.decode(o.responseText);
					o_sch_wizard.cal.highlight(dates);
				}
				,failure:App.error.xhr
			});
		},
		
		hide:function()//was hide_timeslots
	    {
    		o_sch_wizard.games.abort();
	    	Ext.get('wiz-screen-timeslots').addCls(o_sch_wizard.hideCls); 
			Ext.getCmp(o_sch_wizard.timeslots.grid_id).hide();
			Ext.getCmp(o_sch_wizard.games.grid_id).hide();
	    },
	    
	    show:function()
	    {
	    	//on show, hide otehrs first
	    	o_sch_wizard.audit.hide();
			o_sch_wizard.matches.hide();
			o_sch_wizard.cal.hide();
			o_sch_wizard.finalize.hide();
			o_sch_wizard.venues.hide();
		
			Ext.get('wiz-screen-timeslots').removeCls(o_sch_wizard.hideCls); 
			Ext.getCmp(o_sch_wizard.timeslots.grid_id).show();
			Ext.getCmp(o_sch_wizard.games.grid_id).show();
	    }
	    
	    
	    /**
	    * trigger to be called whenever rules/timeslots are changed
	    * will alert user if game length + warmup is > than time given in timeslot
	    */
	    ,check_overflow:function()
	    {
			Ext.Ajax.request(
			{
				url:'index.php/schedule/json_dateset_overflow/'+App.TOKEN
				,method:'POST'
				,success:function(o)
				{
					 
					var errors = Ext.JSON.decode(o.responseText); 
					if(!errors.length) {return;}
					var name = errors[0]['set_name'];
					var mint = errors[0]['spare'];
					messages.showError(o_sch_wizard.timeslots._make_error_string(name,mint));
				}
				,failure:App.error.xhr
				
			});
			
	    }
	    
	    ,_make_error_string:function(name,mint)
	    {
			return "Some timeslots cannot be used because the "
				+"amount of time assigned to the timeslot was shorter than the "
				+"amount of time required to play the game plus required warmup time and "
				+"cooldown time. Please adjust timeslot '"+name+"', or "
				+"your game, warmup, cooldown rules. ";
	    }
	    
    },
    
    
    cal:
    {
    	load:function()
	    { //load window, add required YUI components  
  
	        var required = ["calendar"];
	        var loader = new YAHOO.util.YUILoader({
	            require: required,
	            base: App.loader.base,
	            //loadOptional: true,
	            //scope: this,
	            filter:App.loader.filter,
	            onSuccess: function()
	            {  
            		o_sch_wizard.cal.init();//create the calendar now that library is ready
	            }
	        }).insert();
	    },
	    minDate:null,//test
	    maxDate:null,
	    pages:null,
	    window:null,
	    window_id:'_sch_cal_window_',
		init:function()//if(this.tsCalendar)
		{ 
			//first create modal window for it to live in
			if(Ext.getCmp(o_sch_wizard.cal.window_id)) {Ext.getCmp(o_sch_wizard.cal.window_id).destroy();}
			o_sch_wizard.cal.window = Ext.create('Ext.spectrumwindows',
			{
				title: 'Calendar',
				id:o_sch_wizard.cal.window_id,
				closable:false,//close button
				closeAction:'hide',//default is destroy, we want to only hide
				resizable:false
				,width: 580
				,height: 500
				,draggable:false,
				plain: true
				,modal:true
				,headerPosition: 'top'
				,layout: 'fit',
				html: "<div class='yui-skin-sam'><div id='ts-multi-cal'></div></div>"//render to this div. 
				// yui-skin-sam is not named after me, it is just required forYUI
				,buttons:
				[
					{text:'Cancel',iconCls:'cross'//,scope:this
					,handler:function()
					{
						o_sch_wizard.cal.clear();
						o_sch_wizard.cal.window.hide();
					}}
					,{text:'Save',iconCls:'disk'//,scope:this
					,handler:function()
					{
						o_sch_wizard.cal.save();//save on success will trigger hide window
					}}
				]
			});
			//events for IE, to make sure cal is follows hide show of window
			o_sch_wizard.cal.window.on('hide',function()
			{
				document.getElementById('ts-multi-cal').style.display="none";
			});
			o_sch_wizard.cal.window.on('show',function()
			{
				document.getElementById('ts-multi-cal').style.display="block";
			});
			//show hide might be needed for render to cal to have div exist
			o_sch_wizard.cal.window.show();
			o_sch_wizard.cal.window.hide();
    		if(o_sch_wizard.cal.pages)
    		{
				//o_sch_wizard.cal.clear();	
				//o_sch_wizard.cal.show();
 
				return;
    		}
 
			o_sch_wizard.cal.pages = new YAHOO.widget.CalendarGroup('ts-multi-cal','ts-multi-cal' , 
					        {MULTI_SELECT:true, PAGES:6, close:false,
	                        	pagedate:o_sch_wizard.cal.minDate,//defines first month shown in group
	                        	mindate: o_sch_wizard.cal.minDate,//cannot select before this
	                        	maxdate: o_sch_wizard.cal.maxDate, //cannot select after this
                        		title:"Select Dates"
					        });
			
			//configure the calendar to begin on Monday
			//this.venueCalendar.cfg.setProperty("start_weekday", "1");
			
			
			o_sch_wizard.cal.select(o_sch_wizard.cal.initial_dates);
			//o_sch_wizard.cal.pages.render();//render called by select
			
		},
		initial_dates:[],
		clear:function()
		{
			o_sch_wizard.cal.pages.deselectAll();
			o_sch_wizard.cal.pages.render();
		},
		select:function(date_array)
		{
			if(!o_sch_wizard.cal.pages){o_sch_wizard.cal.load();}
			
			var selected=new Array();
			for(i in date_array)if(date_array[i])
			{ 
				selected.push(new Date(date_array[i]));
 
			} 
			if(o_sch_wizard.cal.pages)
			{
				o_sch_wizard.cal.pages.select(selected);
				o_sch_wizard.cal.pages.render();
				
			}
		},
		highlight:function(date_array,hex_col)
		{
			if(!o_sch_wizard.cal.pages){o_sch_wizard.cal.load(); return;}
			o_sch_wizard.cal.pages.render();//IMPORTANT if you render AFTEr collours added they get erased
			if(!hex_col)
			{
				hex_col='FF0000';
			}
			for(i in date_array)if(date_array[i])
			{
				var d=date_array[i];
 
				if(typeof(d) !='function' )//IE9 validate
				{
					//add the background colour to each of these dates
					var oDate=new Date(d);
					
					o_sch_wizard.cal._set_colour(oDate.getFullYear(),oDate.getMonth()+1,oDate.getDate(),'#'+hex_col);
				}

			}
			
		},
		_set_colour:function(c_year,c_month,c_day,hex)
	    {
    		var c_year  = "y"+c_year;
    		var c_month = "m"+c_month;
    		var c_day   = "d"+c_day;
    		//.log('cal._set_colour',c_year,c_month,c_day,hex);
			var visible_months = YAHOO.util.Dom.getElementsByClassName(c_year);
			for(i in visible_months)if(visible_months[i])
			{
				var months = YAHOO.util.Dom.getChildren(visible_months[i]);
				for(j in months)if(months[j])
				{
					if(YAHOO.util.Dom.hasClass(months[j],c_month))
					{
						var weeks = YAHOO.util.Dom.getChildren(months[j]);
						for(k in weeks)if(weeks[k])
						{
							var days = YAHOO.util.Dom.getChildren(weeks[k]);
							for(d in days)if(days[d])
							{
								if(YAHOO.util.Dom.hasClass(days[d],c_day))
								{
									//YAHOO.util.Dom.addClass(days[d],name);
									YAHOO.util.Dom.setStyle(days[d],'background-color',hex)
								}
							}
							
						}
						
					}
				}
			}
			
	    },
	    save_mode:null,//t for timeslots. alternate is 'matches'
	    
	    save:function(o)
	    {
			var dates=o_sch_wizard.cal.pages.getSelectedDates();
			
			o_sch_wizard.cal.clear();	
			var valid_dates=new Array();//need to validate this before save for IE9
			//otherwise the YUI function returns lats of NaN,false,functions,other garbage
 
			for(i in dates)if(dates[i] )
			{
				var d=dates[i];
				 
				if(isNaN(d)===false &&typeof(d) =='object' )//IE9 validate
				{
					valid_dates.push( Ext.Date.dateFormat(d, 'Y/m/d'));
				}

			}
			 
			if(o_sch_wizard.cal.save_mode=='timeslots')
			{
				var rowsSelected = Ext.getCmp(o_sch_wizard.timeslots.grid_id).getSelectionModel().getSelection();
 
				var post={};
				post.dateset_pk=rowsSelected[0].get('dateset_pk');
				post.date_array=Ext.JSON.encode(valid_dates); 
				 
				 Ext.Ajax.request(
				 {
					 url:'index.php/schedule/post_ds_dates/'+App.TOKEN
					 ,params:post
					 ,success:function(o)
					{
						//o_sch_wizard.timeslots.show();
						o_sch_wizard.cal.clear();
						
						o_sch_wizard.cal.hide();
						o_sch_wizard.timeslots.get();

					}
					,failure:App.error.xhr
				 });
 
			}
			else if(o_sch_wizard.cal.save_mode=='matches')
			{
				 
				var rowsSelected = Ext.getCmp(o_sch_wizard.matches.grid_id).getSelectionModel().getSelection();
 
				var post={};
				post.match_pk=rowsSelected[0].get('match_pk');
				post.date_array=Ext.JSON.encode(dates);
				 
				
				//var url='index.php/schedule/post_match_dates/'+App.TOKEN;
				Ext.Ajax.request(
				{
				 	 url:'index.php/schedule/post_match_dates/'+App.TOKEN
				 	 ,params:post
				 	 ,success:function(o)
					 {
						//o_sch_wizard.matches.get();
						o_sch_wizard.cal.clear();
						o_sch_wizard.cal.hide();
						o_sch_wizard.matches.get();//was show
						o_sch_wizard.matches.show();//was show
					}
					,failure:App.error.xhr
				});
 

			}
	    },
	    hide:function()
	    {
 
			if(o_sch_wizard.cal.window==null ){return;}
			 
	    	o_sch_wizard.cal.window.close();
			//Ext.get('wiz-screen-calendar').addCls(o_sch_wizard.hideCls); 
	    },
	    showing:false,
	    show:function()
	    {
 
			//create if it does not exist
			console.log('cal.show');
			console.log(o_sch_wizard.cal.pages);
			console.log(o_sch_wizard.cal.window);
	    	if(!o_sch_wizard.cal.window)
	    		{o_sch_wizard.cal.load();}
	    	else
				{o_sch_wizard.cal.window.show();	}
	    }
	    
    },
    
    games:
    {
 
    	loading:"Remaking games, this may take up to 5 minutes...",
    	//paused:"Paused...",
    	t_start:null,
    	t_response:null,
    	wait:null,
    	get:function()
		{
 			var five_minutes = 300000;//time in MS
			Ext.getCmp(o_sch_wizard.games.grid_id).setTitle('Loading...');
 			o_sch_wizard.games.t_start = new Date().getTime();
			o_sch_wizard.games.wait=Ext.MessageBox.wait(o_sch_wizard.games.loading,'Calculating Schedule');
			
			Ext.Ajax.request(
			{
				success:o_sch_wizard.games.display
				,method:"POST"
				,url:'process.php/schedule/post_schedule_data/'+App.TOKEN
				,timeout:five_minutes
				,failure:function(o)
				{
					o_sch_wizard.games.wait.hide();
					Ext.getCmp(o_sch_wizard.games.grid_id).setTitle('Scheduler timed out, too many Fields and Days');
					App.error.xhr(o);
				}
			}); 
    	},
    	//not used anymroe: abort
    	abort:function()
    	{
 
    	},
    	clear:function()
    	{
    		//clear all data in the grid
			Ext.getCmp(o_sch_wizard.games.grid_id).store.loadData(new Array(),false);
    	},
    	display:function(o)
    	{ 
    		//round to one decimal
    		o_sch_wizard.games.t_response =Math.round(( new Date().getTime() - o_sch_wizard.games.t_start)/100 )/10;
    		o_sch_wizard.games.t_start=null;
 
    		o_sch_wizard.games.wait.hide()
    		//o_sch_wizard.games.ajax=null;
    		o_sch_wizard.saves.save();
    		var r=o.responseText.split('#*#');
			if(r.length<1)
			{
				o_sch_wizard.toolbar.disable_right(true);
				return;
			}
			
			var debug = null;
			var games = null;
			var warnings = null;
			try
			{
				//in case the json.parse fails
				debug = r[0];
				warnings = Ext.JSON.decode(r[1]);
				games    = Ext.JSON.decode(r[2]);
			}
			catch(e)
			{
				App.error.xhr(o);
			}
			finally
			{
				console.log(debug);
				//regardless of json.parse failing or not, continue to trigger everything else
				if(!warnings || warnings=='null'|| warnings.length==0)  warnings = [];
				
				if(warnings.length)
				{
					messages.showError(warnings.join("<br>"));
				}
			 	
				if(!games || games=='null'|| games.length==0)
				{
					games =[];//make it an array
					o_sch_wizard.toolbar.disable_right(true);
				}
				else
				{
					o_sch_wizard.toolbar.disable_right(false);
				} 
				
				o_sch_wizard.loaded.total_games = games.length;
				Ext.getCmp(o_sch_wizard.games.grid_id).store.loadData(games,false);
				
				var newtitle = 'Created '+o_sch_wizard.loaded.total_games+" games in "+o_sch_wizard.games.t_response+' second' ;
				if(o_sch_wizard.games.t_response !=1) newtitle+='s';
				Ext.getCmp(o_sch_wizard.games.grid_id).setTitle( newtitle);
			}
    	},
		dom_id:'wiz-dg-games',
		grid_id:'wiz_games_grid',
		grid:function()//was  
		{
 			 
		    var grid = Ext.create('Spectrum.grids.wizard_games', 
		    {
		    	title: 'Games'
		    	,renderTo: 'wiz-dg-games'
		    	,id:o_sch_wizard.games.grid_id
		    	,bbar:
		    	[
		    		{
            			tooltip:'Refresh'
		                ,text:'Generate Schedule'
		                ,iconCls:'arrow_rotate_clockwise'
						,cls:'x-btn-default-small'
		                ,handler:function()
				        {
		        			o_sch_wizard.games.clear();
		        			o_sch_wizard.games.get();
				        }
				     }
		    	]
 
			});

	    }	
    },
    venues:
    {
		facility_id:-1,//todo: this
 
		facility_btn_id:'wiz_venues_fac_btn',
		timeslot_name:'',
		facility_menu:[],
		selected_ids:[],
		dateset_pk:null,
		grid_id:'wiz_venues_grid',
		grid_display_id:'wiz_venues_display_grid',
		grid:function()
		{
			//create  grid on the left, existing venues 
 
			if(Ext.getCmp(o_sch_wizard.venues.grid_id)) {Ext.getCmp(o_sch_wizard.venues.grid_id).destroy();}
			Ext.create('Ext.spectrumgrids.venue', 
			{
				id:o_sch_wizard.venues.grid_id
				,renderTo:'wiz-dg-venues'
				,collapsible:false
				,height:300
				,rowEditable:true
				,title           : 'Select the venues for Timeslot: '+o_sch_wizard.venues.timeslot_name				
				,selModel: Ext.create('Ext.selection.RowModel', {mode:'MULTI'})//override because i want MULTI, defaults to single
				,topItems:
				[
					{
					    //basic configs
					    xtype:'combobox'
					    ,id:o_sch_wizard.venues.wiz_venues_fac_btn
					    ,value:o_sch_wizard.venues.facility_id
					    ,valueField:'facility_id'					    	
					    ,displayField:'facility_name'
						,valueNotFoundText:'Select Facility'
						,width:200
				    	//flags needed for autocomplete and data
						,typeAhead : true
						, mode: 'local'
						,queryMode: 'local'
						,forceSelection: true
						,triggerAction: 'all'
 						//a store is needed for the template (tpl)
				    	,store:Ext.create('Ext.data.Store',
				    	{
							fields:['facility_id','facility_name','venues_count']
							,data:o_sch_wizard.venues.facility_menu  
				    	})
				    	// display configs
				    	//,colspan:2
				    	 
				    	,tpl:'<tpl for=".">' +
							'<div class="x-boundlist-item">' +
								'{facility_name} ({venues_count}) &nbsp;' +
							'</div></tpl>'
						//events
				    	,listeners:
				    	{
				    		select:function(c,r,o)
				    		{
								var id = r[0].data.facility_id;
								
								o_sch_wizard.venues.facility_id = id;
        						
								if(Ext.getCmp(o_sch_wizard.venues.grid_id)) 
								{
									Ext.getCmp(o_sch_wizard.venues.grid_id).facility_id = id;
									Ext.getCmp(o_sch_wizard.venues.grid_id).store.proxy.extraParams.facility_id = id;
								}
  
        						o_sch_wizard.venues.get_filtered();
				    		}
 
				    	}
					},
				],
				
				bottomLItems:
				[
				"Hold 'Shift' or 'Ctrl' to select multiple"
				]
				,bottomRItems://
				[
					
					{xtype:'button'
					,iconCls:'arrow_right'
					,tooltip:'Assign this venue'
					,handler:function(o)
					{

						var i,s_rows=Ext.getCmp(o_sch_wizard.venues.grid_id).getSelectionModel().getSelection();
						
						o_sch_wizard.venues.selected_ids=new Array();
						//for i in breaks IE
						for(i=0; i<s_rows.length; i++)//if(s_rows[i] )
						{
							o_sch_wizard.venues.selected_ids.push(s_rows[i].get('venue_id'));
						}
						o_sch_wizard.venues.save();
					}
					}
				],
				extraParamsMore : {facility_id:o_sch_wizard.venues.facility_id}
			} );
			 
			 
			if(Ext.getCmp('venues_grid_add_btn_'))
			{
				Ext.getCmp('venues_grid_add_btn_').setDisabled(true);
				Ext.getCmp('venues_grid_add_btn_').hide();
			}
		},
		display:function(o)
		{

			//this is the right hand grid, the assigned venues
			
			try
			{
				var venues=Ext.JSON.decode(o.responseText);
				
			}
			catch(e)
			{
				//in case json_parse fails, we still want an empty array so the code works
				var venues=[];
				//but also display the error
				App.error.xhr(o);
			}
 
			if(Ext.getCmp(o_sch_wizard.venues.grid_display_id))
			{
				Ext.getCmp(o_sch_wizard.venues.grid_display_id).destroy();
			}
			//TODO: spectrumgrids
			var grid = Ext.create('Ext.grid.Panel', 
			{
				title: 'Assigned Fields'
				,renderTo: 'wiz-dg-vselected'
				,id:o_sch_wizard.venues.grid_display_id
				,collapsible: false
		    	,store: []
		    	,stateful: true
		    	,width:"100%"
				,height:300
		    	//height: '90%',
				,selModel: Ext.create('Ext.selection.RowModel', {mode:'MULTI'})
				,columns: 
				[
				   {dataIndex: 'long_name',flex:1,  text:'Name'  }
				   ,{dataIndex: 'venue_id',flex:1, hidden:true, text:''  }

				]//
			
				,bbar:
				[
					{
						tooltip:'Save and Return'
						,iconCls:'disk'
						,handler:function()
						{
							//copied and pasted from toolbar->scheduler, (wiz_tb_btn_scheduler)
							o_sch_wizard.timeslots.get();
							o_sch_wizard.timeslots.show();
							o_sch_wizard.saves.save();	
						}
					}
					,'->'
		    		,{
		    			iconCls:'delete'
		    			,tooltip:'Remove All Selected'
		    			,handler:function(o)
		    			{
		    				
							var s_rows=Ext.getCmp(o_sch_wizard.venues.grid_display_id).getSelectionModel().getSelection();
							
							var ids=new Array();
							for(i in s_rows)if(s_rows[i])
							{ 
								ids.push(s_rows[i].get('venue_id'));
							}
							
							var post={};
							post.dateset_pk=o_sch_wizard.venues.dateset_pk;
							post.venue_array=Ext.JSON.encode(ids);
 
							Ext.Ajax.request(
							{
								url:'index.php/schedule/post_delete_ds_venues/'+App.TOKEN
								,params:post
								,success:function(o)
								{
									o_sch_wizard.venues.get();
								}
								,failure:App.error.xhr
							});
							 
		    			}
		    		}
		    		
				]
			});
			
			
			Ext.getCmp(o_sch_wizard.venues.grid_display_id).store.loadData(venues,false);
			o_sch_wizard.venues.show(); 
			if(o_sch_wizard.venues.facility_id==-1)
				o_sch_wizard.venues.get_facilities();
			  
		},
		
		hide:function()
		{

			Ext.get('wiz-screen-venues').addCls(o_sch_wizard.hideCls); 
			if(Ext.getCmp(o_sch_wizard.venues.grid_id))Ext.getCmp(o_sch_wizard.venues.grid_id).hide();
		},
		show:function()
	    {
 
			//o_sch_wizard.venues.get_facilities();	
	    	//on show, hide otehrs first
	    	o_sch_wizard.audit.hide();
			o_sch_wizard.matches.hide();
			o_sch_wizard.cal.hide();
			o_sch_wizard.finalize.hide();
			//o_sch_wizard.venues.hide();
		
			//doesnt work anyway, might as well hide it
			if(Ext.getCmp('venues_grid_add_btn_'))
			{
				Ext.getCmp('venues_grid_add_btn_').setDisabled(true);
				Ext.getCmp('venues_grid_add_btn_').hide();
			}
			
			
			if(Ext.get('wiz-screen-venues'))Ext.get('wiz-screen-venues').removeCls(o_sch_wizard.hideCls); 
			
			if(Ext.getCmp(o_sch_wizard.venues.grid_id))Ext.getCmp(o_sch_wizard.venues.grid_id).show();
	    	 
	    },
		
		save:function()//o_sch_wizard.venues.hide(true);
		{
			if(o_sch_wizard.venues.dateset_pk==null)
			{
				return;
			}
			var post={};
			post.dateset_pk=o_sch_wizard.venues.dateset_pk;
			post.venue_array=Ext.JSON.encode(o_sch_wizard.venues.selected_ids);
			
				
			Ext.Ajax.request(
			{
				url:'index.php/schedule/post_ds_venues/'+App.TOKEN
				,params:post
				,success:function(o)
				{
					o_sch_wizard.venues.get();
					o_sch_wizard.venues.selected_ids=new Array();
				}
				,failure:App.error.xhr
			});
 
		},
		
		get:function()
		{
			var post={};
			post.dateset_pk=o_sch_wizard.venues.dateset_pk;
 
			Ext.Ajax.request(
			{
				success:o_sch_wizard.venues.display
				,failure:App.error.xhr
				,params:post
				,url:'index.php/schedule/json_ds_venues/'+App.TOKEN
			});
		},
		

		get_facilities:function()
		{  
			Ext.Ajax.request(
			{
				url:'index.php/facilities/json_get_facilities/'+App.TOKEN
				,method:'POST'
				,params:{dist:25}
				,success:function(o)
				{ 
					var fac_data = Ext.JSON.decode(o.responseText);
					 
					//ready for table paginator,  go to root to skip that 
					if(fac_data.root)
						 var fac = fac_data.root; //this is REQURIED since it is returning data 
					else var fac = fac_data;
					
					o_sch_wizard.venues.facility_menu =  fac;
					
				 
					o_sch_wizard.venues.grid();
				}
				,failure:App.error.xhr
				
			});
				 
		},
		
		get_filtered:function()
		{
			var post = {};
			post.facility_id=o_sch_wizard.venues.facility_id;
			
			Ext.Ajax.request(
			{
				params:post
				,url:'index.php/facilities/json_venues_by_fac/'+App.TOKEN
				,success:function(o)
				{
					Ext.getCmp(o_sch_wizard.venues.grid_id).store.loadData(Ext.JSON.decode(o.responseText),false);
						
					//Ext.getCmp('venues_grid_add_btn_').setDisabled(false);
				}
				,failure:App.error.xhr
			});
			
			
		}
    },
    
    
    
    audit:
    {
		type_id:null,
		type_name:'',
		grid_id:'',
		grid_id_list:{tc:'wiz_grid_audit_tc',tv:'wiz_grid_audit_tv',vc:'wiz_grid_audit_vc',dc:'wiz_grid_audit_dc',
			tt:'wiz_grid_audit_tt',td:'wiz_grid_audit_td',dv:'wiz_grid_audit_dv',dd:'wiz_grid_audit_dd',dm:"wiz_grid_audit_dm"
			,gm:'wiz_grid_audit_gm'
			,vv:'wiz_grid_audit_vv'},
		filter_btn_id:'btn_audit_filter',
		get:function()
		{
			if(Ext.getCmp(o_sch_wizard.audit.grid_id)){//if OLD exists, destroy it
				Ext.getCmp(o_sch_wizard.audit.grid_id).destroy();}
			 
			var url=null;
			o_sch_wizard.audit.grid_id = o_sch_wizard.audit.grid_id_list[o_sch_wizard.audit.type_id];
			switch(o_sch_wizard.audit.type_id)
			{
				case 'tc':
					url="index.php/schedule/json_schedule_stats/"+App.TOKEN;
				break;
				case 'tv':
					url="index.php/schedule/json_schedule_join/"+App.TOKEN;
				break;
				case 'vc':
					url="index.php/schedule/json_schedule_vstats/"+App.TOKEN;
				break;
				case 'dc':
					url="index.php/schedule/json_schedule_datestats/"+App.TOKEN;
				break;
				case 'tt':
					url="index.php/schedule/json_schedule_matchstats/"+App.TOKEN;
				break;
				case 'td':
					url="index.php/schedule/json_schedule_teamdate/"+App.TOKEN;
				break;
				case 'dm':
					url="index.php/schedule/json_audit_div/"+App.TOKEN;
				break;
				case 'dd':
					url="index.php/schedule/json_audit_div_date/"+App.TOKEN;
				break;
				case 'dv':
					url="index.php/schedule/json_audit_div_venue/"+App.TOKEN;
				break;
				case 'gm':
					url="index.php/schedule/json_audit_missing/"+App.TOKEN;
				break;
				case 'vv':	//venue - venue audit: for venue distance / team switching
					url="index.php/schedule/json_audit_venue_distances/"+App.TOKEN;
				break;
			}
			
			if(url!=null)
				Ext.Ajax.request({url:url,method:'POST',success:o_sch_wizard.audit.display,failure:App.error.xhr});

		},
		display:function(o)
		{
			var stats=Ext.JSON.decode(o.responseText);
			//remkae the grid every time: this is a dynamic grid
			o_sch_wizard.audit.grid();

			Ext.getCmp(o_sch_wizard.audit.grid_id).store.loadData(stats,false);
			
			o_sch_wizard.audit.show();
		},
 		fmt_diff:function(total)
 		{
 			var msg='';
 			var s='s';
 			if(total==1||total==-1)
 				s='';
 			
			if(total ==0)
			{
				msg = 'Balanced';
			}
			else if(total < 0)
			{
				total=Math.abs(total);//no negatives since we are parsing to english
				msg = total+" fewer home game"+s;
			}
			else//so total > 0
			{
				msg = total+" extra home game"+s;
			}
			return msg;
 		},
		grid:function()
		{
			///   
			if(Ext.getCmp(o_sch_wizard.audit.grid_id))Ext.getCmp(o_sch_wizard.audit.grid_id).destroy();
			var cols=[];
			var fields=[];
			switch(o_sch_wizard.audit.type_id)
			{
				case 'tc':
					fields=['name','home','away','diff','total','team_id','division_id','division_name'];
					cols=
					[
						{text: 'Team',flex:1,sortable : true,dataIndex: 'name'}
						,{text: 'Division',flex:1,sortable : true,dataIndex: 'division_name'}
						,{text: 'Home',flex:1,sortable : true,dataIndex: 'home'}
						,{text: 'Away',flex:1,sortable : true,dataIndex: 'away'}
						,{text: 'Difference',flex:1,sortable : true,dataIndex: 'diff'
						 	,renderer: o_sch_wizard.audit.fmt_diff} //parse number to sentence
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					];
					//            fields:[ "name","home","away","diff","total","id"]
				break;
				case 'tv':
					fields=['team_name','venue_name','total'];
					cols=
					[
						{text: 'Team',flex:1,sortable : true,dataIndex: 'team_name'}
						,{text: 'Venue',flex:1,sortable : true,dataIndex: 'venue_name'}
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					]; 
				break;
				case 'vc':
					fields=['venue_name','total'];
					cols=
					[
						{text: 'Venue',flex:1,sortable : true,dataIndex: 'venue_name'}
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					]; 
				break;
				case 'dc':
					fields=[{name:'date',type:'date',dateFormat:'Y-m-d'},'total'];
					cols=
					[
						{text: 'Date',flex:1,sortable : true,dataIndex: 'date'
							,renderer: Ext.util.Format.dateRenderer('M j, Y') }
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					];
					 
				break;
				case 'tt':
					fields=['home_name','home_count','percent_home','away_name','away_count','percent_away','total'];
					cols=
					[
						{text: 'Home Team',flex:1,sortable : true,dataIndex: 'home_name'}
						,{text: '# Home',flex:1,sortable : true,dataIndex: 'home_count'}
						,{text: '% Home',flex:1,sortable : true,dataIndex: 'percent_home'}
						,{text: 'Away Team',flex:1,sortable : true,dataIndex: 'away_name'}
						,{text: '# Away',flex:1,sortable : true,dataIndex: 'away_count'}
						,{text: '% Away',flex:1,sortable : true,dataIndex: 'percent_away'}
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					]; 
				break;
				case 'td':
					fields=[{name:'date',type:'date',dateFormat:'Y-m-d'},'team_name','total'];
					cols=
					[
						{text: 'Team',flex:1,sortable : true,dataIndex: 'team_name'}
						,{text: 'Date',flex:1,sortable : true,dataIndex: 'date'
								,renderer: Ext.util.Format.dateRenderer('M j, Y') }
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					];
				break;
				case 'dm':
					fields=['h_division_id','a_division_id',
						 'h_division_name','a_division_name',	'total'];
					cols=
					[
						{text: 'Division',flex:1,sortable : true,dataIndex: 'h_division_name'}
						,{text: 'Division',flex:1,sortable : true,dataIndex: 'a_division_name'}
 
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					];
				break;
				case 'dd':
					fields=[{name:'date',type:'date',dateFormat:'Y-m-d'},'division_id','division_name','total'];
					cols=
					[
						{text: 'Division',flex:1,sortable : true,dataIndex: 'division_name'}
						,{text: 'Date',flex:1,sortable : true,dataIndex: 'date'
								,renderer: Ext.util.Format.dateRenderer('M j, Y') }
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					];
				break;
				case 'dv':
					fields=['venue_name','venue_id','division_id','division_name','total'];
					cols=
					[
						{text: 'Division',flex:1,sortable : true,dataIndex: 'division_name'}
						,{text: 'Venue',flex:1,sortable : true,dataIndex: 'venue_name'} 
						,{text: 'Total',flex:1,sortable : true,dataIndex: 'total'}
					]; 
				break;
				case 'gm':
					fields=['home_name','away_name','home_id','away_id'
							,'h_division_name','a_division_name'
							,'h_division_id','a_division_id'
							,'match_pk'];//match pk will help later with finding out where games are coming from
					cols=
					[
						{text: 'Home Team',flex:1,sortable : true,dataIndex: 'home_name'}
						,{text: 'Division',flex:1,sortable : true,dataIndex: 'h_division_name'}
						,{text: 'Away Team',flex:1,sortable : true,dataIndex: 'away_name'}
						,{text: 'Division',flex:1,sortable : true,dataIndex: 'a_division_name'} 
					]; 
				break;
				case 'vv':
					fields=['first_venue'
							,'second_venue'
							,'distance'
							,'bb_teams'
							,'bb_games'
							,'day_teams'];// 
					cols=
					[
						{text: 'Venue of First Game',flex:1,sortable : true,dataIndex: 'first_venue'}
						,{text: 'Venue of Second Game',flex:1,sortable : true,dataIndex: 'second_venue'}
						,{text: 'Distance Between (m)',flex:1,sortable : true,dataIndex: 'distance'}
						,{text: 'Total Back-to-Back Games',flex:1,sortable : true,dataIndex: 'bb_games'} 
						,{text: 'Teams moving Back-to-Back',flex:1,sortable : true,dataIndex: 'bb_teams'} 
						,{text: 'Teams moving same day',flex:1,sortable : true,dataIndex: 'day_teams'} 
					]; 
				break;
			}  
		    var grid = Ext.create('Ext.grid.Panel', 
		    {
		    	id   :o_sch_wizard.audit.grid_id,
		    	title: o_sch_wizard.audit.type_name,
		    	renderTo:'wiz-dg-audit' ,
		    	collapsible: false,
		    	store:  Ext.create('Ext.data.Store',{fields:fields,data:[]}),
		    	
		    	stateful: true,
		    	width:"100%",
		    	height: 250,//height: '90%',

				columns: cols
			
			});
		 
		},
		
		hide:function()
		{
			Ext.get('wiz-screen-audit').addCls(o_sch_wizard.hideCls); 
    		if(Ext.getCmp(o_sch_wizard.audit.grid_id))Ext.getCmp(o_sch_wizard.audit.grid_id).hide();
		},
		show:function()
	    {
	    	//on show, hide otehrs first
	    	o_sch_wizard.timeslots.hide();
			o_sch_wizard.matches.hide();
			o_sch_wizard.cal.hide();
			o_sch_wizard.venues.hide();
			o_sch_wizard.finalize.hide();
		
			Ext.get('wiz-screen-audit').removeCls(o_sch_wizard.hideCls); 
    		if(Ext.getCmp(o_sch_wizard.audit.grid_id))Ext.getCmp(o_sch_wizard.audit.grid_id).show();
			
	    }
    },
    
 
    rules_global:
    { 
		//return form items to be loaded into an existing form. use: items:o_sch_wizard.rules_global.items(),
		record:{},
 
		
		get:function()
		{
			var url='index.php/schedule/json_global_rules/'+App.TOKEN;
			Ext.Ajax.request(
			{
				url:url
				,method:"POST"
				,success:function(o)
				{
					try
					{
						//in case of parse error, throw app.error
						var rules=Ext.JSON.decode(o.responseText);
						o_sch_wizard.rules_global.record=rules;
						o_sch_wizard.rules_global.show();//now show and display the windowform
						
					}
					catch(e)
					{
						App.error.xhr(o);
					}
	 
				}
				,failure:App.error.xhr
			});
			 
		},
 
		window_id:'wiz_rulesgb_window',
		form_id:'wiz_rulesgb_form',
		
		form:function()
		{
			 console.log('rules form');
			var g_form=Ext.create('Spectrum.form.sch_rules',
			{
				id:o_sch_wizard.rules_global.form_id
				,record:o_sch_wizard.rules_global.record
				
			});
			 console.log(g_form);
			//g_form.loadRecord(o_sch_wizard.rules_global.record);
			var w=Ext.create('Ext.spectrumwindows', 
			{
				title: 'Global Rules',
				id:o_sch_wizard.rules_global.window_id,
 				
				width: 470,
				height: 350,
 
				items: g_form
			});
			w.on('hide',function()
			{
				o_sch_wizard.timeslots.get();
			});
			w.show();
		},
		
		
		show:function()
		{
			//get triggers show on success
			//create if it doesnt exist, then show it
			if(Ext.getCmp(o_sch_wizard.rules_global.window_id))
				{Ext.getCmp(o_sch_wizard.rules_global.window_id).destroy();}
			o_sch_wizard.rules_global.form();//create
			
			//before show 
 
			Ext.getCmp(o_sch_wizard.rules_global.window_id).show();
		},
		hide:function()
		{
			if(Ext.getCmp(o_sch_wizard.rules_global.window_id))
				Ext.getCmp(o_sch_wizard.rules_global.window_id).hide();
		}
		
    },
 
 
    
    finalize:
    {
 
    	form_id:'wiz_finalize_form',
    	window_id:'wiz_finalize_window',
    	//total_games:0,
    	get:function()
    	{
			//gets schedule name. and schedule id, use saves.session_id
			var post='session_id='+o_sch_wizard.saves.session_id
    	},
		form:function()
		{
 
			var form= Ext.create('Spectrum.form.sch_finalize', 
			{
				id:o_sch_wizard.finalize.form_id
				,window_id:o_sch_wizard.finalize.window_id
  
			});
			
			Ext.create('Ext.spectrumwindows', 
			{
				title: 'Finalize Schedule: Save and Publish options',
				id:o_sch_wizard.finalize.window_id
 
				,width: 500
				,height: 175
 
				,items: form//the contents of window  are this form
			});
		},
		hide:function(bool)
		{
			if(Ext.getCmp(o_sch_wizard.finalize.window_id))
				{Ext.getCmp(o_sch_wizard.finalize.window_id).destroy()}
			 
			//   'wiz-screen-finalize'
			
		},
		show:function()
	    {

			if(Ext.getCmp(o_sch_wizard.finalize.window_id))
				{Ext.getCmp(o_sch_wizard.finalize.window_id).destroy()}
			o_sch_wizard.finalize.form();	
			Ext.getCmp(o_sch_wizard.finalize.window_id).show();	
			
	    }
		
    },
 
    
    
	formatBool:function(value)
	{
     	 //. ? '<img src="/assets/images/accept.png" />' : '<img src="/assets/images/stop.png" />');
		return value =='t' ? 'Yes' : 'No';
    }
	
    
}

//delete old objects to avoid conflicts
if(o_sch_wizard) 
{
	//o_sch_wizard.destroy();
	delete window.o_sch_wizard;
}
var cal = document.getElementById('ts-multi-cal');
if(cal) 
{
	cal.innerHTML='';
	cal.parentNode.removeChild(cal);
}
var o_sch_wizard = new c_SCHEDULER_WIZARD();
//clear any previously loaded wizard save file, needed since autosave
Ext.Ajax.request(
{	
	url:'index.php/schedule/post_file_clear/'+App.TOKEN
	,method:'POST'
	,success:function(o)
	{
		//init the start form and everytrhing else needed
		o_sch_wizard.init();
		
	}
	,failure:App.error.xhr
});
