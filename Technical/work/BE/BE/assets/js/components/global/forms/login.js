
var loginForm = 'Spectrum.forms.login';
if(!App.dom.definedExt(loginForm)){
Ext.define(loginForm,
{
	extend: 'Ext.spectrumforms', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
 
    url:"/index.php/permissions/check_login",
    waitingForCallback:false,
    send_login:function()
    {
    	if(this.waitingForCallback) { return;}
    	this.waitingForCallback=true;
		// The getForm() method returns the Ext.form.Basic instance:
		//var form = Ext.getCmp('login_form').form;
		var form=this.getForm();
        
		var post = [];
		Ext.iterate(form.getValues(), function(key, value){ post.push(key+"="+escape(value ) ); });
        post = post.join("&");
        
    	if(!App.facebookObject)App.facebookObject={};
    	if(!App.facebookObject.id)App.facebookObject.id=-1;//defaults in case null object, that way it wont crash
    	
        //Added by Ryan      
        if(App.facebookObject!=null && App.facebookObject.id!=null && typeof App.facebookObject.id != 'undefined' )
        {                                                                             
            post+='&fb_id='     +App.facebookObject.id
            //post+='&fb_email='  +App.facebookObject.email
              
            //No pairing with FB Email anymore  
            //pairing should be done between logged-in fb_id and authorized username/password
        }   
		YAHOO.util.Connect.asyncRequest('POST', this.url, {scope:this,success:this.callback_login
			,failure:function(o){this.waitingForCallback=false;//just in case
		}}, post);
    },
    Adjust_fb_objects:function(TOKEN)
    {         
    	if(!App.facebookObject)App.facebookObject={};
    	if(!App.facebookObject.id)App.facebookObject.id=-1;//defaults in case null object, that way it wont crash
        YAHOO.util.Connect.asyncRequest('POST', "index.php/home/json_adjustFBFriendsGroups/TOKEN:"+TOKEN, 
        {
            scope   :this,
            success:function(){},
            failure:function(o){}
        },"fb_id="+App.facebookObject.id);
    },
    
    callback_login:function(o)
	{
		this.waitingForCallback=false;
		var json = o.responseText;
		var json_array = YAHOO.lang.JSON.parse(json);
 
		//add data to say which part failed: was it username, password, or cap 
		if(json_array.fb_id=='FBLOGINNOPAIR' )//means no fb_id-user_id pair exists
        {
			Ext.getCmp('ctr_fb_login').hide();
			Ext.getCmp('_login_fb_display_container').show();
		
            document.getElementById('fbProfilePic').innerHTML='<img src="http://graph.facebook.com/'+App.facebookObject.id+'/picture" style="max-height:150px" />';
            if(json_array.fail_type=='org') {return;}
          
			
            
        }
		if(json_array.token==-1)
		{
			var title='Login Failed' ;
			
			var msg='You provided an incorrect ';//' Please retry.  ';
			
			//fail_type is always one of : null,user,pass,cap
			
			switch(json_array.fail_type)
			{
				case "user":
					msg+='<b>username</b>';
				break;
				case 'pass':
					msg+='<b>password</b>';
				break;
				case 'cap':
					msg+='<b>captcha</b>';
				break;
				case 'org':
					msg = 'Either this username was not found, or no organizations are assigned to this user account.  ';
				break;
				
			}
			if(json_array.fail_type != 'org')msg+=', please retry.  ';
			//check if cap req or not
			if(json_array.cap_required==true || json_array.cap_required == 't')
			{
				msg+= "<br><b>Captcha</b> is required to log in because of too many incorrect attempts.  "
				+"Copy the number from the image into the textbox beside it.  "
				+"If you have forgotten your username and password, use the respective buttons on the bottom of the form.";
				
				this.set_cap_required_yes();//regardless of the fail type, set cap if required
			}
			Ext.MessageBox.alert(title ,msg  );
		}
		else
		{
			//send to global controller
			App.login.success(json);
            this.Adjust_fb_objects(json_array.token);
		}
		
	},
    
    set_cap_required_yes:function()
    {
		Ext.getCmp('hbox_login_captcha').show();
		Ext.getCmp('info_login_captcha').show();
		this.refreshCaptcha();//refresh each time
     },
     
     
     /**
     * refresh the image inside the captcha box
     * called by click event on image, and by login failed-sometimes
     * 
     */
    refreshCaptcha:function()
	{ 
		var el=Ext.get('html_component_captcha');
 
		el.dom.src =  'captcha.php?r=' + Math.floor(Math.random()*100+1); 
		Ext.getCmp('input_captcha_textfield').setValue('');//empty out the box

	},
    
    TYPE_FORGOT_PASS:1,
    TYPE_FORGOT_USER:2,
 
    send_email_request:function(type)
    {
    	var method='';
		if(type == this.TYPE_FORGOT_PASS)
		{
			method='post_reset_password';
		}
		else if(type == this.TYPE_FORGOT_USER)
		{
			method='post_retrieve_username';
		}
		
		var form=this.getForm();
		Ext.MessageBox.prompt('Enter the email on your account',"Email:",function(btn_id,text)
	    {
	        
	        if(btn_id != 'ok' && btn_id != 'yes') {return;}
	        
			var url="index.php/permissions/"+method+"/";
 			
			var post = [];
			Ext.iterate(form.getValues(), function(key, value){ post.push(key+"="+value); });
			
			post.push("email="+escape(text)  );
			
			post = post.join("&");
			YAHOO.util.Connect.asyncRequest('POST', url, {scope:this,success:function(o)
			{
				var response = o.responseText;
				
				Ext.MessageBox.alert("Action Complete:",response);

			}}, post);
		});
 
    },
 
    /**
    * Login Form Constructor
    * 
    * @param array config of input that will be ignored,  overrides with default form values
    * to allow input config to be considered, simply change constructor format to look like:
    *  if(!config.id) config.id=... ,   config.title= , config.items.push() .. etc
    */
    constructor: function(config)
    { 
    	var TOTAL_WIDTH=300;
    	var DISPLAY_FB_MSG_W=215;
    	var LABEL_WIDTH=70;
    	//var CAP_HEIGHT=32;
    	//var MARGIN=0;
    	var HALF_WIDTH=(TOTAL_WIDTH-LABEL_WIDTH)/2;
    	var CAP_IMG_HEIGHT=38;
    	var TXT_HEIGHT=22;
        
		config =	
		{
			// Login Form
			id			: 'login_form',
			title		: '',
			autoHeight	: true,
			width		: TOTAL_WIDTH,
			resizable	: false,
			bodyPadding	: 5,
			border		: 0,
			
			fieldDefaults: 
			{
				labelWidth		: LABEL_WIDTH ,
				msgTarget		: 'side',
				autoFitErrors	: true
			},
			
			defaults:
			{
				anchor:'100%'
			},
			items:
			[
			   // Hidden Field to Capture IP Address
				{
					
					xtype		: 'hiddenfield',
					id			: 'login_ip',
					name 		: 'ip',
					value 		: Ext.getDom('ip').value
				}
				// Login Title
				,{
					
					id:'login_image',
					border:0,
					html:'<h1 id="titleLogin">Please Login</h1><br/>'
				}
				// Facebook Login Button
				,{
					//container in order to simulate a dockedItems style '->'
					xtype		: 'fieldcontainer',  
					layout		: 'hbox', //hbox == horizontal group layout
					width		: '100%',
					flex		: 1,  
					id:'ctr_fb_login',
					hideLabel	: true,
					items:
					[
						{flex:1,xtype:'displayfield',value:'  '},
					 
						{
		                    
		                    text    : 'Login with Facebook',
		                    xtype:'button',
		                    iconCls : 'social_facebook',
		                   // iconCls : 'facebook',
		                    scope	: this,
		                    tabIndex: 4,
		                    style:{'text-align':'right'},
		                    
		                    handler:function() 
		                    {
		                        var left = parseInt((screen.availWidth/2) - (800/2));
		                        var top = parseInt((screen.availHeight/2) - (500/2));
								var url = "/index.php/home/index";
								var params = [
									"width=800",
									"height=500",
									"top=200",
									"scrollbars=no",
									"left="+ left,
									"top=" + top,
									"screenX=" + left,
									"screenY=" + top
								];
								params = params.join(',');
								
		                        window.open(url,"_blank",params);
		                    }
		                }
		            ]
	            }

		        //facebook first time users container
				,{ 
 
					xtype		: 'fieldcontainer',  
					fieldLabel	: '', 
					id:"_login_fb_display_container",
					hidden:true,
					layout		: 'hbox',
					width		: '100%',
					flex		: 1,
					height:50,//facebook graph/picture is exactly 50x50
					items:
					[
					    {
		                    // Facebook User Image
		                    id      :'login_fb_pic',
		                    xtype   :"component",
		                    width:LABEL_WIDTH+4,//same size as others, plus buffer fix
		                    html    : "<div id='fbProfilePic'></div>"
		                }
		                ,{
		                	//display shown for first time
							xtype:'component',
							width		: DISPLAY_FB_MSG_W,
							 
							html:"<div style='font-size:10px;'>You have successfully logged in with Facebook, but we still don't know which Spectrum login you are!"
								+" Please login below so that we know which account to log you into for this Facebook User</div>"
							
		                }
	                
	                ]
				}
				    // hr
				,{
		            xtype   :"component",
		         //   width:LABEL_WIDTH/4,//same size as others, plus buffer fix
		            html    : "<div class='hr'></div>"// simulates <hr> defined in spectrum2.css
		        }
				// Username Field
				,{
					
					xtype		: 'textfield', 
					id			: 'input_login_form_username',
					name 		: 'username',
					fieldLabel	: 'Username',
					tabIndex	: 1,
					value		: '',
					allowBlank	: true,
					enableKeyEvents: true
				}
						// Password Field			
				,{

					xtype		: 'textfield', 
					id			: 'input_login_form_password',
					name 		: 'password',
					fieldLabel	: 'Password',
					tabIndex	: 2,
					value		: '',
					allowBlank	: true,
					enableKeyEvents: true,
					inputType	: 'password'
				}
				
				// Captcha Notification
				,{
					
					xtype	: 'displayfield',
					id		: 'info_login_captcha',
					flex	: 1,
					value	: '<div class="captchaNote">Click the image for a different the Captcha image.</div>'
				}
				// Captch Container
				,{
					
					xtype		: 'fieldcontainer',  
					layout		: 'hbox', //hbox == horizontal group layout
					width		: '100%',
					flex		: 1,  
					id			: 'hbox_login_captcha',
					hideLabel	: true,
					
					fieldDefaults: {
						labelAlign: 'bottom'
					},
					
					items:
					[	
				    	{
				    		// Captcha Image
				    		xtype		: 'component',
				    		
				    		width		: HALF_WIDTH,
				    		height		: CAP_IMG_HEIGHT,
			    			id			: 'html_component_captcha',//raw image is 100 by 38
			    			hideLabel	: true,
							autoEl:
							{
								tag	:'img',
								src	:"captcha.php"
							},

							listeners:
							{
								click:
								{
									element:'el',
									
									fn:function()
									{ 
 										var txt=Ext.getCmp('input_captcha_textfield');
 										 
										txt.up('form').refreshCaptcha();
										/*//old way without the above function. still works
										this.dom.src =  'captcha.php?r=' + Math.floor(Math.random()*100+1); 
										
										Ext.getCmp('input_captcha_textfield').setValue('');//empty out the box
										*/
									}
								}
							}
						},
						{
				    		// Captcha Input Textbox
				    		xtype		: 'textfield',
				    		label		: '',
				    		name		: 'captcha_input',
				    		id			: 'input_captcha_textfield',
				    		flex		: 1,
				    		tabIndex	: 3,
				    		height		: TXT_HEIGHT,
				    		enableKeyEvents : true
				    	}
					] // end items
				}
 
 				// Login Button
 				,{
					//container in order to simulate a dockedItems style '->'
					xtype		: 'fieldcontainer',  
					layout		: 'hbox', //hbox == horizontal group layout
					width		: '100%',
					flex		: 1,  
					hideLabel	: true,
					items:
					[
						{flex:1,xtype:'displayfield',value:'  '}
						,{
							xtype:'button',
							text		: 'Login',
							id			: 'btn_login_main',
							iconCls		: 'key_start',
							scope		: this,
							tabIndex	: 5,
							
							handler:function()
							{
			                    this.send_login();
							}
						}
				]}
				// hr
				,{
		            xtype   :"component",
		           // width:"75%",//same size as others, plus buffer fix
		            html    : "<div class='hr'></div>"// simulates <hr> defined in spectrum2.css
		        }
				//Forgot Username Container
				,{
					
					xtype		: 'fieldcontainer',  
					fieldLabel	: '', 
					layout		: 'hbox',
					width		: '100%',
					flex		: 1,
					
					items:
					[
						{
				    		// Captcha Label
				    		xtype	: 'displayfield',
				    		value	: '',
				    		width	: LABEL_WIDTH+4
				    	},
				    	{
							xtype	:"displayfield",
							html	:'<div class="btnForgot" id="btnForgotUsername"><a href="javascript:void(0)" '
										+'  >I forgot my Username</a></div>',
							flex	: 1,
							height	: 12,
							width	: '100%',
							listeners:
							{
 
								
								afterrender: 
								{
									scope:this,
									fn:function(component) 
									{
										component.getEl().on('click', function() 
										{ 
                                            //alert('btnForgotUsername field was clicked!');
											this.send_email_request(this.TYPE_FORGOT_USER); 
										},this);  
								    }
								}
							}
				    	}
					]
				}
				 
				//Forgot Password Container
				,{
					
					xtype		: 'fieldcontainer',  
					fieldLabel	: '', 
					layout		: 'hbox',
					width		: '100%',
					flex		: 1,
					
					items:
					[
						{
				    		// Captcha Label
				    		xtype	: 'displayfield',
				    		value	: '',
				    		width	: LABEL_WIDTH+4
				    	},
				    	{
							xtype	:"displayfield",
							html	:'<div class="btnForgot" id="btnForgotPassword" ><a href="javascript:void(0)" '
										+'  >I forgot my Password</a></div>', 
							flex	: 1,
							height	: 12,
							width	: '100%',
							listeners:
							{
 
								afterrender: 
								{
									scope:this,
									fn:function(component) 
									{
										component.getEl().on('click', function() 
										{ 
											this.send_email_request(this.TYPE_FORGOT_PASS); 
										},this);  
								    }
								}
							}
				    	}
					]
				}
				
				
	 
            ]
            
 
		};

		this.callParent(arguments); 

		//on enter key, set focus to login, as if they ahd hit TAB
		Ext.getCmp('input_login_form_username').on('keyup',function(f,e,o)
		{
			if(e.getCharCode() == e.ENTER)
			{ 
				var selectAllTextInBox=true;
				var delayInMS=0;
				
				Ext.getCmp('input_login_form_password').focus(selectAllTextInBox,delayInMS);
				
			} 
		},this);
		
		// on enter key, perform the login action
		Ext.getCmp('input_login_form_password').on('keyup',function(f,e,o)
		{ 
			if(e.getCharCode() == e.ENTER)
			{ 
				this.send_login();
				
			} 
		},this);
		
		//also add to capcha box, which may be hidden 
		Ext.getCmp('input_captcha_textfield').on('keyup',function(f,e,o)
		{  
			// on enter key, perform the login action
			if(e.getCharCode() == e.ENTER)
			{ 
				this.send_login();
				
			} 
		},this);
		
		//hide for now
		Ext.getCmp('hbox_login_captcha').hide();
		Ext.getCmp('info_login_captcha').hide();
	}
    
});}

function FBLogin(id,email)
{             
	if(!App.facebookObject) App.facebookObject={};  
	 
	App.facebookObject.id=id;
	App.facebookObject.email=email;
	//fixed to use the real login function, removed copy paste version. 
	//this way we have more control over error messages and captcha refresh
	Ext.getCmp('login_form').send_login();
}


/***
* 
* 
* 
* 
* 
* 
* 
* 
* 
* 
* 
* 
* The following two functions have absolutely nothing to do with the login procedure.  They are sometiems called through a click event 
* on the right panel, maybe other times as well.
* They are moved to global/controller.js at the bottom
* In the future,  could be somewhere better, maybe App.FB.getFacebookObject so they are in a useful subobject
* 
* 
*/




