


//window


//define constants and functions
var save_user = function()
{
    var form = this.up('form').getForm();
    var form_values=form.getValues();
    if (!form_values["person_fname"] &&  !form_values["person_lname"])
    {
        Ext.MessageBox.alert({title:"Cannot Save",msg:"Person Name cannot be empty", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
        return;
    }
    if(Ext.getCmp('frm_config_login_optional').getValue() == false)
    {
	    if(!form_values['login'])
	    {
			//if login empty
			Ext.MessageBox.alert({title:"Cannot Save",msg:"Login is empty", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
	        return;
	    }
	    if (form_values["pass"]!=form_values["pass-cfrm"])
	    {
	        Ext.MessageBox.alert({title:"Cannot Save",msg:"Passwords do not match", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
	        return;
	    }
	}
    //else login is optional because its probably for roster
    var form_data={};
    Ext.iterate(form.getValues(), function(key, value) 
    {
        form_data[key]=value;
    }, this);
    //save
    var post="form_data="+escape(YAHOO.lang.JSON.stringify(form_data));

    var url="index.php/person/post_insert_update_person/"+App.TOKEN;
    var callback={failure:App.error.xhr,scope:this,success:function(o)
    {    
    	var success;
    	try
    	{
    		 success = YAHOO.lang.JSON.parse(o.responseText);

    	}
    	catch(e)
    	{
			App.error.xhr(o);
			return;//if json decode fails, an error has occured
    	}
    	var e_msg='';
    	var user_id=-1;
    	var person_id=-1;
    	if(isNaN(success['email']))
    	{
			e_msg+=success['email']+"<br/>";
			 
			Ext.MessageBox.confirm('Did not finish saving.',e_msg,function(btn_id)
		 	 {
		 		 if(btn_id!='yes')return;
		 		    
		 		 var user = success['email_user'];
		 		  
		 		 //we probalby dont need both user AND person, but just in case
		 		 //format flag to TRUE
		 		 var post='user_id='+user.user_id+"&person_id="+user.person_id+"&format=true";
		 		   
		 		 Ext.Ajax.request(
                {
                    url     : 'index.php/person/get_user/'+App.TOKEN,
                    params  : post,
                    scope: this,//scope for 'this' button is the save button inside the form, therefore this.up() is valid
                    success : function(o)
                    { 
                                                                
                         var res = YAHOO.lang.JSON.parse(o.responseText);
                         res.password=''; 
                         var f=this.up('form').getForm();
                         
                         f.reset();//clear all fields before load
                           
                         //loadRecord does not work on plain JS objects, needs to be EXT model record
						 res = Ext.ModelManager.create(res, 'User');
                          
                        f.loadRecord(res);
	                    
                    }
                    ,failure:App.error.xhr
                });  
			 },this);
			
			return;
    	}
    	if(Ext.getCmp('frm_config_login_optional').getValue() == false )
    	{//login required so test what happened 
    		if(success['user_id']<0)
    		{
    			//but it failed
				e_msg+="User not created, login name may already be in use by someone else<br/>";
    		}
    		else
    		{
				user_id=parseInt(success['user_id']);
    		}
		}
    	if(e_msg != '')
    	{
			
			Ext.MessageBox.alert('Saved with issues',e_msg);
			return;
    	}
    	person_id = success['person_id'];
        if(!isNaN(person_id) && person_id   != '-1'   )
        {
            person_id=parseInt(person_id);
            if(Ext.getCmp('form_person_id'))
            	Ext.getCmp('form_person_id').setValue(person_id);//for create
			
            if(Ext.getCmp('form_user_id'))
            	Ext.getCmp('form_user_id').setValue(user_id);//for create	
				
				
            //Ext.MessageBox.alert("Save Successful.  ");
            var window_id='user_form_window';
            var form = this.up('form').getForm();
            if( form.window_id ) window_id = form.window_id;
            if(Ext.getCmp(window_id))
            	Ext.getCmp(window_id).hide();
            else //if no referenc to the window then at least confirm to user
            	Ext.Msg.alert("Success",'Person saved'); 
            
            
            
            //this sometimes does not exist
            if(Ext.getCmp('create_user_vvv'))Ext.getCmp('create_user_vvv').destroy();
        }
         
    }};
    YAHOO.util.Connect.asyncRequest('POST',url,callback,post);

};

var bodyPadding= 10;
//subform sizes
var subWidth=335;
var subHeight=180;
var subStyle=' background-color: #DFE8F6';   //padding: 10px; //padding moved to main only
var subLblWidth=105;
//define all (four) sub-forms
var pd="Spectrum.forms.person_details";
if(!App.dom.definedExt(pd)){
Ext.define(pd,
{
	//extend: 'Ext.form.Panel', 
	extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
           
    constructor     : function(config)
    {        
		config.bodyPadding= bodyPadding;
		config.border=false;
		config.autoScroll  = false;
	    if(!config.id) { config.id = 'person_details_form_'}
		
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.bodyStyle) { config.bodyStyle = subStyle; }
		if(!config.width)     { config.width     = subWidth; }
		if(!config.height)    { config.height    = subHeight; }
		if(!config.fieldDefaults) 
		{
			config.fieldDefaults =  {labelWidth: subLblWidth,msgTarget: 'under',autoFitErrors: true};
		}
		if(!config.defaults) config.defaults= {anchor: '100%'};

		config.items= 
		[
			{xtype: 'textfield',name : 'person_fname',id:'form_data_fname',fieldLabel: 'First Name',maskRe     : /^[a-zA-Z]/ ,
					width:config.width,allowBlank: false},
			{xtype: 'textfield',width:config.width,name : 'person_lname',id:'form_data_lname',fieldLabel: 'Last Name',
					allowBlank: false,maskRe: /^[a-zA-Z]/ ,margins: '0'}
                    
                    
			,{xtype: 'datefield',name : 'person_birthdate',fieldLabel: 'Birthdate',width:config.width,allowBlank: true}
			,{xtype:'combo',mode:'local',value:'null',triggerAction:  'all',forceSelection: true,editable:       false,fieldLabel:     'Gender',
				name:'person_gender',displayField:   'name',valueField:     'value',queryMode: 'local',
				store:          Ext.create('Ext.data.Store', 
				{
					fields : ['name', 'value'],
					data   : [
					    {name : 'M',   value: 'M'},
					    {name : 'F',  value: 'F'},
					    {name : ' - ', value: 'null'}
					]
				})
			}          

		];
		
		
		this.callParent(arguments); 
		//.info('Spectrum.forms.person_details created');
	}
	
	
});	}


var pa = "Spectrum.forms.person_address";
if(!App.dom.definedExt(pa)){
Ext.define(pa,
{ 
	extend: 'Ext.spectrumforms', 
    initComponent: function(config) {this.callParent(arguments);},
 
    constructor     : function(config)
    {        
    	if(typeof config.hide_type == 'undefined') config.hide_type = false;
    	if(!config.ext_address_type_id)config.ext_address_type_id=Math.random();//for getstarted screen
		config.autoScroll  = false;
	    if(!config.id) { config.id = 'person_address_form_'}
		config.bodyPadding= bodyPadding;
		config.border=false;
		
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.bodyStyle) { config.bodyStyle = subStyle; }
		if(!config.width)     { config.width = subWidth; }
		if(!config.height)    { config.height = subHeight; }
		if(!config.fieldDefaults) 
		{
			config.fieldDefaults =  {labelWidth: subLblWidth,msgTarget: 'under',autoFitErrors: true};
		}
		if(!config.defaults) config.defaults={};
		config.defaults.anchor= '100%';
		config.defaults.validateOnChange =false;
		if(!config.address_ptr)config.address_ptr=Math.random();
		config.defaults.validateOnBlur=true;
		config.items = 
		[

			{xtype: 'hidden',    name : 'address_id',fieldLabel: '',value:-1,allowBlank: true,id:config.address_ptr}
			,{xtype: 'textfield',name : 'address_street',fieldLabel: 'Street',width:config.width,allowBlank: true}
			,{xtype: 'textfield',name : 'address_city',fieldLabel: 'City',width:config.width,allowBlank: true}
			,{xtype: 'textfield',name : 'postal_value',fieldLabel: 'Postal',minLength:5,maxLength:8,width:config.width,allowBlank: true}
			//region in and countyr removed: BROKEN. so this is the culprit
			
			//IE:Unable to get value of the property 'name': object is null or undefined 

			//IE: if both region AND country removed, works. with both, it doesnt.
			//with countrty IN, and region REMOVED: still works
			
			//region in and countyr removed: BROKEN. so this is the culprit
			,{
				xtype:          'combo',
				mode:'local'
				,value:'null'
				,triggerAction:  'all'
				,forceSelection: true,//means they cannot use their own values
				typeAhead:true,//can type 'c' to select canada for example - like autocomplete
				fieldLabel:     'Region',
				name:'region_abbr'
				,displayField:   'name'
				,valueField:     'value'
				,queryMode: 'local',
				store:          Ext.create('Ext.data.Store', 
				{
					fields : ['name', 'value'],
					data   : [
						{name : '', value: 'null'},
						{name : 'BC',  value: 'BC'},
						{name : 'AB',  value: 'AB'},
						{name : 'SK',  value: 'SK'},
						{name : 'MB',  value: 'MB'},
						{name : 'ON',  value: 'ON'},
						{name : 'QC',  value: 'QC'},
						{name : 'NL',  value: 'NL'},
						{name : 'NS',  value: 'NS'},
						{name : 'NB',  value: 'NB'},
						{name : 'NL',  value: 'NL'},
						{name : 'PE',  value: 'PE'},
						{name : 'YT',  value: 'YT'},
						{name : 'NU',  value: 'NU'}//there was a comma here. IE was just too shy to tell me.
					]
				})
			}
			
			,{
				xtype:'combo'
				,mode:'local'
				,value:'null'
				,triggerAction:  'all'
				,forceSelection: true,//means they cannot use their own values
				//,editable:       false// have to remove editable so taht typeahead will work
				
				fieldLabel:     'Country'
				,typeAhead:true,
				name:'country_abbr'
				,displayField:   'name'
				,valueField:'value'
				,queryMode: 'local',
				store:          Ext.create('Ext.data.Store', 
				{
					fields : ['name', 'value'],
					data   : [
						{name : ' - ', value: 'null'},
						{name : 'Canada',  value: 'CA'}
						
					]
				})
			}
			,{
				xtype:          'combo',
				id:config.ext_address_type_id,
				mode:           'local',
				value:          'null',
				triggerAction:  'all',
				forceSelection: true,
				editable:       false,
				fieldLabel:     'Type',
				hidden : config.hide_type,
				name:           'address_type',
				value:'1',//default type
				displayField:   'name',
				valueField:     'value',
				queryMode: 'local',
				enableKeyEvents : true,
				store:          Ext.create('Ext.data.Store', 
				{
					fields : ['name', 'value'],
					data   : [
					
						{name : 'Shipping',     value: '1'},//from public.lu_address_type
						{name : 'Operational',  value: '2'}
					
					]
				})
			}

		];
		
		
		
	
		this.callParent(arguments); 
		//.info('Spectrum.forms.person_address created');
	}	
	
	
	
});	}

var pc="Spectrum.forms.person_contact";
if(!App.dom.definedExt(pc)){
Ext.define(pc,
{
	//extend: 'Ext.form.Panel', 
	extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
           
    constructor     : function(config)
    {        
		config.autoScroll  = false;
		var id='person_contact_form_';
	    if(!config.id) { config.id =id;}
		config.bodyPadding= bodyPadding;
		config.border=false;
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.bodyStyle) { config.bodyStyle = subStyle; }
		if(!config.width)     { config.width = subWidth; }
		if(!config.height)    { config.height = subHeight; }
		if(!config.fieldDefaults) 
		{
			config.fieldDefaults =  {labelWidth: subLblWidth,msgTarget: 'under',autoFitErrors: true};
		}
		if(!config.defaults) config.defaults= {anchor: '100%'};

		config.items=
		[
			{xtype: 'fieldcontainer',fieldLabel: 'Home',combineErrors: true,msgTarget: 'under', 
				layout:{type: 'hbox', defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}},
				defaults: {    hideLabel: true  ,allowBlank: true,hideTrigger: true, xtype: 'textfield',maskRe:/\d/  
							,validateOnChange :false,validateOnBlur:true },
				items: 
				[
				//{xtype: 'displayfield', value: '('},
				{ id:"phone-home-1" ,  minLength:3,    maxLength:3,  fieldLabel: 'Area Code', name: 'home-ac', width: 32
                ,listeners:
                {
                    change:
                    {
                            fn: function(_this, newValue, oldValue, eOpts )
                            {
                                if(newValue.length==3)
                                Ext.getCmp("phone-home-2").focus(true);
                                
                            },
                            scope: this, 
                            buffer:1
                    } 
                }
                },
				//{xtype: 'displayfield', value: ')'},
				{xtype: 'displayfield', value: '-'},
				{ id:"phone-home-2" ,   minLength:3,    maxLength:3, fieldLabel: 'Phone Prefix', name: 'home-pre', width: 32
                ,listeners:
                {
                    change:
                    {
                            fn: function(_this, newValue, oldValue, eOpts )
                            {
                                if(newValue.length==3)
                                Ext.getCmp("phone-home-3").focus(true);
                                
                            },
                            scope: this, 
                            buffer:1
                    } 
                }
                },
				{xtype: 'displayfield', value: '-'},
				{  id:"phone-home-3" ,  minLength:4,    maxLength:4, fieldLabel: 'Phone Number', name: 'home-num', width: 48
                    ,listeners:
                    {
                        change:
                        {
                                fn: function(_this, newValue, oldValue, eOpts )
                                {
                                    if(newValue.length==4)
                                    Ext.getCmp("phone-mobile-1").focus(true);
                                },
                                scope: this, 
                                buffer:1
                        } 
                    }
                }
				]
			},//END PHONE fcontainer
			{xtype: 'fieldcontainer',fieldLabel: 'Mobile',combineErrors: true,msgTarget: 'under',
				layout:{type: 'hbox', defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}},
				defaults: {    hideLabel: true  ,allowBlank: true,hideTrigger: true, xtype: 'textfield', maskRe:/\d/
						,validateOnChange :false,validateOnBlur:true },
				items: 
				[
				//{xtype: 'displayfield', value: '('},
				{ id:"phone-mobile-1" , hideTrigger: true, minLength:3,    maxLength:3,  fieldLabel: 'Area Code', name: 'mobile-ac', width: 32
                ,listeners:
                {
                        change:
                        {
                                fn: function(_this, newValue, oldValue, eOpts )
                                {
                                    if(newValue.length==3)
                                    Ext.getCmp("phone-mobile-2").focus(true);
                                },
                                scope: this, 
                                buffer:1
                        } 
                }
                },
				//{xtype: 'displayfield', value: ')'},
				{xtype: 'displayfield', value: '-'},
				{ id:"phone-mobile-2" ,    minLength:3,    maxLength:3, fieldLabel: 'Phone Prefix', name: 'mobile-pre', width: 32 
                ,listeners:
                    {
                        change:
                        {
                                fn: function(_this, newValue, oldValue, eOpts )
                                {
                                    if(newValue.length==3)
                                    Ext.getCmp("phone-mobile-3").focus(true);
                                },
                                scope: this, 
                                buffer:1
                        } 
                    }
                },
				{xtype: 'displayfield', value: '-'},
				{ id:"phone-mobile-3" ,    minLength:4,    maxLength:4, fieldLabel: 'Phone Number', name: 'mobile-num', width: 48 
                ,listeners:
                    {
                        change:
                        {
                                fn: function(_this, newValue, oldValue, eOpts )
                                {
                                    if(newValue.length==4)
                                    Ext.getCmp("phone-work-1").focus(true);
                                },
                                scope: this, 
                                buffer:1
                        } 
                    }
                }
				]
			},//END PHONE fcontainer

			{xtype: 'fieldcontainer',fieldLabel: 'Work',combineErrors: true,msgTarget: 'under',
				layout:{type: 'hbox', defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}},
				defaults: {    hideLabel: true  ,allowBlank: true,hideTrigger: true, xtype: 'textfield',maskRe:/\d/ 
							,validateOnChange :false,validateOnBlur:true	},
				items: 
				[
				//{xtype: 'displayfield', value: '('},
				{ id:"phone-work-1" , minLength:3,    maxLength:3,//minValue: 0, maxValue:999, 
				fieldLabel: 'Area Code', name: 'work-ac' , width: 32 
                ,listeners:
                    {
                        change:
                        {
                                fn: function(_this, newValue, oldValue, eOpts )
                                {
                                    if(newValue.length==3)
                                    Ext.getCmp("phone-work-2").focus(true);
                                },
                                scope: this, 
                                buffer:1
                        } 
                    }},//
				//{xtype: 'displayfield', value: ')'},
				{xtype: 'displayfield', value: '-'},
				{ id:"phone-work-2" ,     minLength:3,    maxLength:3, fieldLabel: 'Phone Prefix', name: 'work-pre' ,width: 32 
                ,listeners:
                    {
                        change:
                        {
                                fn: function(_this, newValue, oldValue, eOpts )
                                {
                                    if(newValue.length==3)
                                    Ext.getCmp("phone-work-3").focus(true);
                                },
                                scope: this, 
                                buffer:1
                        } 
                    }
                },
				{xtype: 'displayfield', value: '-'},
				{id:"phone-work-3" ,    minLength:4,    maxLength:4, fieldLabel: 'Phone Number', name: 'work-num', width: 48 
                ,listeners:
                    {
                        change:
                        {
                                fn: function(_this, newValue, oldValue, eOpts )
                                {
                                    if(newValue.length==4)
                                    Ext.getCmp("phone-work-4").focus(true);
                                },
                                scope: this, 
                                buffer:1
                        } 
                    }
                },
				{xtype: 'displayfield', value: ':'},
				{id:"phone-work-4" , maxLength:8, fieldLabel: 'Ext', name: 'work-ext', width: 48
                ,listeners:
                    {
                        change:
                        {
                                fn: function(_this, newValue, oldValue, eOpts )
                                {
                                    if(newValue.length==4)
                                    Ext.getCmp("phone-email").focus(true);
                                },
                                scope: this, 
                                buffer:1
                        } 
                    }
                }
				]
			}//END PHONE fcontainer
			,{ id:"phone-email" , xtype     : 'textfield',name      : 'email',width:config.width,fieldLabel: 'Email Address',
				vtype: 'email',msgTarget: 'under',allowBlank: true,validateOnChange :false,validateOnBlur:true }
		];

        this.callParent(arguments); 
		//.info('Spectrum.forms.person_contact created');
	}
    ,afterRender: function() 
    {  
        var me=this;
    }	
	
});	}


var ul="Spectrum.forms.user_login";
if(!App.dom.definedExt(ul)){
Ext.define(ul,
{
	//extend: 'Ext.form.Panel', 
	extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
           
    constructor     : function(config)
    {                                  
    	 
 		if(typeof config.login_optional=='undefined') config.login_optional=false;//default  false
		config.autoScroll  = false;
		config.bodyPadding= bodyPadding;
		config.border=false;
		if(!config.id) { config.id = 'user_login_form_'}
		
		if(typeof config.allowBlank=='undefined') { config.allowBlank=true;}//is changable
		if(typeof config.passwordLength=='undefined') { config.passwordLength=1;}//is changable
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.bodyStyle) { config.bodyStyle = subStyle; }
		if(!config.width)     { config.width = subWidth; }
		if(!config.height)    { config.height = subHeight; }
		if(!config.fieldDefaults) 
		{
			config.fieldDefaults =  {labelWidth: subLblWidth,msgTarget: 'under',autoFitErrors: true};
		}
		if(!config.defaults) config.defaults= {anchor: '100%'};
		
		config.items = 
		[
			{xtype:'hidden',name:'login_optional',value:config.login_optional,id:'frm_config_login_optional'},
			{xtype: 'textfield',id:"login",name : 'login',fieldLabel: 'Login',width:config.width,allowBlank: 
				config.allowBlank},
			{xtype: 'textfield',inputType: 'password',fieldLabel: 'Password',width:config.width,name: 'pass',id: 'pass',minLength:10,
				allowBlank: config.allowBlank},
			{xtype: 'textfield',inputType: 'password',fieldLabel: 'Confirm Password',width:config.width,name: 'pass-cfrm',id:'pass-cfrm',vtype: 'password',allowBlank: 
				config.allowBlank,
				initialPassField: 'pass' // id of the initial password field
			},
            {
                    xtype   : 'checkboxfield',
                    id      :'cb-auto-pass',
                    name    :'cb-auto-pass',
                    fieldLabel:'Auto Login ? ',
                    listeners:
                    {
                        change:function(_this,checked)
                        {
                            if(checked==true)
                            {
                                  var userName=Ext.getCmp("form_data_fname").getValue();
                                  var lastName=Ext.getCmp("form_data_lname").getValue();
                                  
                                  //AJAX CALL TO GET NEW PASSWORD
                                  var post={pass_len:10};
                                  Ext.Ajax.request(
                                    {
                                         url     : 'index.php/permissions/json_genRandomString/TOKEN:'+App.TOKEN,
                                         params  : post,
                                         success : function(response)
                                         {
                                              var result=YAHOO.lang.JSON.parse(response.responseText).result;
                                              Ext.getCmp("login").setValue(userName.substring(0,1)+lastName  + result.substring(0,5));
                                              Ext.getCmp("pass").setValue(result);
                                              Ext.getCmp("pass-cfrm").setValue(result);            
                                         }
                                    });                                                            
                            }
                            else
                            {
                                  Ext.getCmp("login").setValue('');
                                  Ext.getCmp("pass").setValue('');
                                  Ext.getCmp("pass-cfrm").setValue('');
                            }
                        }
                    }
            },
            {
                xtype       :"displayfield",
                html        : "<div style='color:teal'><i>Automatically create a login  username and password for this user</i></div>"
            }
		];

		this.callParent(arguments); 
	}	
});	}

//user edit forms combines other forms together


var uedit='Spectrum.forms.user_edit';
if(!App.dom.definedExt(uedit)){
Ext.define(uedit,
{
    //extend: 'Ext.form.Panel', 
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
     login_optional:false,      
    constructor     : function(config)
    {        
         
 		if(config.login_optional) this.login_optional=true;//default was false
		config.loadMask=false;
		var id='user_edit_form_';
		if(!config.id) config.id=id;
		config.bodyPadding= bodyPadding;
		config.border=false;
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
	    //.log('user form id is =',id);
	    //var userfields=getFields();
	    //create the sub forms
	    var userfields=new Array();
	    userfields[0] = Ext.create('Spectrum.forms.person_details',{window_id:config.window_id});//standard_width
	    userfields[1] = Ext.create('Spectrum.forms.person_address',{window_id:config.window_id});
	    userfields[2] = Ext.create('Spectrum.forms.person_contact',{window_id:config.window_id});
	    userfields[3] = Ext.create('Spectrum.forms.user_login',    {window_id:config.window_id,login_optional:config.login_optional});
        userfields[0].hidden=false;
        userfields[1].hidden=false;
        userfields[2].hidden=false;
        userfields[3].hidden=false;
        
        if(!config.height)     { config.height  = 2*subHeight-20; }
		//if(!config.height)    { config.height = 2*subHeight; }
		
	    config.autoScroll  = false;
	    config.autoHeight  = true;
	    config.resizable   =false;
		config.bodyStyle   = subStyle;//padding: 10px;

		config.items=
		[
			{xtype: 'hidden', name : 'person_id',hidden:true,value:-1}
		    ,{xtype: 'hidden',name : 'user_id',  hidden:true,value:-1}
		    
            //,{xtype:'button',text   : 'Save',id:'tttttuser-btn-save',iconCls:'disk',handler: save_user} 
			//for horizontal
            ,{
            	xtype : 'fieldcontainer',
            	layout:'hbox', 
            	bodyStyle:'padding:10px',//width:2*subWidth,height:
			    items:[ userfields[0],userfields[1] ]
            } 
            ,{
            	xtype : 'fieldcontainer',
            	layout:'hbox', 
            	bodyStyle:'padding:10px',//width:2*subWidth,
			    items:[ userfields[2],userfields[3] ]
            }
            //,'->'
	
         ];//end of items:
         
         config.dockedItems=
        [
            {dock: 'bottom',xtype: 'toolbar',style:{border:0} //bodyStyle:'border:0 none;!important'//border:false,padding:0
                ,items:
                [
                    "->"
            		,{xtype:'button',text   : 'Save',id:'user-btn-save',iconCls:'disk',handler: save_user,cls:'x-btn-default-small'}       
                ]
            } 
        ]; //end of Docked Items   	

         this.callParent(arguments); 
    }//end of Constructor
});//end of Ext.define
}


//user create form combines other forms, with added grid/search/name test feature
var ucreate = "Spectrum.forms.user_create";
if(!App.dom.definedExt(ucreate)){
Ext.define(ucreate,
{
	//extend: 'Ext.form.Panel', //'Ext.spectrumforms'
	extend: 'Ext.spectrumforms', //'Ext.spectrumforms'
	initComponent: function(config) 
	{
	    this.callParent(arguments);
	},
	
	get_user_id:function()
	{
		if(Ext.getCmp('form_user_id'))
			return Ext.getCmp('form_user_id').getValue();
		else return -1;
	},
	get_person_id:function()
	{
		if(Ext.getCmp('form_person_id'))
			return Ext.getCmp('form_person_id').getValue();
		else return -1;
	},
	get_person_name:function()
	{
		if(Ext.getCmp('form_data_fname') && Ext.getCmp('form_data_lname'))
		return Ext.getCmp('form_data_fname').getValue()+" "+Ext.getCmp('form_data_lname').getValue();
		else return '';
	},
	login_optional:false,
	constructor     : function(config)
	{                
		var i,sub_ids=['mixer_north','mixer_center','mixer_south','mixer_left','mixer_right'];
        for(i in sub_ids) 
            if(sub_ids[i])
            {
			    if(Ext.getCmp(sub_ids[i]))Ext.getCmp(sub_ids[i]).destroy();//destroy if it is already used
			    //otherwise do nothing
            }
           
		config.bodyPadding= bodyPadding;
		config.border=false;
		if(!config.id){config.id='_user_form_';}
 
 		if(config.login_optional) this.login_optional=true;//default was false
 		if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
 		if(Ext.getCmp('div-dt-search')){Ext.getCmp('div-dt-search').destroy();}
 		if(Ext.getCmp('user-fieldset-grid')){Ext.getCmp('user-fieldset-grid').destroy();}
		    
	    var fields_user_edit=new Array();
		fields_user_edit[0] = Ext.create('Spectrum.forms.person_details',{id:'create_details',window_id:'create_user_form_window_'});//was passing height:200 to all
		fields_user_edit[1] = Ext.create('Spectrum.forms.person_address',{id:'create_address',window_id:'create_user_form_window_'});
		fields_user_edit[2] = Ext.create('Spectrum.forms.person_contact',{id:'create_contact',window_id:'create_user_form_window_'});
		fields_user_edit[3] = Ext.create('Spectrum.forms.user_login'    ,{id:'create_login'  ,window_id:'create_user_form_window_',login_optional:config.login_optional});
	    fields_user_edit[0].hidden=true;//force hidden
	    fields_user_edit[1].hidden=true;
	    fields_user_edit[2].hidden=true;
	    fields_user_edit[3].hidden=true;
		
		config.title= '';//,autoHeight  : true,resizable   :false,
	    config.autoScroll  = false;
		config.bodyStyle  =subStyle;
		
		config.items= 
		[
			{xtype:'hidden',id:'ctr_form_id',value:id},
			{xtype: 'hidden',name : 'person_id',id:'form_person_id',hidden:true,value:-1},
			{xtype: 'hidden',id:'form_user_id',name : 'user_id',hidden:true,value:-1},
			//search form
			{xtype: 'fieldset',title: 'Name',id:'user-fieldset-search',
			    items:
			    [          
			        {xtype: 'textfield',id:"fname", name : 'fname',fieldLabel: 'First Name',maskRe     : /^[a-zA-Z]/ ,width:400,allowBlank: false},
			        {xtype: 'textfield',id:"lname",width:400,name : 'lname',fieldLabel: 'Last Name',allowBlank: false,maskRe: /^[a-zA-Z]/ ,margins: '0'}
			      ]
			}
			//the grid goes here
			,{xtype:'displayfield',hidden:true,value:"Searching for similar users",id:'create_user_loading_msg'}
			,{xtype:'fieldset',id:'user-fieldset-grid',hidden:true,border:false,frame:false,loadMask:false,
			    title:'Did you mean one of these users? If so, select a row and click "Yes".',
			    items:
			        [
			        //{xtype:'displayfield',value:'Did you mean one of these users? If so, select a row and click "Yes".'},
		            {xtype:'grid',id : 'div-dt-search',store:[]
		           // store:Ext.create( 'Ext.data.Store',{remoteSort:false,autoDestroy: false,loadMask:false,model:user_model,data: []
								//,proxy: {type: 'localstorage'}} )
		            ,height: 250,width:'100%',title:'Similar Users.',loadMask:false,
		            columns: 
		            [
		                {text   : 'First Name',flex: 1,sortable : true,dataIndex: 'person_fname'}
		                ,{text   : 'Last Name',flex: 1,sortable : true,dataIndex: 'person_lname'}
		                //,{text   : 'Birthdate',sortable : true,dataIndex: 'person_birthdate'}
		                ,{text   : 'Email',flex: 1,    sortable : true,dataIndex: 'email'}
		            ],
	                bbar:
	                [
	                { 
	                	disabled:false, 
	                	iconCls: 'tick',
	                	text:'Yes',
	                	tooltip: 'Load this users information'
	                	,cls:'x-btn-default-small',
	                	scope:this,
	                	handler : function()
	                	{
	                     
	                    var s_grid = Ext.getCmp('div-dt-search');

	                    var rowsSelected = s_grid.getSelectionModel().getSelection();
	                    if(rowsSelected.length>0)            
	                    {
	                        var rec=rowsSelected[0];
							Ext.getCmp('form_person_id').setValue(rec.get('person_id'));
							
	                        //var o_form=Ext.getCmp(Ext.getCmp('ctr_form_id').getValue()); //form id depends on what they gave it  
	                        
	                        console.log(form);                         
	                        console.log(rec);                         
	                        var form=this.getForm();
	                        form.loadRecord(rec);
	                        //hide search components
	                        Ext.getCmp('user-fieldset-search').hide();    
	                        Ext.getCmp('user-fieldset-grid').hide();
	                        //and show form components
	                        Ext.getCmp('create_details').show();    
			                Ext.getCmp('create_address').show();    
			                Ext.getCmp('create_contact').show();
			                Ext.getCmp('create_login').show();
	                        Ext.getCmp('user-btn-save').show();    
	                    	//.log('YES button complete');                                
	                    }
	                    else
	                    {
	                        Ext.MessageBox.alert("Required:","Select a Row first");
	                    }
	                        
	                }}
	                ,'->'
	                ,{id:'btn-bbar-search-no', disabled:false, iconCls: 'cancel',text:'No',tooltip: 'Continue with creating a user'
	                ,cls:'x-btn-default-small',handler : function()
	                {
	                    //hide this search stuff
	                    Ext.getCmp('user-fieldset-search').hide();    
	                    Ext.getCmp('user-fieldset-grid').hide();
	                    Ext.getCmp('user-btn-next').hide();
	                    //.log('NO button, so show withotu load');
	                    //show the form stuff    
	                    Ext.getCmp('create_details').show();    
	                    Ext.getCmp('create_address').show();    
	                    Ext.getCmp('create_contact').show();
	                    Ext.getCmp('create_login').show();
	                    Ext.getCmp('user-btn-save').show();
	                }}
	                
	                ]
	            }
	        ]
	        
	    }
	    //for horizontal
        ,{
        	xtype : 'fieldcontainer',
        	layout:'hbox',
			items:[ fields_user_edit[0],fields_user_edit[1] ]
        } 
        ,{
        	xtype : 'fieldcontainer',
        	layout:'hbox',
			items:[ fields_user_edit[2],fields_user_edit[3] ]
        }
	        
	    ];//end of MAIN items
	    config.dockedItems=
	    [
	        {dock: 'bottom',xtype: 'toolbar',style:{border:0},border:false,
	            items:
	            [
	                "->",
	                {text   : 'Next',id:'user-btn-next',cls:'x-btn-default-small',iconCls:'arrow_right',handler: function() 
	                    {
                            if(Ext.getCmp('mixer_left'+config.finalFormGen))Ext.getCmp('mixer_left'+config.finalFormGen).collapse();
                        	Ext.getCmp('user-btn-next').hide();
	                        Ext.getCmp('user-fieldset-search').hide();
	                        Ext.getCmp('create_user_loading_msg').show();
	                        //Ext.getCmp('user-fieldset-address').hide();
	                        var form = this.up('form').getForm();
	                        var form_data=new Array();
	                        var rec={};
	                        Ext.iterate(form.getValues(), function(key, value) 
	                        {
	                            if(key=='fname'||key=="lname")
	                            {
	                                form_data.push(key+"="+escape(value));
	                                //these next guys are just for loading the name in two loations
	                                rec[key]=value;
	                                //rec['person_'+key]=value;
	                            }
	                        }, this);

	                        //.log(Ext.getCmp('form_data_fname'));

	                        Ext.getCmp('form_data_fname').setValue(rec['fname']);
	                        Ext.getCmp('form_data_lname').setValue(rec['lname']);
	                        var post= form_data.join('&');
	                        
	                        var url="index.php/person/post_search_people/"+App.TOKEN;
	                        var callback={success:function(o)
	                        {
	                            var ppl=YAHOO.lang.JSON.parse(o.responseText);
	                            
			                    Ext.getCmp('create_user_loading_msg').hide();//toggle loading message
	                            if(ppl.length==0)
	                            {
	                                //.log('zero found so continue');//copied this from the no button
	                                //hide this search stuff
	                                Ext.getCmp('user-fieldset-search').hide();    
	                                Ext.getCmp('user-fieldset-grid').hide();//div-dt-search
	                                //show the form stuff        
	                                Ext.getCmp('create_details').show();    
				                    Ext.getCmp('create_address').show();    
				                    Ext.getCmp('create_contact').show();
				                    Ext.getCmp('create_login').show();
	                    
	                                Ext.getCmp('user-btn-save').show();
	                                //Ext.getCmp('user-btn-next').hide();
	                            }
	                            else
	                            {
                                	
	                                //.log(ppl);
	                                var table=Ext.getCmp('div-dt-search');
	                                table.store.loadData(ppl,false);
	                                Ext.getCmp('user-fieldset-grid').show();
	                            }
	                        }};
	                        YAHOO.util.Connect.asyncRequest('POST',url,callback,post);                        
	                    }
	                },
	                {text   : 'Save',id:'user-btn-save',cls:'x-btn-default-small',iconCls:'disk',hidden:true,handler: save_user}        
	            ]
	        } 
	    ] ;//end of config definition              

	    this.callParent(arguments);    // }//end the finally statement
	}//end of Constructor
});//end of Ext.define
}





