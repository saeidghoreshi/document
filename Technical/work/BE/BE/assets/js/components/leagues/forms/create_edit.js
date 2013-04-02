var lgf="Spectrum.forms.league";
if(!App.dom.definedExt(lgf)){
Ext.define(lgf,
{
	extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    setDisabled_savebtn:function(bool)
    {
		if(Ext.getCmp('create_league_frm_btn_'))Ext.getCmp('create_league_frm_btn_').setDisabled(bool); 
    },
    window_id:null,           
    save:function()
    {
		var form=this.getForm();
	    if (!form.isValid()) {return;} 
	    
        //Ext.getCmp('create_league_frm_btn_').setDisabled(true);
         
		this.setDisabled_savebtn(true);
	    //
	   // Ext.Ajax.request(
	    form.submit(
	    {
	        url     : 'index.php/associations/json_createnewleague/'+App.TOKEN,
	       // params  : form.getValues(),
	         method:'POST',
	        scope:this,
	        success : function(frm,act)
	        {
 				var o=act.response;
                try
                {
                	// try catch is just hunting for IE errors
		            var res=YAHOO.lang.JSON.parse(o.responseText);
				}
				 catch(e)
	            {
					App.error.xhr(o);
					return;
	            }
	            finally
	            {
					//false
					this.setDisabled_savebtn(false);
            		//if(Ext.getCmp('create_league_frm_btn_'))Ext.getCmp('create_league_frm_btn_').setDisabled(false); //always re enable, even fi window does not close
	            }
	             
		        if(res.success == true||res.success=='true')
		        {
		            //if the window exists then hide it
		            if(this.window_id && Ext.getCmp(this.window_id))
		                Ext.getCmp(this.window_id).hide();
		            else //otherwise display save message
		                Ext.MessageBox.alert(
		                	{title:"Status"
		                	,msg:"League Created Successfully."
		                	, icon: Ext.Msg.INFO
		                	,buttons: Ext.MessageBox.OK
		                	});
		        }     
		        else
		        {
					if(res.result.park == -5)
					{
		                Ext.MessageBox.alert(
		                {
		                	title:"Website Conflict"
		                	,msg:"This website address is in use by another league.  Try a different <b>Website Prefix</b>."
		                	, icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK
		                });
						
					}
					else
					{
						Ext.MessageBox.alert(
		                {
		                	title:"CPANEL update was rejected. "
		                	,msg:res.result.park+" "+res.result.unpark
		                	, icon: Ext.Msg.INFO
		                	,buttons: Ext.MessageBox.OK
		                });
					}
					
		        }
			
	            
	        },
	        failure: function(f,o)
	        {
 
                App.error.xhr(o);
                Ext.getCmp('create_league_frm_btn_').setDisabled(false);
	        }
	    }); 
    },
    constructor     : function(config)
    {        
    	if(typeof config.save_btn == 'undefined') {config.save_btn=true;}
    	if(typeof config.hide_upload == 'undefined') {config.hide_upload=false;}
    	if(typeof config.hide_email == 'undefined') {config.hide_email=true;}
    	if(typeof config.border == 'undefined') {config.border=false;}
    	//var hide_save = !config.save_btn;
    	this.window_id=config.window_id;
    	if(!config.title)config.title='';
 
    	 
		config.fieldDefaults=
        {
 			 labelWidth: 150
        };
 		
 		config.centerAll=true;//this takes care of most of the formatting for us

		config.items=
		[
			{xtype:'hidden',name:'league_id',value:-1}//default for creating new league.  if editing a record this is set by loadRecord
			,{
                xtype       : 'textfield',
                name        : "league_name",
                fieldLabel  : 'League Name',
                allowBlank  : false,
 				maskRe: /^[a-zA-Z0-9_\s]/ ,//alphabetic only, or numbers or underscores. also whitespace /s
                validateOnChange :false,
                validateOnBlur:true 
            }
			,{
                xtype       : 'textfield',
                name        : "websiteprefix",
                fieldLabel  : 'Website Prefix',
                allowBlank  : false, 
                maskRe: /^[a-zA-Z0-9_]/ ,//alphabetic only, or numbers or underscores
                validateOnChange :false,validateOnBlur:true 
            }
          /*  ,{xtype     : 'textfield',name      : 'email',width:config.width,fieldLabel: 'Create User',hidden:config.hide_email,
				vtype: 'email',msgTarget: 'under',allowBlank: config.hide_email// if hidden is true, then its allowed to be blank
				,validateOnChange :false,validateOnBlur:true 
			}*/
            ,{
                 id          : 'domainlist',   
                 xtype       : 'combo',
                 name        : 'domainname',
                 fieldLabel  : 'Domain',
                 allowBlank  : false,
                 mode        : 'local',
                 forceSelection: true,
                 editable    : false,
                 displayField: 'domain',
                 valueField  : 'domain',
                 queryMode   : 'local',
              //   margins     :'0 0 0 5', 
                 store       : config.domainListStore

            }
            ,{  
               // id          : 'file_upload',                      
                xtype       : 'filefield', 
                name        : 'file_upload',
                emptyText   : 'Upload League Logo  ',
                fieldLabel  : '',
                //labelWidth  : 150,          
                //flex        :1,
                width:310,   
                hidden:     config.hide_upload,//creator of object can hide this
                allowBlank  : true
            }
		];
		if(config.save_btn) 
		{//if we want a save button
			
			if(!config.bottomItems)config.bottomItems=new Array();
			config.bottomItems.push(
		//	{dock: 'bottom',xtype: 'toolbar',style:{border:0},
		//	items:
		//	[
	            '->'
	            ,{
            		text: 'Save',
            		iconCls: "disk",
            		cls:'x-btn-default-small',
            		id:'create_league_frm_btn_',
            		scope:this,
            		//width:70,
            		handler: function()
		            {
            			this.save();
		                
		            }
		        });
       // ]
        
        
		}//end of if save_btn
		this.callParent(arguments); 

		if(config.domainListStore.getCount())
		{
			//if store is not empty, then select top one by default
			var first=config.domainListStore.getAt(0);
			if(Ext.getCmp('domainlist'))Ext.getCmp('domainlist').select(first.get('domain'));//select goes by valueField of the combo
			
		}
		
	}
});}
