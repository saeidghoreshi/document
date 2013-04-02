if(!App.dom.definedExt('Ext.spectrumgrids.bankaccount')){//very important workaround for IE and sometimes chrome
Ext.define('Ext.spectrumgrids.bankaccount',    
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
    ,constructor : function(config) 
    {   
        var me=this;
        if(config.columns ==null)
        config.columns  =
        [  
            {
                    text    : 'Bank Account Details',
                    columns :
                    [
                        {
                            text        : "Bank Account Owner"
                            ,dataIndex  : 'bankaccount_name'
                            ,width      :150
                        }
                        ,{
                            text        : "Bank Name"
                            ,dataIndex  : 'bankname'
                            ,width      :100
                        }      
                        ,{
                            text        : "Institution"
                            ,dataIndex  : 'institution'
                            ,width      :100
                        }
                        ,{
                            text        : "Transit Code"
                            ,dataIndex  : 'transit'
                            ,width      :100
                        }
                        ,{
                            text        : "Account Number"
                            ,dataIndex  : 'account'
                            ,width      :100
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
                text        : "action"
                ,dataIndex  : 'action'
                ,width      : 100
            }
            ,{
                text        : "Created By"
                ,dataIndex  : 'created_by_name'
                ,flex       :1
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
             //make motion delete request
             /*{
                    id      : 'bankaccount_del_motion',
                    iconCls : 'fugue_minus-button',
                    xtype   : 'button',
                    text    : '',
                    scope:this,
                    tooltip : 'Motion to Remove selected bank account',
                    handler : function()
                    {
 
 						Ext.MessageBox.confirm('Delete Account', "Delete the selected bank account.  Are you sure?"
                         , function(answer)
                          {     
                              if(answer!="yes"){return;}
 
   							var selected=this.getSelectionModel().getSelection();
	                         if(selected.length==0)
	                         {
	                            Ext.MessageBox.alert({title:"Nothing to Delete",msg:"Please select a bankaccount", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
	                            return;
	                         }
	                         var record=selected[0].data;
	                         var post={}
	                         post["bankaccount_id"] =record.bankaccount_id;
	                         
	                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
	                         Ext.Ajax.request(
	                         {
	                            url     : 'index.php/finance/json_new_motion_bankaccount_del/'+App.TOKEN,
	                            params  : post,
	                            scope:this,
	                            success : function(response)
	                            {
	                                box.hide();
	                                var res=YAHOO.lang.JSON.parse(response.responseText);
	                                if(res.result=="1")
	                                {
	                                     Ext.MessageBox.alert({title:"Status",msg:"Request Sent Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
	                                     this.getStore().load();
	                                     win.Hide();
	                                }
	                                if(res.result=="-1")
	                                {
	                                     Ext.MessageBox.alert({title:"Cannot Delete",msg:"Still one pending bankaccount Motion exists in system", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
	                                
	                                     win.Hide();
	                                }
	                            }
	                            //always put box.hide inside failure, otherwise sysetme will crash
	                            ,failure:function(o){box.hide();App.error.xhr(o);}
	                         });    
                         
						  },this);//end of msgbox.confirm     
 
 
                    }
                }   
             */
        );   
        config.bottomLItems.push
        (
             //make motion Add request
             {
                    id      : 'bankaccount_add_motion',
                    iconCls : 'application_form_add',
                    xtype   : 'button',
                    text    : '',
                    scope:this,
                    tooltip : 'Motion to Add a new Bank Account',
                    handler : function()
                    {
       
                        var form=Ext.create('Spectrum.forms.bankaccount',{});
  
                        var win=new Ext.spectrumwindows(
                        {
                        	width   : 400,
                            height  : 375,
                            title       : 'Apply for New Account',
                            items  : form
                        });
                        win.on('hide',function(){this.getStore().load();},this);
                        win.show();
                    }
             }
        );       
        
        
        if(config.fields==null)     config.fields       = ['bankaccount_id','bankaccount_name','bankname','institution','transit','account','entity_id','created_by','created_by_name','created_on_display','motion_status_name','motion_status_id','action'];
        if(config.sorters==null)    config.sorters      = null;
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = "/index.php/finance/json_get_bankaccounts/TOKEN:"+App.TOKEN;
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
         
        if(config.groupField==null) config.groupField   = "";
        
        this.override_edit          =config.override_edit;
        this.override_selectiochange=config.override_selectionchange;
        this.override_itemdblclick  =config.override_itemdblclick;
        this.override_collapse      =config.override_collapse;
        this.override_expand        =config.override_expand;
        
        this.config=config;
        this.callParent(arguments); 
    }
 
});}
