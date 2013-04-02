
var formorgrole = 'Spectrum.forms.org_roles';
if(!App.dom.definedExt(formorgrole)){         
Ext.define(formorgrole,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    user_id:0,//default
    url:'index.php/permissions/post_create_assignment/'+App.TOKEN,
    constructor     : function(config)
    {        
    	
    	if(!config.id){config.id='roles_form_grid_window_';}
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
    	//dt_allowed_roles_org
    	this.org_id=config.org_id;
    	this.window_id=config.window_id;
        
        this.org_name=config.org_name;
    	
    	//this.org_type=config.org_type;
    	this.person_name=config.person_name;
    	
		this.person_id=config.person_id;
		
    
        //now make the form itself 
        config.title='Assigning To : '+this.person_name;
        config.height=350;
        config.width= '100%';
        
        config.items= 
        [      
	        //{xtype: 'displayfield',fieldLabel: 'Assigning To',name : 'full_name',id:'assign_full_name',value:this.person_name},
	        Ext.create('Spectrum.grids.org_roles',{org_type:config.org_type,id:'insideform_org_roles_grid'})	
        ];
        config.dockedItems=
        [
            {dock: 'bottom',xtype: 'toolbar',items:[
                '->',
                {text   : 'Save',id:'btn_save_roles_fixed',iconCls:'disk',cls:'x-btn-default-small',scope:this,handler: function() 
                {
                    var grid=Ext.getCmp('insideform_org_roles_grid');
                    var checked = grid.getSelectionModel();//.getSelected();
                    var records=checked.getSelection();
                    var role_ids=new Array();

                    for(i in records)
                    {
                        if(typeof records[i].data != 'undefined') //IE9
	                        role_ids.push(records[i].data.role_id);
	                    else if(typeof records[i].get == 'function')
	                        role_ids.push(records[i].get('role_id'));
                    }
                    if(role_ids.length==0) return;
                    var json_role_ids = YAHOO.lang.JSON.stringify(role_ids);
                    var post="user_id="+this.user_id+"&role_ids="+json_role_ids+"&org_id="+this.org_id+"&person_id="+this.person_id;

                    
                    var callback={
                    scope:this,
                    success:function(o)
                    {
                        var r=o.responseText;
                        if(isNaN(r) || r<0)
                        {
                        	//if(r==-1 || r=="-1")
                        		r="User not found, please make sure this person has a valid user account (user name and password)";
                        	
                            Ext.MessageBox.alert("Error",r);
                            //App.error.xhr(o);
                        }
                        else
                        {
                            if(Ext.getCmp(this.window_id))
                                Ext.getCmp(this.window_id).hide();
                            else
                            {
                               // Ext.MessageBox.alert({title:"Status",msg:"Selected Role(s) Saved For "+this.person_name, icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                this.hide();
                            }
                                
                            this.destroy();//testing this
                            //App.forms.winAssignRoles.hide();
                        }
                    },failure:App.error.xhr};
                    YAHOO.util.Connect.asyncRequest('POST',this.url,callback,post);
                }} 
            ]
            }
        ];   
        
                 
			 
    	this.callParent(arguments); 
	}
	
	
	
});}
