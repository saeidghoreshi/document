if(!App.dom.definedExt('Ext.spectrumgrids.sa_assignment')){//very important workaround for IE and sometimes chrome
Ext.define('Ext.spectrumgrids.sa_assignment',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    },
    
     override_edit          :null
    ,override_selectiochange:null
    ,override_itemdblclick  :null
    ,override_collapse      :null
    ,override_expand        :null
    
      
    ,config                 :null
    ,org_type_id            :null
    ,constructor : function(config) 
    {                         
        var me=this;
        
        // (Sam) I added  extra parameters, to allow columns  to be hidden
        //if parameters not given, assume NOT hidden, of course
        if(typeof config.hide_role   =='undefined') {config.hide_role   =false;}
        if(typeof config.hide_created=='undefined') {config.hide_created=false;}
        
        //also a parameter to skip existing motions buttons, in case we want to disable those functions. used for getstarted screen 
        
        if(typeof config.disable_motions=='undefined') {config.disable_motions=false;}
        
        config.columns  =
        [    
            {
                    text    : 'Assignment Details',
                    columns :
                    [
                        {
                            text        : "Role"
                            ,dataIndex  : 'role_name'
                            ,width      : 300
                            ,hidden     :config.hide_role
                        }
                        ,{
                            text        : "User"
                            ,dataIndex  : 'person_name'
                            ,width      : 200 
                        }
                    ]
            }
            ,{
                text        : "Status"
                ,xtype      :'templatecolumn'
                ,tpl        :'<div style=text-align:center;>{[values.motion_status_id == "1" ? "<img src='+config.imageBaseUrl+'hourglass.png>" :(values.motion_status_id  == "2" ? "<img src='+config.imageBaseUrl+'tick.png>" : (values.motion_status_id == "3" ? "<img src='+config.imageBaseUrl+'delete.png>" :"<img src='+config.imageBaseUrl+'lightning.png>"))]}'      
                ,width      : 70
             }
            ,{
                text        : "Action"
                ,dataIndex  : 'action'
                ,width      : 50
            }
            ,{
                text        : "Created By"
                ,dataIndex  : 'created_by_name'
                ,flex       :1   
                ,hidden     :config.hide_created  
            }
        ];
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        
        var motionStatus           =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_motion_status/"+App.TOKEN+'/',{test:true});
        config.topItems.push
        (
            //  FILTER BASED ON MOTION-RULE STATUS
            {  
                xtype       : 'combo',
                labelStyle  : 'font-weight:bold;padding:0',
                name        : 'motion_status_id',
                emptyText   : '',
                fieldLabel  : 'Motion Status',
                labelWidth  : 100,    
                forceSelection: false,
                editable    : false,
                displayField: 'type_name',
                valueField  : 'type_id',
                queryMode   : 'local',     
                allowBlank  : false,
                store       : motionStatus,
                listeners   :
                {
                    change:function(_this,selected_id)
                    {             
                        me.getStore().proxy.extraParams.motion_status_id=selected_id;
                        me.getStore().load();
                    }                        
                    
                }    
            }
        );
        
        config.bottomRItems.push
        (
             //make motion delete Motion  request
             {
                    iconCls : 'application_form_delete',
                    xtype   : 'button',
                    text    : '',
                    scope:this ,
                    tooltip : 'Apply for Motion (Delete)',
                    handler : function()
                    {
                    	
                    	var sel=this.getSelectionModel().getSelection();
                    	if(!sel.length)return;
						  var user_role=sel[0];
                    	 
                         var name = user_role.get('person_name');
                         
                         Ext.MessageBox.prompt('Reason for Motion',"You are about to remove the authority from user '"
                         	+name
                         	+"'.  Please enter a reason or description to continue, or press cancel.",function(btn, text)
                         {
						    if (btn != 'ok' || !text||text.split(' ').join('')==''    ){return;}//reject empty text
						    
						    //escape unsafe characters with stringify
	                         var post='description='+escape(text);
	                         post  +='&assignment_id=' + user_role.get('assignment_id');
                         	 var url='index.php/finance/json_new_motion_assignment_del/'+App.TOKEN;
                         	 
							console.log(url);
                         	 console.log(post);
                         	 YAHOO.util.Connect.asyncRequest('POST',url,
                         	 {
							 	scope   : this,
	                            failure: function(o){App.error.xhr(o);},
	                            success : function(o)
	                            {
	                                 console.log(o);
	                                var res=YAHOO.lang.JSON.parse(o.responseText);
	                                if(res.result=="1")
	                                {
	 
	                                    Ext.MessageBox.alert(
	                                    {
	                                    	title:"Status",
		                                    msg:"Motion Sent Successfully", 
		                                    icon: Ext.Msg.INFO,
		                                    buttons: Ext.MessageBox.OK
	                                    });
	                                    this.getStore().load();
	                                }           
	                                if(res.result=="2")                                                                                                                                     
	                                    Ext.MessageBox.alert(
	                                    {
	                                    	title:"Status",
	                                    	msg:"Selected User already has a motion or an assignment to be removed as a Signing Authority",
	                                    	icon: Ext.Msg.INFO,
	                                    	buttons: Ext.MessageBox.OK
	                                    });     
	                                if(res.result=="-1")
	                                    Ext.MessageBox.alert(
	                                    {
	                                    	title:"Motion cannot be created",
	                                    	msg:"Still one pending motion related to signing authority assignments, this must be resolved before new motions for signing authorities are made.",
	                                    	 icon: Ext.Msg.ERROR,
	                                    	 buttons: Ext.MessageBox.OK
	                                    });
	                            }
                         	 },post);
                         	 /*
	                        Ext.Ajax.request
	                        ({
	                            //url     : 'index.php/finance/json_new_motion_assignment_del/TOKEN:'+App.TOKEN+'/',
	                            url     : url,
	                            params  : post,
	                            method : "POST",
	                            
	                        });     
						    */
						},this);
                       
                        
                        
                    }
                }   
        );
        config.bottomLItems.push
        (
             //make motion Add Motion request
             {
                    iconCls : 'application_form_add',
                    xtype   : 'button',
                    text    : '',
                    scope: this,
                    tooltip : 'Apply for motion (Add)',
                    handler : function()
                    {                  
    
                        var window_id='sa_create_window';
                        
                        var final_form=Ext.create('Spectrum.forms.signing_authority',{window_id:window_id});
                        
                        var win=new Ext.create('Spectrum.windows.signing_authority',
                        {
                        	items:final_form,
                        	title       : 'Assign User in Current Organization',
                        	id:window_id
                        });
                        
                        win.on('hide',function(){this.getStore().load();},this);//refresh when its done
                        win.show();
                    }
             }
        );
        if(!config.disable_motions){//important for get started screen
        config.bottomRItems.push
        (
             //Cancel Request
             {
                    iconCls :'delete',
                    xtype   : 'button',
                    text    : '',
                    tooltip : 'Cancel Motion Request',
                    hidden  : true,
                    handler : function()
                    {
                        if(me.getSelectionModel().getSelection().length==0)
                        {
                          // Ext.MessageBox.alert({title:"Error",msg:"No SA-Assignment selected", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        var record=me.getSelectionModel().getSelection()[0].data;
                        var _config=
                        {
                            width   : 400,
                            height  : 175,
                            bottomItems:   
                            [
                                '->'
                                ,{   
                                     xtype   :"button",
                                     text    :"Apply",
                                     iconCls :'table_save',
                                     pressed :true,
                                     width   :70,
                                     tooltip :'Apply',
                                     handler :function()
                                     {         
                                         var post={}
                                         post["assignment_id"]=record.assignment_id;
                                         post["description"]=form.getForm().getValues()["description"];            
                                         if (form.getForm().isValid()) 
                                         {   
                                             form.getForm().submit({
                                                 url     : 'index.php/finance/json_new_motion_assignment_cancel/TOKEN:'+App.TOKEN,
                                                 waitMsg : 'Processing ...',
                                                 params  : post,
                                                 success : function(form, action)
                                                 {
                                                     var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                     if(res.result=="1")
                                                     {
                                                         win.Hide();
                                                         Ext.MessageBox.alert({title:"Status",msg:"Successfully Done", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                         me.getStore().load();
                                                     }
                                                     if(res.result=="-1")
                                                     {
                                                         win.Hide();
                                                         Ext.MessageBox.alert({title:"Error",msg:"Still one pending SA-Assignment Motion exists in system", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                     }
                                                     if(res.result=="-3")
                                                         Ext.MessageBox.alert({title:"Error",msg:"This Motion Cannot be deleted because others voted on this Motion", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                 },
                                                 failure: function(form, action){alert(action.response.responseText);}
                                             }); 
                                         
                                         }   
                                     }                          
                                }
                            ]
                        }          
                        var form=financeForms.form_assignment_del_motion();
                        var final_form=new Ext.spectrumforms.mixer(_config,[form],['form']);
                        var win_cnf=
                        {
                                title       : '',
                                final_form  : final_form
                        }
                        var win=new Ext.spectrumwindow.authority(win_cnf);
                        win.show();
                    }
                }   
        );   }
        
        
        if(config.fields==null)     config.fields       = ['assignment_id','role_id','role_name','user_id','person_name','org_id','org_name','created_by','created_by_name','created_on_display','motion_status_id','motion_status_name','action'];
        if(config.sorters==null)    config.sorters      = null;
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = "/index.php/finance/json_get_sa_assignments/TOKEN:"+App.TOKEN+"/";
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
  
        
        //Get Org_Type
       // var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
        Ext.Ajax.request
        (
        {
                url: 'index.php/permissions/json_get_active_org_and_type/TOKEN:'+App.TOKEN,
                params: {test:'test'},
                success: function(response)
                {
               //     box.hide();
                    var res=YAHOO.lang.JSON.parse(response.responseText);
                    me.org_type_id=parseInt(res.result.org_type_id);
                }
        });
        this.config=config;
        this.callParent(arguments); 
    }
});
}
