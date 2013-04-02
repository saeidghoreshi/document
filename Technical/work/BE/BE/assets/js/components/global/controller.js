//this WAS js2/class.spectrum.js 



//global variables
var toolbar;

var Spectrum = function(){ this.construct(); }

Spectrum.prototype = 
{ 
	
	 
  	facebookObject          :null,//fixed
 
    orgWinHistory           :[],
    activeOrgId             :null,
    activeOrgLogo           :null,
    MAX_TABS_ALLOWED:6,
    
    MAX_GRID_HEIGHT:360,//Max allowed for resolution 1024 x 768
    
    currentTabCount:0,//not including welcome tab
	TOKEN:null,
	loader:{
		filter:"DEBUG",
		base:'http://endeavor.servilliansolutionsinc.com/yui/build/'
	},
	
	construct:function()
	{
		//detect browser right away on construct
 		 
 		
 		//load js files
        var loader = new YAHOO.util.YUILoader({
			require: ['connection','json'],
			base: this.loader.base,
			loadOptional: false,
			scope: this,
			filter:this.loader.filter,
			onSuccess: function()
			{ 
				YAHOO.util.Get.script([
					//get all base classes
                    '/assets/js/classes/googlemaps.js'
                    ,'/assets/js/classes/googlemapsv3.js'
                    ,'/assets/js/classes/spectrumwindow.js'//the real base class
                    ,'/assets/js/classes/Spectrumwindows.js'//depreciated base window class, extends from above. keep it here
                    ,'/assets/js/classes/spectrumforms.js'//base forms
                    ,'/assets/js/classes/spectrumgrids.js'//base grids
                    ,'/assets/js/classes/spectrumtreeviews.js'
                    ,'/assets/js/classes/spectrumdataviews.js'
                    ,'/assets/js/classes/spectrumforms.mixer.js'
                    ,'/assets/js/classes/spectrumforms.mixer2.js'
                    ,'/assets/js/classes/cls.spectrumforms.js'
                    ,'/assets/js/classes/spectrumstore.js' 
                    ,'/assets/js/classes/spectrum.combobox.js' //extend Ext.form.field.ComboBox , simplify auto loading from a URL datasource
                    ,'/assets/js/classes/btn_menu.js' //extension of Ext.menu.Menu, used menu that will be placed inside a button (dropdown)
                    ,'/assets/js/classes/button.js' //extension of Ext.Button
                    ,'/assets/js/classes/store.js' //extension of Ext.data.Store
                    
                    ,'/assets/js/classes/spectrumDataviewSlider.js'
                    ,'/assets/js/components/contentGallery/photoGallery/components/spectrumDataViewSlider.photoGallery.js',
                    

                    //required for getting started winodw and other things
                    ,'/assets/js/components/users/grids/user.js' 
                    
                    //browser prompt that might show before login
					,'/assets/js/components/global/windows/browsers.js'
					//login form
					,'/assets/js/components/global/forms/login.js'
					,'/assets/js/components/global/windows/login.js'               
					
					//??
                    ,'/assets/js/components/global/windows/spectrumwindow.login2.js'
                    
                    //forms from some components are needed globally as well
					,'/assets/js/models/users.js'//,//this is for My User button 
					,'/assets/js/models/role.js'//,/basic roles model 
					
					,'/assets/js/components/users/forms/user_person.js'//,//this is for My User button 
					,'/assets/js/components/users/windows/users.js'//,//this is for My User button 

					,'/assets/js/components/global/forms/multi_address.js'//new my org form
					,'/assets/js/components/global/forms/bugfeature.js'//submit a bug or feature request
					,'/assets/js/components/global/windows/bugfeature.js'//submit a bug or feature request

					//for myOrg button
					,'/assets/js/models/league.js'
					,'/assets/js/models/assoc.js'
					,'/assets/js/components/leagues/forms/create_edit.js'
					,'/assets/js/components/association/forms/createedit_assoc.js'
					,'/assets/js/components/global/forms/basic_org.js'
					,'/assets/js/components/global/windows/my_org.js'
 
				],
				{onSuccess:function()
				{
                    this.init();
				}
				, scope:this 
				});
			}
		}).insert();
	},
	
	init:function()
	{
    	 
 		
    	//in case a browser does not have this defined
		if( typeof(console) =='undefined' || !console || console==null)
		{
			console={};
			//define as null functions: so calls to these will not break in other browsers
			console.log=function(args){};
			console.error=function(args){};
			console.info=function(args){};
			console.warn=function(args){};
		}
		try
		{
			App.browser.name = navigator.appName;// +" "+navigator.appVersion; 		 
 			if(App.browser.name == "Microsoft Internet Explorer") App.browser.isIE = true;
 			
    		App.login.init();
    		 
    		if(App.browser.isIE)
    		{
				// prompt user to switch browser in window /global/windows/browsers.js
				App.browser.prompt();
    		}
    		else
    		{
				App.login.show();				
    		}
		}
		catch(e)
		{
			///IE9 debug catch
			for(i in e){console.log(i,":",e[i]);}
		}
	},
	

	
	browser:
	{
		name:null,
		isIE:false,  
		window_id:'browsers_form_window',
		prompt:function()
		{
			//show the window for browser detection
			var win = Ext.create('Spectrum.windows.browsers',{});
			win.show();
		}
	}, 
	
	login:
	{
		win:null,
		
		init:function()
		{
			var loginForm = Ext.create('Spectrum.forms.login',{});
			App.login.win = Ext.create('Spectrum.windows.login',{items:loginForm});
		},
		
		show:function()
		{
			//kill the browser IE detector window, if its up
			if(Ext.getCmp(App.browser.window_id)){Ext.getCmp(App.browser.window_id).destroy();}
			
			Ext.getCmp('login_form').getForm().reset(); //reset the form
			
			var ad = Ext.getDom('loginad');
			
			if(ad && typeof ad.contentDocument != 'undefined' && typeof ad.contentDocument.location != 'undefined' ) //seems broken in IE9
			{
				ad.contentDocument.location.reload(true); //reload the ad
			}
			App.login.win.show();

			//var el=document.getElementById('google_ads_frame1');//.removeNode(true);
			//el.parentNode.removeChild(el);
		},
		
		hide:function()
		{
			App.login.win.hide();
		},
		
		success:function(json)
		{
            
            var response = YAHOO.lang.JSON.parse(json);
			App.login.hide();
			Ext.get('token').dom.value = response.token;
			App.TOKEN = response.token;
            
            App.activeOrgId = parseInt(response.org_id);			
             			
            
            
			try
			{
				App.initViewport(); //moved here from init, for faster login screen popup //nice BH
				App.initMenu();
				App.AO.get();
				App.updatePanel();
				App.updateWelcome();
				
				App.decideWelcomeWindow(response);
                //Added By Ryan but not in use
                //App.facebookLogoutInterval=setInterval(function(){App.FB.checkFBLogOut()},2000/*310000*/);//after 5'10 min
			}
			catch(e)
			{
				for(i in e){console.log(i,":",e[i]);}
				 
			}			
		}
	},
	      
	AO:
	{
		interval:null,
		get:function()
		{ 
 
			var url='index.php/endeavor/get_active_orgs/'+App.TOKEN;
		    var callback = {success:App.AO.display,scope:this,failure:function(o)
		    {
		    	//the logged out has timedout
		    	if(o.status=='401')//then 'Unauthorized', meaning timed logout
		    	{
					Ext.Msg.alert(
						"Your Spectrum session has timed out"
						, "You have been logged out due to inactivity.  When you are ready, press 'OK' to log in again"
						,function(a)
						{
							App.menuHandlers.logout();
						});
					
					
		    	}
		    	else
		    	{
					App.error.xhr(o);
		    	}
		    	App.AO.interval=null;
		    }};
		    YAHOO.util.Connect.asyncRequest('GET',url,callback);
		    /*
			if(App.AO.interval == null)
			{
				var ao_interval = 500000;
				//.log('setInterval now, should be only once:');
				App.AO.interval = setInterval(function(){App.AO.get();},ao_interval);
			}*/
		}
		
		,post:function(org_id)
		{
			if(App.GS.welcomeWindow) 
            {
                App.GS.welcomeWindow.hide();//added to simulate foake modal stuff
				//if currently showing, then remove it.
				//  because we need the 'on hide' event to trigger.
				//but also destroy it after 
				App.GS.welcomeWindow.destroy();
			}
			
		   var url = 'index.php/permissions/post_active_org/TOKEN:'+App.TOKEN;
			var post = 'org_id='+org_id;

			//Switch Organization happens here
            var callback = 
            {
                success:function(o)
				{
					//(  should be a positive integer on success;
	                App.hideLastOrgTabs(App.activeOrgId  );
	                App.activeOrgId = org_id;
	                App.showExistingOrgTabs();
	                
					App.initMenu();
					App.updatePanel();
					App.updateWelcome();
	                 
					var response = YAHOO.lang.JSON.parse(o.responseText);
					App.decideWelcomeWindow(response);
					
				}
				,failure:App.error.xhr
			};	        
			YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
		}
       
 	   ,first_time:true
		,display:function(o)
		{
			try{
			var roles = YAHOO.lang.JSON.parse(o.responseText);
			}
			catch(e){App.error.xhr(o);return;}
			 
			Ext.getCmp('btn-panel-combo').store.loadData(roles);
			//if(!roles.length){return;}
			if(App.AO.first_time)
			{
				//set the default org value from this fresh user login
				App.AO.first_time = false;
				App.AO._reset();
			}
		}
		/**
		* reset combo to the actual activeorg displaying
		* this should happen AUTO in ext, but there is a bug in 402, fixed in 407 : 
		* http://www.sencha.com/forum/showthread.php?138437-Resetting-the-editable-combobox-to-non-empty-original-value-results-in-empty-val
		* basically we have to call store.clearFilter 
		* before setvalue 
		*/
		,_reset:function()
		{
			//IMPORTANT to have that empty string append - it forces string conversion.  combobox data model has strings for ids
			//however, the App data value has a parseInt called when it is saved, which wont work
			 
			Ext.getCmp('btn-panel-combo').store.clearFilter(false);
			Ext.getCmp('btn-panel-combo').reset();
			Ext.getCmp('btn-panel-combo').setValue(''+App.activeOrgId);
		}
		
 
	},
	
	FB:
    {
        //Check if 5 minutes passed then logout from facebook
        checkFBLogOut:function()  
        {           
            var callback = 
            {
                success:function(o)      
                {                    
                    //if(o.responseText=='1')
                   // {
                        clearInterval(App.facebookLogoutInterval);
                        //Ext.MessageBox.alert({title:"Status",msg:"You're Logged out from Facebook", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});    
                        
                    //}                                                                                                                        
                }
                ,scope:this
            };
            YAHOO.util.Connect.asyncRequest('GET','index.php/home/check_facebook_login_status/'+App.TOKEN,callback);            
        }
    },
    
    /**
    * @access public
    * @author Sam Bassett
    * @date Nov 1, 2011
    * 
    * close whatever tab is active on the main panel
    * so that components never need to know the id of the main panel or have to 
    * access it directly
    */
    closeActiveTab:function()
    {
		var tabpanel = Ext.getCmp('main-tabpanel');
		
		var active = tabpanel.getActiveTab();
		
		//these first two probably do teh exact same thing
		active.setVisible(false);
		active.hide();
		//destroy is to kill memory and ids and dom
		active.destroy();
		
    },

	initViewport:function()
	{ 
		var cw;  
		var noborder=
		{
			'border-style':'none'
		}; 
		noborder['border-width']    = '0px 0px 1px 0px';
 		var panel_width = 250;//do not change
 		var menu_height = 121;
		this.vp = Ext.create('Ext.Viewport', 
		{
			bodyStyle:'background-color:transparent;border-style:none !important;',
			style:noborder,
			frame:false,
			border:false,
		    layout: {
		        type: 'border',
		        padding: 5
		    },
		    
		    defaults: 
		    {
		    	style:noborder,
				
		        split: false
		    },
		    items: [
		    {
		        region: 'center',
		        layout: 'border',
		        bodyStyle:'background-color:transparent;',
		        style:noborder,
		        id:'spectrum-stage',
		        items: [
		        {
					region:'north',
					title:'Spectrum | Online Sports Management',
					html:false,
					height:menu_height,
					style:noborder,
					id:'north-region',
					//border:false,
					split:true
			    }
			    
			    ,{
		            region: 'center',
		            title: false,
		            xtype: 'tabpanel',
		            id:'main-tabpanel',
		            activeTab:0,
		            
					style:noborder,
		           // resizable: true,
				    autoScroll:false,//testing
		            listeners: 
		            { 
		            	//on tab change, update help screen
		            	tabchange:function(o,tab)
		            	{
		            		//these controller,method values were saved inside the tab
		            		// when tab was created
		            		//on initTab, triggered by App.nav
							App.updateHelp(tab.controller,tab.method);
		            	}
		            },
		            plugins: 
		            [{
		                ptype: 'tabscrollermenu',
		                maxText  : 15,
		                pageSize : 5
		            }],
		            items: 
		            [
			            {//the first and only tab that is loaded to start with
							id:"tabwelcome",
							title:"Welcome",
							html:"<center><p><br/><br/><b>Waiting...</b><br/><img src='/assets/images/loading.gif'/></p></center>"
			            }
			        ]
		        }
		        ,{//help screen!!!
		            region: 'south',
		            id:'main-tabpanel-helpscreen',//sam: i added this to work with html_help and updateHelp()
		            height: 255,
					style:noborder,
		            split: true,
		            collapsible: true,
		            collapsed:true,
		            title: 'Help',
		            html: "<div id='div-tapbanel-helpscreen'>No Help file found</div>"//default message. do not change div id !!
		        }]
		    }
		    ,{
		        region: 'east',
		        bodyPadding: 5,
		        collapsible: true,
		        floatable: false,
		        id:'global_panel_account',
		        split: true,
		        width: panel_width,
		        title: 'My Account',
		        tbar:[
		        {
					xtype: 'buttongroup',
					id:'global_main_spectrum_buttongroup',//added for IE9 debugging
				    columns: 3,//sam changed this from 4 to 3, july 12 2011. 
				    rowspan: 3,
				    width: panel_width,
				    title: 'Spectrum&trade;',
				    height:menu_height-20,
				    items: 
				    [
		
					    {
					    	//basic configs
					    	xtype:'combobox'
					    	,id:'btn-panel-combo'
					    	,value:App.activeOrgId
					    	,valueField:'org_id'					    	
					    	,displayField:'org_name'
							,valueNotFoundText:'error'
				    		//flags needed for autocomplete and data
							,typeAhead : true
							, mode: 'local'
						    ,queryMode: 'local'
						    ,forceSelection: true
						    ,triggerAction: 'all'
 							//a store is needed for the template (tpl)
				    		,store:Ext.create('Ext.data.Store',
				    		{
								fields:['org_name','org_id','org_image']
								,data:[] 
				    		})
				    		// display configs
				    		,colspan:2 
				    		,tpl:'<tpl for=".">' +
								'<div class="x-boundlist-item">' +
									'<image src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/{org_image} />'+
									'{org_name}&nbsp;' +
								'</div></tpl>'
							//events
				    		,listeners:{
				    			select:function(c,r,o)
				    			{
									App.AO.post(r[0].data.org_id);
				    			}
				    			,blur:function(f,o)
				    			{ 
									App.AO._reset();
				    			}
				    		}
					    },
					    {
				    		text: 'Set as<br/>My Home<br/>Organization'
				    		, iconCls: 'fugue_home backcenter'
				    		,rowspan:3
				    		,colspan:1 
					    	,iconAlign: 'top' 
					    	,textAlign:'center'
							, handler:App.menuHandlers.postDefaultOrg
							 	///Sam moved this method into menuHandlers, for consistency with other buttons
					    }
					    //second row

					    ,{ 
					    	text: 'Logout', 
					    	iconCls: 'key_stop', 
					    	handler:App.menuHandlers.logout
					    }
					    ,{
					    	text: 'About' //was About Spectrum
					    	,iconCls: 'fugue_information-balloon'
					    	,cls: 'x-btn-as-arrow'
				    		,handler:App.menuHandlers.about
					    }
					    //third row
 					    ,{ 
					    	text: "My Org"
					    	,iconCls:'fugue_briefcase--pencil'
					    	,handler:App.menuHandlers.myOrg
					    }
					    ,{ 
					    	text: "My User"
					    	,iconCls:'fugue_user-white'
					    	,handler:App.menuHandlers.myUser
					    }
 
				    ]
				}]
		    }
		    
		    ]
		});
	},
	
	initMenu:function()
	{
		if(this.menu) this.menu.destroy();
		var url = "/index.php/endeavor/get_menubar";
		var callback = {success:this.buildMenu,scope:this};
		YAHOO.util.Connect.asyncRequest('POST',url,callback,'');
	},
		
	buildMenu:function(o)
	{
		var json = YAHOO.lang.JSON.parse(o.responseText);
		var links = [];
		//.log(json);
        var dobug=true;
		for(x in json) if(json[x]) //cleans up IE9
		{
            if(typeof json[x]['menu_label'] == 'undefined') {continue;}//FOR IE9
			
			var itemsinlvl1 = 0;
			var disableditems = 0;
			
			//high level button groups
			var item1 = {
				xtype: 'buttongroup',
				columns: json[x]['menu_colspan'],
				title: json[x]['menu_label'],
				collapseDirection:'left',
				collapsible:true
			};
			
			//add level 2 links
			 
			if( typeof json[x]['items']== 'object'&&json[x]['items'] != null
				//&& items in json[x]//added for IE9. an array is an object, but so is null
				&& json[x]['items'].length > 0)
			{
				
				item1.items = [];
				if(dobug)//always put bug in teh first element of the first spot
				{
					dobug=false;
					
					var item2 = {
						text: 'Bug or Feature',
						iconCls: 'bug',
						handler:App.menuHandlers.submitBugFeature
					}
 
					item1.items.push(item2);
					itemsinlvl1++
				}
				
				for(y in json[x]['items'])
				{

					var lvl2 = json[x]['items'][y];
					if(typeof lvl2['menu_label'] == 'undefined') {continue;}//FOR IE9
					//default item
					var item2 = {
						text: lvl2['menu_label'],
						iconCls: lvl2['image']
					}
					
					//add column, rowspan if required
					if(lvl2['menu_rowspan']>1) item2.rowspan = lvl2['menu_rowspan'];
					if(lvl2['menu_colspan']>1) item2.colspan = lvl2['menu_colspan'];
					
					//medium icon attributes for rowspanning icon
					if(lvl2['menu_rowspan'] > 1)
					{
						item2.scale = 'medium';
						item2.iconAlign = 'top';
					}
					
					//disabled unavailable items
					item2.disabled = false;
					if(lvl2['allowed']=='f' && lvl2['menu_default']=='f')
					{
						item2.disabled = true;
						disableditems++;
					}
					
					if(item2.disabled==false && lvl2['type_id'] != 3)
					{
						//create window handler
						if(lvl2['window_controller'])
						{
							var c = lvl2['window_controller'];
							var m = lvl2['window_method'];
							var id = lvl2['id'];
							item2.handler = Ext.bind(App.nav, this, [c,m,'','',id]);
						}
						
						//create custom handler
						if(lvl2['handler_id'])
						{
							var h_method=lvl2['handler_method'];
							item2.handler=App.menuHandlers[h_method];
						}
					}
					
					//search for next level items
					if(lvl2 && typeof lvl2['items'] != 'undefined'//extra error checks for IE
					  &&lvl2['items'].length > 0 && lvl2['allowed']=='t')
					{
						var lastGroup = 0;
						if(item2.scale=='medium') item2.arrowAlign = 'bottom';
						item2.cls = 'x-btn-as-arrow';
						item2.menu = [];
						item2.xtype = (lvl2['type_id']==1) ? 'splitbutton' : 'button';
						
						var itemsinlvl3 = 0;
						for(z in lvl2['items'])
						{
							var lvl3 = lvl2['items'][z];
							
							if(typeof lvl3['menu_label'] == 'undefined') {continue;}//FOR IE9
							
							if(lastGroup != lvl3['menu_group'])
							{
								//if(lastGroup>0) item2.menu.push({xtype:'menuseparator'});//SB: removed its ugly
								lastGroup = lvl3['menu_group'];
							}
							
							//create default link
							var item3 = {
								text: lvl3['menu_label'],
								iconCls: lvl3['image']
							}
							
							//disabled unavailable items
							item3.disabled = false;
							if(lvl3['allowed']=='f' && lvl3['menu_default']=='f') continue;
							
							//add window controller if not disabled
							if(lvl3['window_controller'] && item3.disabled==false)
							{
								var c = lvl3['window_controller'];
								var m = lvl3['window_method'];
								var id = lvl3['id'];
								item3.handler = Ext.bind(App.nav, this, [c,m,'','',id]);
							}
							
							item2.menu.push(item3);
							itemsinlvl3++
						}
						
						if(itemsinlvl3==0) item2.disabled = true;
						
					}
					
					item1.items.push(item2);
					itemsinlvl1++
				}
				
			}
			
			//if(disableditems == itemsinlvl1) item1.collapsed = true;
			links.push(item1);
		}
		
		this.menu = Ext.create('Ext.toolbar.Toolbar', 
		{
			style:
			{
				'border-style':'none'
			}
			,items:links
		});
		var north = Ext.getCmp('north-region');
		north.add(this.menu);
	},
	
	updatePanel:function()
	{
		YAHOO.util.Connect.asyncRequest('POST','index.php/endeavor/html_panel',{success:this.displayPanel,scope:this},'');
		
        //getLogo ?? this just stores the logo as a string, does not really affect the HTML of the panel, so its redundant
        YAHOO.util.Connect.asyncRequest('get','index.php/endeavor/json_getactive_org_logo',{success:function(o)
        {
            App.activeOrgLogo =  o.responseText;
        },scope:this},'');
	},
	
	displayPanel:function(o)
	{
		var html = o.responseText;
        //added error checking for IE compatibility
 
		if(Ext.getCmp('global_panel_account') && typeof Ext.getCmp('global_panel_account').update=='function')
		{
			Ext.getCmp('global_panel_account').update(html);
		}
	},
	hidePanel:function()
	{
		Ext.getCmp('global_panel_account').collapse();
	},
 
	updateWelcome:function(o)
	{
		this.nav('endeavor','html_welcome','','welcome');
	},
	
	/**
	* @string c Controller Name
	* @string m Method Name
	* @string p Additional Parameters : @array p Array of Additional Parameters
	* @string d Dimensions: w,h,x,y,max w, max h, min w, min h (depreciated)
	* @string arguments[4] Window Id to replace (now argument 3... leaving argument 4 for backward compatibility)
	*/
	nav:function(c,m,p)
	{
		//.log(c+","+m);//SB: added for help screan data, remove later 
    	if(App.currentTabCount >= App.MAX_TABS_ALLOWED)// SAM: added to stop tab overflow error. maximum number of tabs allowed check
    	{
			Ext.Msg.alert('Too Many tabs Open',"Only "+App.MAX_TABS_ALLOWED+" tabs can be open at one time, please close some first");
			return;
    	}
    	
		//get or generate tab id                                         
		switch(arguments.length)
		{
            
			default:
			case 3:
				var id = 'window_'+Math.floor(new Date().getTime()/1000);
                break;
			case 4:
			case 5:
				var id = arguments[arguments.length-1];
                break;
		}
		
		//create tab ->updated to save method and controller method for help
		this.initTab(id,c,m,p);
		
        //Transfered Under initTab BY RYAN
		//send request
		/*
        if(p instanceof Array) p = p.join('/');
		var url = "/index.php/"+c+"/"+m+"/TOKEN:"+this.TOKEN+"/"+id+"/"+p;
		var callback = {success:this.display,failure:App.error.xhr,scope:this,argument:[id]};
		YAHOO.util.Connect.asyncRequest('GET',url,callback,'');
		*/
		        
		this.updatePanel();
		
		//return id for custom calls
		return id;
	},
	
	updateHelp:function(c,m)
	{
		var url='index.php/endeavor/html_help/'+App.TOKEN;
		var post='cname='+c+'&mname='+m;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,failure:App.error.xhr,success:function(o)
		{
			var html = o.responseText;
			
			
			//this is the panel object, not currently used. 
			//this is the object we woudl call .collapse() on, for example
			var tab = Ext.getCmp('main-tabpanel-helpscreen');
			
			//this is the raw html dom  element container
			//it is where we put the visible html
			var div = Ext.get('div-tapbanel-helpscreen').dom;
			
			//set the help file contents here
			div.innerHTML=html;
			
			
		}},post);
		
		
	},
	
	display:function(o)
	{
		try
		{					
			var json = YAHOO.lang.JSON.parse(o.responseText);
		}
		catch(e)
		{			
			App.error.xhr(o);
			return;	
		}
		
		var tab = Ext.getCmp('tab'+o.argument[0]);
		
		if(json.HTML)
		{
			tab.setTitle(json.WINDOW.header);
			tab.update(json.HTML);
			//tab.setFooter(json.WINDOW.footer);
		}
		
		if(json.JS)
		{
			var scripts = json.JS;
			YAHOO.util.Get.script(json.JS, {onSuccess:function(o){
				for(x in o.nodes)
				{
					//init toolbar for object
					var JS = o.nodes[x].src;
					if(typeof JS != 'undefined' && typeof JS.indexOf == 'function'//added for IE9 
					    &&JS.indexOf('toolbar.js')!=-1)
					{
						if(typeof(toolbar) == 'undefined' || toolbar==null ) {continue;}//added in case toolbar.js is broken
						toolbar.id = o.data.id+'_toolbar';
						var _old_toolbar = Ext.getCmp(toolbar.id);
						//typeof is not a reserved word: fixed it for IE/chrome
						if(typeof _old_toolbar!='undefined') _old_toolbar.destroy();
						var _toolbar = Ext.create('Ext.toolbar.Toolbar', toolbar);
						tab.add(_toolbar);
						tab.doComponentLayout();
						toolbar=null;//task 1389: facilities was getting scheduler toolbar, adn other things silly like that
					}
				}
			},data:{id:'tab'+o.argument[0]},scope:this});
		}

		if(json.CSS) 	YAHOO.util.Get.css(json.CSS);
		//if(json.JS)		YAHOO.util.Get.script(json.JS);
	},
	//Added by Ryan  ==>  Handeling and TAbs Histoty Keeping of Switching orgs
    /*
        Structure:
        orgWinHistory=
        [
            {org_id:x1,org_name:n1,openList:[{tab_id:t1},{tab_id:t2},...]},
            {org_id:x2,org_name:n2,openList:[{tab_id:t3},{tab_id:t4},...]},
            ......
        ]
    */
    updateOrgWinHistory:function(org_id,tab_id)
    {       

        //if Orgs other than the specific org/tab try to open the similar tab
        for(var i in App.orgWinHistory) if(App.orgWinHistory[i])//for IE9
            for(var j in App.orgWinHistory[i].openList)
                if(App.orgWinHistory[i].org_id!=App.activeOrgId && App.orgWinHistory[i].openList[j].tab_id==tab_id)
                    return false;                                
                                                                                              
        var sw=false;
        for(var i in App.orgWinHistory)if(App.orgWinHistory[i])//for IE9
            if(App.orgWinHistory[i].org_id==org_id)//if matches existing org_id index then if passes redundency checks, adds to the History
            {
                if(tab_id!="tabwelcome")
                {             
                    //Dont allow Duplicate Entries
                    for(var j in App.orgWinHistory[i].openList) if(App.orgWinHistory[i])            
                        if(App.orgWinHistory[i].openList[j].tab_id==tab_id)
                            sw=true;                                       
                    //Dont allow Duplicate Entries
                    if(sw==true)break;
                    
                    App.orgWinHistory[i].openList.push({"tab_id":tab_id});
                    sw=true;    
                }       
            }
        
        //Open an entry for NEW ORG
        var box = Ext.MessageBox.wait('Please wait while processing ...', 'Loading'); 
        Ext.Ajax.request(
        {
            url: 'index.php/permissions/json_get_active_org_and_type/'+App.TOKEN,
            params: {test:'test'},//it fails without this: because without it EXT ads ?_dc=random, which breaks the token or something
            success: function(o)
            {
                box.hide();
                try{
                var res=YAHOO.lang.JSON.parse(o.responseText);
                
                if(sw==false)  ///If there is no existing similar org then create new one
                {
                    if(tab_id!="tabwelcome")
                    {
                        App.orgWinHistory.push({"org_id":org_id,"org_name":res.result.org_name,"openList":[{"tab_id":tab_id}]});
                    }
                }
				}
				catch(e)
				{
					//if php or db or server error occurs, response will crash JSON.parse, so handle this here
					//added for task 1555
					App.error.xhr(response);

				}
            }
            ,failure:function(o)
            {
                //SB: added for task 1555, so that box can hide even if this fails
				box.hide();
				//App.error.xhr(o);
            }
        });    
            
        return true; 
    },
    removeOrgWinHistory:function(org_id,tab_id)
    {
        for(var i in App.orgWinHistory)
            if(parseInt(App.orgWinHistory[i].org_id)==parseInt(org_id))
            {
                for(var j in App.orgWinHistory[i].openList)                 
                    if(App.orgWinHistory[i].openList[j].tab_id==tab_id)    
                    {
                        App.orgWinHistory[i].openList.splice(j,1);
                        break;
                    }                                                       
            }       
    },
    hideLastOrgTabs:function(lastOrgId)
    {
    	//internet explorer says "SCRIPT5007: Unable to get value of the property 'setDisabled': object is null or undefined 
    	//fixed nov 28 2011

    	var i,j,tab;//uses less memory for IE
        if(lastOrgId==null)return;
        for( i in App.orgWinHistory)if(App.orgWinHistory[i]) //added for IE null error check
        {                                                                       
            if(App.orgWinHistory[i].org_id==lastOrgId)
            {                         
                for( j in App.orgWinHistory[i].openList)
                {
					tab=Ext.getCmp(App.orgWinHistory[i].openList[j].tab_id);
					if(tab && typeof tab.setDisabled=='function') { tab.setDisabled(true); }//SB: added for weird IE errors
					
					
					
                }
			}
                     
        }       
    },
    showExistingOrgTabs:function()
    {
    	var i,j,tab;
        if(App.activeOrgId==null)return;
        for( i in App.orgWinHistory)if(App.orgWinHistory[i])//IE null check
        {                                                                       
            if(App.orgWinHistory[i].org_id==App.activeOrgId)
            {
                for( j in App.orgWinHistory[i].openList)
                {
                    tab=Ext.getCmp(App.orgWinHistory[i].openList[j].tab_id);
					if(tab && typeof tab.setDisabled=='function') { tab.setDisabled(false); }//SB: added for weird IE errors
					
                }
				
            }
        }       
    },
    findExistingTabOrgId:function(id)
    {
        var containingOrgId=null;
        for(var i in App.orgWinHistory)
            for(var j in App.orgWinHistory[i].openList)
                if(App.orgWinHistory[i].openList[j].tab_id=="tab"+id)
                    containingOrgId=App.orgWinHistory[i].org_id;    
        return containingOrgId;
    },
    findExistingTabOrgName:function(id)
    {
        var containingOrgName=null;
        for(var i in App.orgWinHistory)
            for(var j in App.orgWinHistory[i].openList)
                if(App.orgWinHistory[i].openList[j].tab_id=="tab"+id)
                    containingOrgName=App.orgWinHistory[i].org_name;    
        return containingOrgName;
    },
	initTab:function(id,c,m,p)
	{                      
		//sam: some minor changes to fix teh 'no' bug, task 1410
		//     now 'result1', 'result2' is not used, because ext.confirm is asynchronos, that value was never set by the time
		//it was used. we have to call the ajax INSIDE the .confirm yes branch. ported taht over to
		//new method loadTabData
		
		var stage = Ext.getCmp('main-tabpanel');
		var tab = Ext.getCmp('tab'+id);
        
		if(typeof tab=='undefined')//changed for the better using typeof operator
		{ 
			App.updateOrgWinHistory(App.activeOrgId,"tab"+id);//update this tab in the history
 			//we know tab is new, no need to prompt it will nevr happen
 			//create the tab
            var tab = stage.add(
            {
                closable    :true,
                title       :"Loading...",
                html        :"Loading...",
                id          :"tab"+id,
                controller  :c,//save for help file
                method      :m
                
            });
            //we are adding a new tab so update counter
            App.currentTabCount++;
            
            tab.on("close",function()
            {
                App.removeOrgWinHistory(App.activeOrgId,tab.id);
                App.currentTabCount--;//keep counter up to date
            });
 			
			stage.setActiveTab(tab);
			App.loadTabData(id,c,m,p);//sends ajax using controler and method
 			
		} 
        else 
        {
            var active_org_name=App.findExistingTabOrgName(id);
            
            if(App.updateOrgWinHistory(App.activeOrgId,"tab"+id)==false)
            {
 
				//problem: confirm is ASYNCHRONOUS, so cannot rely in for setting usable boolean
                Ext.MessageBox.confirm('Warning', "This  screen is already open for '"
	                +active_org_name+"'. Opening this tab for this organization will lose all changes made from '"
	                +active_org_name+"'. Do you still wish to open this tab?", function(answer)
	                {     
                       if(answer=="yes") 
                       {
 
                           var existingTabOrgId=App.findExistingTabOrgId(id);
                                                       
                           App.removeOrgWinHistory(existingTabOrgId,"tab"+id);
                           App.updateOrgWinHistory(App.activeOrgId,"tab"+id);
                            
				            
						    tab.setTitle('Loading...');
						   //tab.update('Loading...');
							App.loadTabData(id,c,m,p);//send ajac here not outside confirm handler
							//after we load, enable teh tab
							tab.setDisabled(false);
                                                   
							stage.setActiveTab(tab);
			           }
 					   //else answer == no, so do nothing
                  });          
			}
			else
			{
				//brand new tab is TRUE
				stage.setActiveTab(tab);
				App.loadTabData(id,c,m,p);
			}
		}//end ELSE branch
	},
	
	loadTabData:function(id,c,m,p)
	{
		
		if(p instanceof Array) p = p.join('/');
		var url = "/index.php/"+c+"/"+m+"/"+this.TOKEN+"/"+id+"/"+p;
		var callback = {success:this.display,failure:App.error.xhr,scope:this,argument:[id]};
		YAHOO.util.Connect.asyncRequest('GET',url,callback,'');
	},
	setStatus:function(msg, target)
	{	
		//variables
		//if(typeof target=='undefined') console.error('No Target');
		var target = (arguments.length > 1) ? arguments[1] : Ext.getCmp('main-tabpanel').getActiveTab();
		var img = (arguments.length > 2) ? arguments[2] : false;
		
		//add image
		switch(img)
		{
			case 'loading':
				msg = '<img src="/assets/images/ajax-loader.gif" align="left"/> '+msg;
				break;
		}
		
		//create statusbar in tab
		var statusbar = Ext.getCmp(target.id+"_statusbar");
		//UNDEFEIND not a reserved word. fixed for chrome / ie
		if(typeof statusbar!='undefined') statusbar.destroy();
		statusbar = Ext.create('Ext.toolbar.Toolbar', {id:target.id+"_statusbar", dock:'bottom',items:[msg]});
		
		//add statusbar
		target.addDocked(statusbar);
	},

	error:
	{
		xhr:function(o)
		{ 
			try
			{
				var r=YAHOO.lang.JSON.parse(o.responseText);
				
				if(typeof r.result != 'undefined' && r.success===false)
				{
					//assume that if r.success=true, AND parse is safe
					
					// r.result actually exists, so display it
					var title = 'Action Denied ';
					//in case o.status is /undefined/ then dont show it 
					if(o.status) title += '('+o.status+":"+o.statusText+')';
					Ext.Msg.alert(  title, r.result);
					return;
			
				}
				//else no failure code given ,or no result,
				// continue as normal
				//and this will be handled in Finally
			
			}
			catch(e)
			{
				 
			/*}
			finally
			{*/
				//if we cannot aprse the response, continue as normal	
				
				//in future, we could add a SWITCH CASE statement on status
				if( o.status==='0')
				{
					//special case for connection problem
					
					Ext.Msg.alert("Internet connection problem",
						"Spectrum could not reach the internet.  Please check your internet connection.  If the problem persists, "
						+"contact your service provider.");
					return;
				}
				else if(o.status==='-1')
				{
					Ext.Msg.alert("Connection problem",
						"The connection is very slow, or our servers just took too long to respond.");
					return;
				}
				//default case here.
				
				Ext.Msg.alert(o.status+":"+o.statusText, o.responseText);
				
			}
		}
	},
	
	menuHandlers:
	{
		logout:function()
		{
			//destroy token and start over
			var url='index.php/permissions/logout';
			YAHOO.util.Connect.asyncRequest('GET',url,{success:function(o)
			{
				App.TOKEN=null;
				if(App.AO.interval) {App.AO.interval=clearInterval(App.AO.interval);}
                
                if(window.location.toString().indexOf('#')!=-1)
                    window.open(window.location.toString().substring(0,window.location.toString().indexOf('#')),'_self');
                else
				    window.location.reload();			
			}})						
		},
		about:function()
		{
			//TODO: get this from the views/endeavor/about.php html page,, but this not working for me
			//so for now i copied it in, adding the '\' at end of each line to escape newline char/
			//Sam
			
			var msg='<div style="padding:5px;">\
	<h1 style="color:#003366;">Spectrum &reg;</h1>\
	<span style="color:#333333;font-weight:bold;">\
		&copy; 2011 Servillian Solutions Inc.<br/>\
		<br/>\
	</span>\
	<br/>\
	<br/>\
	<table cellpadding="0" cellspacing="0" width="100%">\
		<tr><td>Product Information</td><td><a href="http://playerspectrum.com/" target="_blank">playerspectrum.com</a></td></tr>\
		<tr><td colspan="2"><b>Developed By:</b></td></tr>\
		<tr><td>Servillian Technology Ltd</td><td><a href="http://www.servillian.com" target="_blank">www.servillian.com</a></td></tr>\
		<tr><td colspan="2"><br/><b>Developers:</b></td></tr>\
		<tr><td>Bradley Holbrook</td><td>Development Director</td></tr>\
		<tr><td>Allan Holbrook</td><td>Developer</td></tr>\
		<tr><td>Ryan Goreshi</td><td>Developer</td></tr>\
		<tr><td>Samson Bassett</td><td>Developer</td></tr>\
		<tr><td colspan="2"><br/><b>Contributers:</b></td></tr>\
		<tr><td>Debra Holbrook</td><td>Project Manager</td></tr>\
		<tr><td>Bill Burrows</td><td>Sales</td></tr>\
		<tr><td colspan="2"><br/><b>Special Thanks To:</b></td></tr>\
		<tr><td>Terry Sibbick</td><td>President, NSA Canada</td></tr>\
		<tr><td>Ewan Webster</td><td>Business Manager, NSA Canada</td></tr>\
		<tr><td>Diana Vietch</td><td>NSA Canada</td></tr>\
		<tr><td>Mark James</td><td>Silk Icon Set</td></tr>\
	</table>\
</div>';
			Ext.MessageBox.alert('About Us',msg);
		},
		
		postDefaultOrg:function()
		{  
			//save the current selected org as which org they log in as to start 
			var url = 'index.php/permissions/post_default_org/'+App.TOKEN;
			YAHOO.util.Connect.asyncRequest('POST',url,{failure:App.error.xhr,success:function(o)
			{
				//now this remakes the menu: needed to show new star location in the menu
				App.AO.get();
			}},'');
 
		},
		
		//view and edit user record
		myOrg:function()
		{
			
			//first get active org type with ajax
			
            //get list of Domains
			var domainListStore    =new simpleStoreClass().make(['id','domain'],"index.php/endeavor/json_getDomainNames/"+App.TOKEN+'/',{test:true});        
            
			var url='index.php/endeavor/json_active_org_full_details/'+App.TOKEN;
			var org_type_str="";
			YAHOO.util.Connect.asyncRequest('GET',url,{failure:App.error.xhr,success:function(o)
			{ 
				var window_id = 'my_org_window_frommainmenu';
				 
				var r = YAHOO.lang.JSON.parse(o.responseText);
                
				var org_type = r.org_type;
				var org_id   = r.org_id; 
				var is_system=1;
				var is_assoc=2;
				
				var is_league=3;
				var is_team=6;
				var org_type_str='';
				//we need org type to get the first (top) of the form
				var window_items=new Array();
				var window_title='My Organization';
				//basically this is thee ORG DETAILS form
				if(org_type == is_system)
				{
					 
				 	org_type_str = 'System';
					var f=Ext.create("Spectrum.forms.basic_org",
					{
						url     : 'index.php/endeavor/post_org_name/'+App.TOKEN
						,org_id:org_id
						,org_name:r.org_name
						,title:org_type_str+' Details'
					});
					  
					window_items.push(f);					
				}
				else if(org_type == is_assoc  )
				{
				 	org_type_str = 'Association';
					var f=Ext.create("Spectrum.forms.assoc",
					{ 
						org_id:org_id
						,hide_addr:true//address is done a different way
						,hide_save:true// 
						,org_name:r.org_name
						,title:org_type_str+' Details'
					});
					//turn the data into a record that can be loaded
					var lg = Ext.ModelManager.create(r, 'Assoc');
 
					f.loadRecord(lg);
					window_items.push(f);
 
				}
				else if(org_type == is_league)
				{
					org_type_str="League";
					var f=Ext.create("Spectrum.forms.league",
                    {
                        window_id       : window_id,
                        save_btn        : false,
                        width:300,
                        title:org_type_str+' Details',
						url             : 'index.php/associations/json_createnewleague/'+App.TOKEN,
                        domainListStore : domainListStore
                    });
					
					var lg = Ext.ModelManager.create(r, 'LeagueModel');

					f.loadRecord(lg);


					window_items.push(f);
				}
				else if(org_type == is_team)
				{
					org_type_str="Team";
					var f=Ext.create("Spectrum.forms.basic_org",
					{
						url     : 'index.php/endeavor/post_org_name/'+App.TOKEN
						,org_id:org_id
						,org_name:r.org_name
						,title:org_type_str+' Details'
					});
 
					window_items.push(f);
				}
 
 				
				//in all cases, make TWO address form s
				var ship_addrForm = Ext.create('Spectrum.forms.person_address',
				{ 
				 	title:'Shipping Address',
					id:'my_org_address_ship',//added id so it wont conflict with person address form
					url:'index.php/endeavor/post_org_address/'+App.TOKEN,
					// hide_type: true
					width:355//,disabled:true 
				}); 
				
				ship_addrForm.loadRecord(Ext.ModelManager.create(r['ship'], 'User'));
				
				var oper_addrForm = Ext.create('Spectrum.forms.person_address',
				{   
				 	title:'Operational Address',
					id:'my_org_address_oper',//added id so it wont conflict with person address form
					url:'index.php/endeavor/post_org_address/'+App.TOKEN,
					// hide_type: true
					width:355//,disabled:true 
				}); 
 
				oper_addrForm.loadRecord(Ext.ModelManager.create(r['oper'], 'User'));
 					
				
				window_items.push(ship_addrForm);
				window_items.push(oper_addrForm);
 				
				//then set up the window
				App.dom.w_myOrg = Ext.create("Spectrum.windows.my_org",{id:window_id,items:window_items});
 				App.dom.w_myOrg.on('hide',function(){App.updatePanel();});//refresh panel when done
 				//needed in case logo or org name have changed
 
				App.dom.w_myOrg.show();
				
				if(org_type==is_league)
				{ 
					//moved this to after show and load
					domainListStore.on('load',function()
					{
						var index=domainListStore.find( 'domain', r.domainname , 0, true, false, false);
	                    var rec=domainListStore.getAt(index);
	                    Ext.getCmp('domainlist').select(rec); 
					});
				}
			}});
		},		
		myUser:function(oArgs)
		{
			YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/active_user_record/'+App.TOKEN,
			{success:function(o)
			{
				var form_id='f_global_user';
				var w_id='w_global_user';
				//pass window id to form, so the forms save button can close window
				var form=Ext.create('Spectrum.forms.user_edit',{id:form_id,window_id:w_id});
				var data=YAHOO.lang.JSON.parse(o.responseText);
				
				//cannot just load a plain object,  EXT needs a MODEL
				var user = Ext.ModelManager.create(data[0], 'User');

	            form.loadRecord(user);
	            //form is ready; create and show the window
	            var win = Ext.create('Spectrum.windows.user', {items: form,id:w_id});
				win.show();
			},failure:App.error.xhr})	
		}
		
		
		,submitBugFeature:function(oArgs)
		{
			//this object is defined in the root view
			//spectrum2.php near the </body> tag
			feedback_widget.show();
		}
		
		
		/**
		* this is the old bug feature window, that shows an extjs form , that sends to request@intervals
		*/
		,_old_submitBugFeature:function(oArgs)
		{
			var window_id='bgfeature'+Math.random();
			var f=Ext.create('Spectrum.forms.bugfeature'  ,{window_id:window_id});//use default parameters/id is fine
			var w=Ext.create('Spectrum.windows.bugfeature',{items:f,id:window_id});//use defaults except put the form in there
			w.show();
		}
	},
	
	//ww:false,
	//************************
	// changed from App.ww to App.GS.welcomeWindow
	//*******************
	decideWelcomeWindow:function(response)
	{
 
		App.GS.org_type=response.org_type;
 		 
 		if(response.user_exec)//only consider this if executive role is assigned
 		///such as league exec or team manager
		if( response.org_is_valid==false||response.org_is_valid=='false'||response.org_is_valid=='f' ) 
		{
			//we will show the window for sure
			if(response.last_login_date==''||response.last_login_date==null )
				App.GS.new_user=true;
			else
				App.GS.new_user=false;
				
			this.showWelcomeWindow();
		}
	},
	
	showWelcomeWindow:function()
	{
		try
		{
			App.GS.welcomeWindow = Ext.create('Ext.spectrumwindows',//use our custom base class , as defined in class.form.js
			{
				width:750,
				height:450,
				modal:false,
				y:50,//added to move up 
				closable:false,
				//ownerCt:Ext.getCmp('spectrum-stage'),//so modal does not cover right panel.  remove to make modal /global/ 
				//ownerCt:Ext.getCmp('north-region'),//so modal does not cover right panel.  remove to make modal /global/ 
				title:"Getting Started with Spectrum",
				id:"global_getstarted",
				draggable:false,
				html:'<center><br/><img src="http://playerspectrum.com/templates/spectrum/images/spectrum_tiny.png" height="36"/>'
							+'<br/><br/><b>Loading...</b><br/><img src="/assets/images/loading.gif"/><br/></center>'
			});
			//found a parent / relative modal solution for ext 3.3.3 :
			//http://www.sencha.com/forum/showthread.php?141901-are-modal-windows-inside-a-container-a-bad-idea&p=630526
			//http://stackoverflow.com/questions/6974624/are-modal-windows-in-a-container-hierarchy-a-bad-pattern
			//but it DOES NOT WORK IN 4.0. so:::::::::

			//SIMULATE the modal property with this
			App.GS.welcomeWindow.on('hide',function()
			{
				Ext.getCmp('spectrum-stage').enable();
				Ext.getCmp('north-region').enable();
				
			});
			App.GS.welcomeWindow.on('show',function()
			{
				Ext.getCmp('spectrum-stage').disable();
				Ext.getCmp('north-region').disable();
				
			});			
			App.GS.welcomeWindow.show();
			//this.ww = ww;
		}
		catch(e)
		{
			for(i in e) console.log(i,":",e[i]);//for IE debug
		}
		
		this.requestGetStartedFramework();
	},
	
	activeGSCSS:false,
	requestGetStartedFramework:function()
	{
		//css file
		if(App.activeGSCSS!=false) App.activeGSCSS.purge();
		var time = Math.floor(new Date().getTime() / 1000);
		YAHOO.util.Get.css('/assets/css/getstarted/getstarted.css?T'+time,{onSuccess:function(o){App.activeGSCSS=o},scope:this});
		
		//get GS framework
		var url = "/index.php/getstarted";//loads slide 1 by default
		var callback = {scope:this,success:this.initGetStarted}
		var post = "";
		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
	},
	
	
	initGetStarted:function(o)
	{
		//clear open intervals
		if(this.DYK.interval!=false) clearInterval(this.DYK.interval);
		
		//parse html
		var json = YAHOO.lang.JSON.parse(o.responseText);
		App.GS.welcomeWindow.update(json.html, false, function(){
			App.GS.init();
			App.DYK.init();
		});
	},
	
	GS:
	{
		new_user:true,
		isAnimating:false,
		animDuration : 750,
		animInEffect : 'easeOut',
		animOutEffect : 'easeOut',
		isLoading:true,
	
		activeSlide:null,
		
		welcomeWindow:null,
		activeForm:null,
		activeGrid:null,
		
		org_type:null,
		LEAGUE:3,ASSOC:2,TEAM:6,
		
		slideEnum:
		{
			//matches the defined constants in constants.php and getstarted.php
			WELCOME:1,PASSWORD:2,BEFORE:3,PERSON:4,ADDRESS:5,CONTACT:6
			,ORGDETAILS:7,ORGADDRESS:8,USERS:9
			,SIGN:10,BANK:11,SUMMARY:12,PAYMENT:13
			,TERMS:14,
			FINAL:15
		},
		
		btns:{},
		slideOrder:[],

		
		//slideOrder is a mono direction linked list

		init:function()
		{
			App.GS.slideOrderSetup();
			App.GS.activeForm=new Array();
			App.GS.activeGrid=new Array();
			App.GS.btns.refresh = Ext.create('Ext.button.Button',{
				text	:'Refresh',
				renderTo:'gs-btn-refresh',
				handler	:App.requestGetStartedFramework,
				iconCls	:'arrow_rotate_clockwise'        ,hidden:true
			});
			App.GS.btns.next = Ext.create('Ext.button.Button',{
				text	:'Next',
				renderTo:'gs-btn-next',
				handler	:App.GS.next,
				iconCls	:'resultset_next'         ,iconAlign:'right'//defaults to 'left' 
			});
			App.GS.btns.prev = Ext.create('Ext.button.Button',{
				text	:'Back',// was 'Prev'
				renderTo:'gs-btn-prev',
				handler	:App.GS.prev,
				iconCls	:'resultset_previous',
				disabled:true
			});
			
			App.GS.getForm(App.GS.findNext(),'next');
		},
		slideOrderSetup:function()
		{
			var START=0;
			
			App.GS.slideOrder[START]=App.GS.slideEnum.WELCOME;
			var END=App.GS.slideEnum.WELCOME;//temporary end
			//App.GS.slideOrder[App.GS.slideEnum.WELCOME] =END;//prefix ends here, check next type			
			//.log(App.GS.org_type);
			//.log(App.GS.LEAGUE);
			if(App.GS.new_user) 
			{
				// now we add the user slides
				App.GS.slideOrder[END]=App.GS.slideEnum.PASSWORD;
				//from password, to the before slide
				App.GS.slideOrder[App.GS.slideEnum.PASSWORD]=App.GS.slideEnum.BEFORE;
				App.GS.slideOrder[App.GS.slideEnum.BEFORE]  =App.GS.slideEnum.PERSON;
				App.GS.slideOrder[App.GS.slideEnum.PERSON]  =App.GS.slideEnum.ADDRESS;
				App.GS.slideOrder[App.GS.slideEnum.ADDRESS] =App.GS.slideEnum.CONTACT;
				END = App.GS.slideEnum.CONTACT;
			}
			//league and assoc happen t obe the same, for now. some differences in teh view
			if(App.GS.org_type==App.GS.ASSOC )
			{
				App.GS.slideOrder[END]  =  App.GS.slideEnum.ORGDETAILS;
				App.GS.slideOrder[App.GS.slideEnum.ORGDETAILS]=App.GS.slideEnum.ORGADDRESS;
				App.GS.slideOrder[App.GS.slideEnum.ORGADDRESS]=App.GS.slideEnum.USERS;
				//App.GS.slideOrder[App.GS.slideEnum.USERS     ]=App.GS.slideEnum.SIGN;
				//App.GS.slideOrder[App.GS.slideEnum.SIGN      ]=App.GS.slideEnum.BANK;
				//skip signing authority: is casuing problems, and not yet ready for 2.0: finance all moved to 2.1
				
				
				
				
				App.GS.slideOrder[App.GS.slideEnum.USERS     ]=App.GS.slideEnum.BANK;
				
				
				App.GS.slideOrder[App.GS.slideEnum.BANK]      =App.GS.slideEnum.SUMMARY;
				END =App.GS.slideEnum.SUMMARY;
			}
			else if(App.GS.org_type==App.GS.LEAGUE)
			{
				App.GS.slideOrder[END]  =  App.GS.slideEnum.ORGDETAILS;
				App.GS.slideOrder[App.GS.slideEnum.ORGDETAILS]=App.GS.slideEnum.ORGADDRESS;
				App.GS.slideOrder[App.GS.slideEnum.ORGADDRESS]=App.GS.slideEnum.USERS;
				//App.GS.slideOrder[App.GS.slideEnum.USERS]     =App.GS.slideEnum.SIGN;
				//App.GS.slideOrder[App.GS.slideEnum.SIGN]      =App.GS.slideEnum.BANK;
				//skip signing authority: is casuing problems
				
				
				
				
				
				App.GS.slideOrder[App.GS.slideEnum.USERS     ]=App.GS.slideEnum.BANK;
				App.GS.slideOrder[App.GS.slideEnum.BANK]      =App.GS.slideEnum.SUMMARY;
				END =App.GS.slideEnum.SUMMARY;
				
			}
			else if(App.GS.org_type==App.GS.TEAM  ) 
			{
				App.GS.slideOrder[END]  =  App.GS.slideEnum.ORGDETAILS;
				App.GS.slideOrder[App.GS.slideEnum.ORGDETAILS]=App.GS.slideEnum.USERS;
				END=App.GS.slideEnum.USERS;
				
			}
 
			//final steps for allways
			//team does not have summary
			App.GS.slideOrder[END]=App.GS.slideEnum.TERMS;
			App.GS.slideOrder[App.GS.slideEnum.TERMS]   =App.GS.slideEnum.FINAL;//prefix ends here, check next type
			App.GS.slideOrder[App.GS.slideEnum.FINAL]=null;
			//prev stays null
			App.GS.activeSlide = parseInt(App.GS.slideOrder[START]);//assume we always use prefix group from the start.
		},
		
		findNext:function()
		{
			//was ++
			var from=App.GS.activeSlide;
			var to  = App.GS.slideOrder[App.GS.activeSlide];
			//.log('findNext from '+from+'to'+to);
			return parseInt(to);
		},
		findPrev:function()
		{
			//was --
			
			var from,to,current=App.GS.activeSlide;
			
			for(from in App.GS.slideOrder)
			{
				to = App.GS.slideOrder[from];
				if(to == current) 
				{
					
					//.log('findPrev given '+current+' return'+from);
					return parseInt(from);//found it. even if its null		
				}
			}			
		},
		next:function()
		{
 
			if(App.GS.isAnimating || App.GS.isLoading)
			{
 
				return;
			}
		
 


			//check which slide we are trying to leave. certain ones require validation first
			//inwhich case we submit the form, and call nextAnimation on succcess.  Otherwise,
			//we can call nextAnimation right away
			///some cases could be merged, by removing some break; statements, etc, but i leave them seperate
			//incse we need to add/modify them later
			//BE CAREFUL:  A JS SWITCH uses === , meaning int and string wont match, :  '1'===1  is false
			switch (App.GS.activeSlide)
			{
				case App.GS.slideEnum.WELCOME:
					//.log('animate away from welcome');
					App.GS.nextAnimation();//no validation here
				break;
				case App.GS.slideEnum.PASSWORD:
					//.log('submit passsword');
					
					App.GS.nextSubmit();//submit active form
					
				break;
				case App.GS.slideEnum.BEFORE:
					//.log('animate away from before');
					App.GS.nextAnimation();//no validation here
					
				break;
				case App.GS.slideEnum.PERSON:
					App.GS.nextSubmit();//submit active form

				break;
				case App.GS.slideEnum.ADDRESS:
					
					App.GS.nextSubmit();//submit active form

				break;
				case App.GS.slideEnum.CONTACT:
					 
					App.GS.nextSubmit();//submit active form
					
				break;
				case App.GS.slideEnum.ORGDETAILS:

					App.GS.nextSubmit();//submit active form
				
				break;
				case App.GS.slideEnum.ORGADDRESS:

					App.GS.nextSubmit();//submit active form
				
				break;
				case App.GS.slideEnum.USERS:
				
					//make sure at least one user assigned	
					var grid=App.GS.activeGrid[App.GS.slideEnum.USERS].grid;

					var userCount = grid.getStore().getCount();
					
					//.log(userCount);
					
					if(userCount === 0)
					{
						Ext.MessageBox.alert('Cannot continue','Please assign at least one user');
					}
					else
					{   //users are assigned
						App.GS.nextAnimation();
					}
				break;
				case App.GS.slideEnum.SIGN:
				
					//.log('tood: check grid sign auth');
					App.GS.nextAnimation();
				break;
				case App.GS.slideEnum.BANK:
				
					//var form=financeForms.form_bankaccount_add_motion()
					//.log('about to submit bank account form');
					var oForm = App.GS.activeForm[App.GS.activeSlide];
					var form= oForm.form.getForm();
					if (!form.isValid())
					{
						//.log('is not valid!!!!!!!, skip post!!');
						//in this case, blank form is allowed
						App.GS.nextAnimation();
					}
					else
					{
						App.GS.nextSubmit();//submit active form if not blank
					}
				break;
				case App.GS.slideEnum.SUMMARY:
				
				
					App.GS.nextAnimation();
				break;
				case App.GS.slideEnum.PAYMENT:
				
				
					App.GS.nextAnimation();
				break;
				case App.GS.slideEnum.TERMS:
				
				
					//its a plain html input checkbox
					var cb=Ext.get('frm-gs-terms-check');//.dom;

					//get the value in the dom
					if(!cb || cb.dom.checked)
						{App.GS.nextAnimation();}
						
						
				break;
				case App.GS.slideEnum.FINAL:
				
					App.GS.finish();
					
					//will call window destroy on success
					//App.GS.welcomeWindow.destroy();

					
					
					//App.GS.nextAnimation();
				break;
			}

		},
		//submit the active form, whatever it is
		nextSubmit:function()
		{
			var oForm = App.GS.activeForm[App.GS.activeSlide];
			if(!oForm){
				//console.log('oForm undefined for slide'+App.GS.activeSlide+'SKIP AHEAD');
				App.GS.nextAnimation();
				return;
			}
			var form= oForm.form.getForm();
			var url = oForm.url;
			
			if (!form.isValid()){return;}
			App.GS.startLoading();//show loading image and block movement buttons
			 /*
			form.submit(
			{
				url:url
				,failure :function(){} //App.GS.nextSuccess
				,success :function(){}// App.GS.nextSuccess
			});*/
			Ext.Ajax.request(
			{
				url:url
				,params:form.getValues()
				,method:'POST'
				,disableCaching:true
				,failure : App.error.xhr
				,success : App.GS.nextSuccess
			});
			
			//App.GS.nextSuccess(null,null);//workaround for that  chrome html toolbar bug
		},
		//made for form callback
		nextSuccess:function(/*f, */action)
		{
			App.GS.stopLoading();//allow navigation even if failure
			/*
			if(f==null || action==null)
 			{
 				App.GS.nextAnimation();
 				
				if(App.GS.activeForm[App.GS.SUMMARY] && App.GS.activeForm[App.GS.SUMMARY].form)//update summary each time
					{App.GS.activeForm[App.GS.SUMMARY].form.load();}
				return;
 			}*/
			try
			{					
		        var res=YAHOO.lang.JSON.parse(response.responseText);
 
				if(!res.success )//if failed, if success set to false
				{
					App.error.xhr(response);
				}
				/*else
				{
					
				}*/
				
			}
			catch(e)
			{
				//App.error.xhr(action.response);//if json does not parse
				//removed above due to weird chrome issues, div toolbar
												
			}
			finally
			{
				App.GS.nextAnimation();
				
				if(App.GS.activeForm[App.GS.SUMMARY] && App.GS.activeForm[App.GS.SUMMARY].form)//update summary each time
					{App.GS.activeForm[App.GS.SUMMARY].form.load();}
			}
		},
		nextAnimation:function()
		{
			App.GS.btns.prev.setDisabled(false);
			
			var prev 	= Ext.get((Ext.query('div[class=ctr-gs-frm-prev]')).pop());
			var current = Ext.get((Ext.query('div[class=ctr-gs-frm-current]')).pop());
			var next 	= Ext.get((Ext.query('div[class=ctr-gs-frm-next]')).pop());
			
			//animate
			current.animate({
				to			: { top:-388 },
				easing		: App.GS.animOutEffect,
				duration 	: App.GS.animDuration,
				listeners	: {
					beforeanimate:function()
					{
						App.GS.isAnimating = true;
					}
				}
			});
			
			next.animate({
				to			: { top:0 },
				easing		: App.GS.animInEffect,
				duration 	: App.GS.animDuration,
				listeners	: {
					afteranimate:function()
					{
						current.removeCls('ctr-gs-frm-current');
						current.addCls('ctr-gs-frm-prev');
						
						next.removeCls('ctr-gs-frm-next');
						next.addCls('ctr-gs-frm-current');
						
						prev.removeCls('ctr-gs-frm-prev');
						prev.addCls('ctr-gs-frm-next');
						prev.setTop(388);
						//.log('next.animate was setting active++ here');
						
						//.log('NEXT active slide changing from '+App.GS.activeSlide);
						App.GS.activeSlide= App.GS.findNext();
						//.log('to  '+App.GS.activeSlide);
						
						App.GS.isAnimating = false;
						
						App.GS.getForm(App.GS.findNext(),'next');
					}
				}
			});
		},
		prev:function()
		{
			if(App.GS.isAnimating || App.GS.isLoading || App.GS.activeSlide==1)
			{
				 
				return;
			}
			
			var prev 	= Ext.get((Ext.query('div[class=ctr-gs-frm-prev]')).pop());
			var current = Ext.get((Ext.query('div[class=ctr-gs-frm-current]')).pop());
			var next 	= Ext.get((Ext.query('div[class=ctr-gs-frm-next]')).pop());
			
			
			var prevSlide=App.GS.findPrev();
			if(App.GS.activeForm[prevSlide] && App.GS.activeForm[prevSlide].form)
			{
				//activate the previous form, if it exists. otherwise tab / shift-tab will avoid animations
				App.GS.activeForm[prevSlide].form.setDisabled(false);
			}
			//animate
			current.animate({
				to			: { top:388 },
				easing		: App.GS.animOutEffect,
				duration 	: App.GS.animDuration,
				listeners	: {
					beforeanimate:function()
					{
						App.GS.isAnimating = true;
					}
				}
			});
			
			prev.animate({
				to			: { top:0 },
				easing		: App.GS.animInEffect,
				duration 	: App.GS.animDuration,
				listeners	: {
					afteranimate:function()
					{
						current.removeCls('ctr-gs-frm-current');
						current.addCls('ctr-gs-frm-next');
						
						prev.removeCls('ctr-gs-frm-prev');
						prev.addCls('ctr-gs-frm-current');
						
						next.removeCls('ctr-gs-frm-next');
						next.addCls('ctr-gs-frm-prev');
						next.setTop(-388);
						
						//.log('prev.animate was setting active-- here');
						//App.GS.activeSlide--;//////////////???????????????????????????????
						//.log('active slide changing from '+App.GS.activeSlide);
						App.GS.activeSlide = prevSlide;
						//.log(' to '+App.GS.activeSlide);
						App.GS.isAnimating = false;
						App.GS.getForm(App.GS.findPrev(),'prev');
					}
				}
			});
		},
		
		//jump to the given slide number.  also updates next and prev accordingly
		jumpTo:function(slide)
		{
			//update all three slides
			App.GS.activeSlide = parseInt(slide);
			
			App.GS.getForm(App.GS.activeSlide,'current');
			
			App.GS.getForm(App.GS.findNext(),'next');//update the hidden divs that will slide
			App.GS.getForm(App.GS.findPrev(),'prev');
				

			
		},
		
		getForm:function(slide,location)
		{
			//.log('getForm:',slide,location);
			
			if(!slide ||slide==0 || !location) {return;}//form zero does not exist, for example
			
			App.GS.startLoading();
			var url = "/index.php/getstarted/getForm/"+slide+"/"+location;
			var callback = {success:App.GS.updateForm,failure:App.error.xhr};
			YAHOO.util.Connect.asyncRequest('GET',url,callback);
		},
		
		updateForm:function(o)
		{
 
			try{
			var json = YAHOO.lang.JSON.parse(o.responseText);
			}
			catch(e)
			{
 
				App.error.xhr(o);//if we could not parse json, then php error must be in there
				return;
			}
			

			var div = Ext.get((Ext.query('div[class=ctr-gs-frm-'+json.DIR+']')).pop());
			div.dom.innerHTML = json.HTML;
			
			if(App.GS.activeForm[App.GS.activeSlide] && App.GS.activeForm[App.GS.activeSlide].form)
			{							 
				App.GS.activeForm[App.GS.activeSlide].form.setDisabled(false);
				App.GS.activeForm[App.GS.activeSlide].form.show();
				App.GS.activeForm[App.GS.activeSlide].form.getForm().isValid();
				
				//enable next form if it exists
			} 
			App.GS.btns.next.setDisabled(false);
			App.GS.btns.prev.setDisabled(false);
			
			if(json.DISABLE)
			{
				switch(json.DISABLE)
				{
					case 'next': App.GS.btns.next.setDisabled(true); break;
					case 'prev': App.GS.btns.prev.setDisabled(true); break;
				}
			}

			if(json.JS)
			{
				YAHOO.util.Get.script(json.JS, {onSuccess:function(){
					App.GS.stopLoading();
				}});
			} 
			else
			{
				App.GS.stopLoading();
			}
			
			

		},
		startLoading:function()
		{
			App.GS.isLoading = true;
			//Ext.get('ctr-gs-loading').dom.innerHTML = "<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/bullet_yellow.png'/>";
			if(Ext.get('ctr-gs-loading'))
				Ext.get('ctr-gs-loading').dom.innerHTML = "<img src='/assets/images/ajax-loader.gif'/>";
		},
		
		stopLoading:function()
		{
			
			App.GS.isLoading = false;
			if(Ext.get('ctr-gs-loading'))
				Ext.get('ctr-gs-loading').dom.innerHTML = "<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/bullet_green.png'/>";
		},
		/**
		* @deprec : only because its not needed/used 
		*/
		bankExample:function()
		{
			Shadowbox.open(
			{
				content:    "/assets/images/cheque_sample.jpg",
				player:     "img",
				title:      ""
			}); 			
		}
		
		
		,finish:function()
		{
			//update last logindate and set activeorg to valid==true.
			//this way, welcome screen will NOT show on their next login
			var url='index.php/getstarted/post_finish/'+App.TOKEN;
			
			var callback={failure:App.error.xhr,success:function(o)
			{
				var r=o.responseText;
				if(isNaN(r)) 
					{App.error.xhr(o);}
				else
				{
					App.GS.welcomeWindow.destroy();
					//App.login.show();
				}//on success: close window
			}};
			YAHOO.util.Connect.asyncRequest('GET',url,callback);
		}
	},
	
	DYK:
	{
		interval:false,
		iteration:2,
		
		init:function()
		{
			App.DYK.interval = setInterval("App.DYK.alternate()",15000); //15 seconds
		},
		
		alternate:function()
		{
			//avoid animating when GS is animating for smoother transitions
			if(App.GS.isAnimating==true)
			{
				///.log('Waiting for GS Animation');
				setTimeout("App.DYK.alternate()",100);
				return;
			}
			
			var current = Ext.get((Ext.query('div[class=ctr-gs-dyk-current]')).pop());
			var next = Ext.get((Ext.query('div[class=ctr-gs-dyk-next]')).pop());
			
			//animate
			if(current && typeof current.animate == 'function'){//added otherwise IE will try and do this once after window closes and then cry!
			current.animate({
				to			: { opacity:0 },
				easing		: 'easeOut',
				duration 	: 1000,
				listeners	: {
					afteranimate:function()
					{
						next.addCls('ctr-gs-dyk-current');
						next.removeCls('ctr-gs-dyk-next');
						current.removeCls('ctr-gs-dyk-current');
						current.addCls('ctr-gs-dyk-next');
						current.setOpacity(1);
						App.DYK.getNext();
					}
				}
			});}
		},
		
		getNext:function()
		{
			var url = "/index.php/getstarted/getDYK/"+App.DYK.iteration++;
			var callback = {success:App.DYK.updateNext};
			YAHOO.util.Connect.asyncRequest('GET',url,callback);
		},
		
		updateNext:function(o)
		{
			var target = (Ext.query('div[class=ctr-gs-dyk-next]')).pop();
			Ext.get(target).dom.innerHTML = o.responseText;
		}
	}
	
	,dom:
	{
		/**
		* 
		* @var string class_id : class that will be passed as the first argument into Ext.define
		* @var showConsole [optional] if passed and true, will display debug messages in console
		* @return boolean:  true iff the class has been defined
		* 
		*/
		definedExt:function(class_id,showConsole)
		{
			if(typeof showConsole != 'boolean') showConsole=false;//if type is 'undefined', or something invalid, assume false
			
			if(typeof class_id != 'string' ) 
			{
				var t=typeof class_id;
				if(showConsole) console.error('Error: App.dom.definedExt only takes type string, a '+t+" was given");
				return false;
			} 
			
			var boolExists = Ext.ClassManager.isCreated(class_id)  || Ext.ModelManager.isRegistered(class_id);
			
			/*|| Ext.isDefined(class_id) //dont use this , it always returns true. it is for variables not classes*/
			if(showConsole)
			{
				if(boolExists)  console.info('Ext cannot redefine :'+class_id);
				else            console.info('Ext.define new : '+class_id);
			}
			
			return boolExists;
		}	
		
		//possible future functions:  definedHtml, definedYui, definedVar
		
	}
	
	
	
}
 
var App;
Ext.Loader.setConfig({enabled: true});
     
Ext.require(['*']);

Ext.Loader.setPath('Ext.ux', '/assets/js/plugins/search');
Ext.require(["Ext.ux.SearchField"]);
Ext.Loader.setPath('Ext.ux', '/assets/js/plugins/tabscrollermenu');
Ext.require(['Ext.ux.TabScrollerMenu']); 
Ext.Loader.setPath('Ext.ux', '/assets/js/plugins/grid');
Ext.require(['Ext.ux.RowExpander']); 
   
//inside on ready we will call App = new Spectrum(); , but first we apply bug fixes/workarounds to problems in Ext
Ext.onReady(function()
{ 
	//  **1**
	//chrome/ie error: isReader null or not an object
	
	//the same error was found in two forum posts: 
	//http://www.sencha.com/forum/showthread.php?131603-OPEN-EXTJSIV-1776-4.0.0-Defining-a-model-twice/page2
	//http://www.sencha.com/forum/showthread.php?140798-OPEN-Ext-redefining-quot-undefined-quot-in-post-processing-step.&s=7b524f38b82c6fda761910cfaab647f2
	
	//bug happened for two reasons
	//1. most browsers do NOT treat undefined as a reserved word (firefox protects it)
	//2. chrome and IE and other browsers, when doing namespace management/garbage collection set undefined as a variable to some value
	//3. Ext.js compares things like this: ( x===undefined). which is wrong. they should be doing (typeof(x) == 'undefined' )
	// we apply a simple workaround.
	var originalReader=Ext.data.proxy.Proxy.prototype.setReader;

	Ext.override(Ext.data.proxy.Proxy,{

	  setReader: function (reader) 
	  {
	  	//unset the variable undefined 
		var undefined;//remember: undefined is a variable, not a reserved word like 'null'
		window[undefined] = undefined;
		//call original function
		originalReader.apply(this,arguments);
    }

	});
	/**
	* in IE9 we sometimes get 'c.events is null or undefined'. but thats exactly what its checkign: is it undefeind 
	* so if(c.events) should be false on undefiend or null
	* 
	* SCRIPT5007: Unable to get value of the property 'events': object is null or undefined 
	* ext-all-debug.js, line 32738 character 13
	*/
	Ext.override(Ext.toolbar.Toolbar,{

	    applyDefaults: function(c) {
        if (!Ext.isString(c)) {
            c = this.callParent(arguments);
            var d = this.internalDefaults;
            if (typeof(c.events) != 'undefined' || c.events) {
                Ext.applyIf(c.initialConfig, d);
                Ext.apply(c, d);
            } else {
                Ext.applyIf(c, d);
            }
        }
        return c;
    }

	});
	
	
	
	//  **2** 
	
	//http://www.sencha.com/forum/showthread.php?136528-4.0.2-Store.bindStore-assumes-me.loadMask-has-bindStore-function
	    
	//this happens when a grid is created, not rendered, and then destroyed. for example, in a form but hidden.
	//possibly also if the store is not an object, but just 'store:[]', 

	//the only thing I added was "&& typeof this.loadMask.bindStore == 'function' "  in two places
    Ext.override(Ext.view.AbstractView,{

	    bindStore : function(store, initial) {
		//var me = this;
		if (!initial && this.store) {
			if (store !== this.store && this.store.autoDestroy) {
				this.store.destroy();
			}
			else {
				this.mun(this.store, {
					scope: this,
					datachanged: this.onDataChanged,
					add: this.onAdd,
					remove: this.onRemove,
					update: this.onUpdate,
					clear: this.refresh
				});
			}
			if (!store) {
				if (this.loadMask && typeof this.loadMask.bindStore == 'function') {//I changed this line
					this.loadMask.bindStore(null);
				}
				//else {} //me.loadMask is either false 
				this.store = null;
			}
		}
		if (store) {
			store = Ext.data.StoreManager.lookup(store);
			this.mon(store, {
				scope: this,
				datachanged: this.onDataChanged,
				add: this.onAdd,
				remove: this.onRemove,
				update: this.onUpdate,
				clear: this.refresh
			});
			if (this.loadMask && typeof this.loadMask.bindStore == 'function') {//i changed this line
				this.loadMask.bindStore(store);
			}
		}
		this.store = store;
		this.getSelectionModel().bind(store);
		if (store && (!initial || store.getCount())) {
			this.refresh(true);
		}
		}
    });
	// do not need override of this, 
	//workaround found in this thread http://www.sencha.com/forum/showthread.php?130680-TreeStore-supported-in-MVC&p=643402#post643402

	// WARP 9 ENGAGE 
	App = new Spectrum(); 

	 
	
	
});

function merge_json_arrays(x1,x2)
{
    var result={}
    for (var x in x1)result[x]=x1[x];
    for (var x in x2)result[x]=x2[x];
    return result;    
}

function getFacebookObject()
{
	
    YAHOO.util.Connect.asyncRequest('get', "/index.php/home/getFacebookObject/"+App.TOKEN,
    {
        scope:this,
        success:function(o)
        {      
            App.facebookObject= YAHOO.lang.JSON.parse(o.responseText);
            extraFBSettings();  
        },
        failure:App.error.xhr
    });    
}
function extraFBSettings()
{                   
    if(App.facebookObject.id!=null && App.TOKEN!=null)
    {
    	return;//this causes 404 every time
        YAHOO.util.Connect.asyncRequest('POST', "/index.php/home/json_adjust_fb_objects/"+App.TOKEN,
        {
            scope:this
            ,success:function(o){}
            ,failure:App.error.xhr
        },'fb_id='+App.facebookObject.id);
    }                                             
}


// givers user option to cancel back / browser close
// window.onbeforeunload = function(){ return "You are about to close Spectrum."; }
// this not working as expected


