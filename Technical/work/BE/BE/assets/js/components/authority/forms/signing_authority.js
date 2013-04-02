  
var fnm = 'Spectrum.forms.signing_authority';
 
if(!App.dom.definedExt(fnm)){
Ext.define(fnm,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config)
    {  
    	//.log('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
		var id='c_sanew_form';//+Math.random();
    	if(!config.id){config.id=id};
    	this.window_id=config.window_id;

    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
    	if(Ext.getCmp('innner_usersgrid')){Ext.getCmp('innner_usersgrid').destroy();}
		if(!config.title)config.title= '';
		this.window_id=config.window_id;
 
    	 
	    config.items=
	    [
			Ext.create("Ext.spectrumgrids.user",
	        {
	            //width   :200,
	            height  :200,
				id:'innner_usersgrid',
	            title           : 'Available Users',

	            url             :"index.php/permissions/json_org_users_distinct/"+App.TOKEN

	            
	        })
	    ];
 
			config.bottomItems=
			[
				'->',
				{   
		             xtype   :"button",
		             text    :"Save",
		             scope:this,
		             tooltip :'Apply Motion for this User',
		             handler :function()
		             {                
		                var users =    Ext.getCmp('innner_usersgrid').getSelectionModel().getSelection();
		                if(users.length==0)
		                {
		                    Ext.MessageBox.alert({title:"Cannot Save Yet",msg:"Please select a User", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
		                    return;
		                } 

		                var recordu=users[0];
		                 
		                                                        
		                var post={}
		                post["user_id"]= recordu.get('user_id');
		                post["role_id"]= 16;//shouldnt be neeeded but just in case
		                
		                var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
		                Ext.Ajax.request
		                ({
		                    url     : 'index.php/finance/json_new_motion_assignment_add/'+App.TOKEN,
		                    params  : post,
		                    scope:this,
		                    success : function(o)
		                    {
		                         box.hide();
		                         var res=Ext.JSON.decode(o.responseText);
		                         if(res.result=="1")
		                         { 
		                             Ext.MessageBox.alert(
		                             {
		                                 title:"Status",
		                                 msg:"Request Sent Successfully", 
		                                 icon: Ext.Msg.INFO,
		                                 buttons: Ext.MessageBox.OK
		                             });
		                             if(Ext.getCmp(this.window_id))Ext.getCmp(this.window_id).hide();
		                             
		                         }           
		                         if(res.result=="2")
		                             Ext.MessageBox.alert(
		                             {
		                                 title:"Status",
		                                 msg:"Selected User already has a valid or pending Signing Authority status", 
		                                 icon: Ext.Msg.INFO,
		                                 buttons: Ext.MessageBox.OK
		                             });     
		                         if(res.result=="-1")
		                             Ext.MessageBox.alert(
		                             {
		                                 title:"Error",
		                                 msg:"Still pending Signing Authority motion in system for this user", 
		                                 icon: Ext.Msg.ERROR,
		                                 buttons: Ext.MessageBox.OK
		                             });
		                             
		                    },
		                    failure: function(o){box.hide();App.error.xhr(o);}
		                });    
		             }                          
		        }];
 
	        	
        this.callParent(arguments); 
	}
	
	
	
});}
 