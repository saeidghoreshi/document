Ext.define('Ext.spectrumgrids.client',    
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
            config.title='Manage Advertising Clients';
            var me=this;
            config.columns  =
            [    
                {
                    text        : "Client"
                    ,dataIndex  : 'co_org_name'
                    ,width      :200
                }                
                ,{
                    text        : "Contact Person"
                    ,dataIndex  : 'person_name'
                    ,flex       :1
                }                             
                ,{
                    text        : "Contact Phone"
                    ,dataIndex  : 'person_phone'
                    ,flex       :1
                }
                ,{
                    text        : "Contact Email"
                    ,dataIndex  : 'person_email'
                    ,flex       :1
                }
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            //Create new client
            config.bottomLItems.push
            (
                {
                        xtype     : 'button',
                        iconCls   :'add',
                        text      :"",
                        tooltip   :"Create new Client",
                        handler: function()
                        {
                            var formClientDetailsConfig=
                            {
                                width   : 400,
                                height  : 240,
                                bottomItems:   
                                [
                                    '->'
                                    ,{   
                                         xtype   :"button",
                                         text    :"Save",
                                         iconCls :'table_save',
                                         
                                         tooltip :'Save Client',
                                          
                                         handler :function()
                                         {
                                             var form   =formClientDetails.getForm();
                                             if (form.isValid()) 
                                             {   
                                                 form.submit({
                                                     url     : 'index.php/websites/json_new_client/'+App.TOKEN,
                                                      
                                                     params  : form.getValues(),
                                                     success : function(form, action)
                                                     {
                                                     	 var res=YAHOO.lang.JSON.parse(action.response.responseText);
														 if(res.result=="1")
                                                         {
                                                             Ext.MessageBox.alert({title:"Status",msg:"Client Created Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                             me.getStore().load();   
                                                             formClientDetailsWin.Hide();
                                                         }     
                                                         else App.error.xhr(action.response);
                                                     },
                                                     failure: function(form, action){App.error.xhr(action.response);}
                                                 });
                                             }   
                                         }                          
                                    }
                                ]
                            }          
                            var formClientDetails       =websiteForms.formClientDetails();
                            var formClientDetailsFinal  =new Ext.spectrumforms.mixer(formClientDetailsConfig,[formClientDetails],['form']);
                            
                            var formClientDetailsWinConfig=
                            {
                                title       : 'Create New Client',
                                final_form  : formClientDetailsFinal
                            }
                            var formClientDetailsWin=new Ext.spectrumwindow.advertising(formClientDetailsWinConfig);
                            formClientDetailsWin.show();
                        }   
                    }
            );
            //Edit Client
            config.bottomRItems.push
            (
                {
                        xtype   : 'button',
                        iconCls : 'pencil',
                        tooltip : 'Edit Client',
                        handler : function()
                        {
                            if(me.getSelectionModel().getSelection().length==0)
                            { 
                                return;
                            }
                              var formClientDetailsConfig=
                              {
                                  width   : 400,
                                  height  : 240,
                                  bottomItems:   
                                          [
                                              "->"
                                              ,{
                                                      xtype     :"button",
                                                      iconCls   :'disk',
                                                      text      :"Save",
                                                      tooltip   :"Update Client",
                                                      pressed   :true,
                                                      width     :70,
                                                      handler:function()
                                                      {
                                                          var form=formClientDetails.getForm();
                                                          if (form.isValid()) 
                                                          {   
                                                              post  =form.getValues();
                                                              post['person_id']=me.getSelectionModel().getSelection()[0].data.person_id;
                                                              post['co_org_id']=me.getSelectionModel().getSelection()[0].data.co_org_id;
                                                              
                                                              form.submit({
                                                                  url       : 'index.php/websites/json_update_client/TOKEN:'+App.TOKEN,
                                                                  waitMsg   : 'Processing ...',
                                                                  params    : post,
                                                                  success   : function(form, action)
                                                                  {
                                                                      var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                                      if(res.result=="1")
                                                                      {
                                                                          Ext.MessageBox.alert({title:"Status",msg:"Client updated successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                          formClientDetailsWin.Hide();
                                                                          me.getStore().load();   
                                                                      }           
                                                                  },
                                                                  failure: function(form, action){App.error.xhr(action.response);}
                                                              });  
                                                          
                                                          }   
                                                      }                          
                                              }
                                          ]
                              }          
                              var formClientDetails         =websiteForms.formClientDetails();
                              var formClientDetailsFinal    =new Ext.spectrumforms.mixer(formClientDetailsConfig,[formClientDetails],['form']);
                              var formClientDetailsWinConfig=
                              {
                                    title       : 'Edit Client',
                                    final_form  : formClientDetailsFinal
                              }
                              var formClientDetailsWin      =new Ext.spectrumwindow.advertising(formClientDetailsWinConfig);
                              formClientDetailsWin.show();
                              
                              //Load record
                              var client_details    =me.getSelectionModel().getSelection()[0].data;
                              var person_phone=(client_details.person_phone!=null)?client_details.person_phone:'--';
                              
                              var gen=Math.random(); 
                              Ext.define('model_'+gen, {extend: 'Ext.data.Model'});
                              formClientDetails.loadRecord(Ext.ModelManager.create(
                              {
                                   'client_name'        : client_details.co_org_name
                                  ,'first_name'         : client_details.person_fname
                                  ,'last_name'          : client_details.person_lname
                                  ,'client_email'       : client_details.person_email
                                  ,'area_code'          : person_phone.split('-')[0]
                                  ,'phone_first3'       : person_phone.split('-')[1]
                                  ,'phone_last4'        : person_phone.split('-')[2]
                                  
                              }, "model_"+gen));           
                        }   
                }
            );
            //Delete Client
            config.bottomRItems.push
            (
                {
                        xtype   : 'button',
                        iconCls : 'delete',
                        text    : '',
                        tooltip : 'Delete Client',
                        handler: function()
                        {
                            if(me.getSelectionModel().getSelection().length==0)
                            { 
                                return;
                            }
                            var post={}
                            post['co_org_id']=me.getSelectionModel().getSelection()[0].data.co_org_id;
                            
                            Ext.MessageBox.confirm('Delete Action', "Are you sure ?", function(answer)
                            {     
                                if(answer=="yes")
                                { 
                                    Ext.Ajax.request({
                                        url     : 'index.php/websites/json_delete_client/TOKEN:'+App.TOKEN,
                                        params  : post,
                                        success : function(o)
                                        {
                                             var res=YAHOO.lang.JSON.parse(o.responseText);
                                             if(res.result=="1")
                                             { 
                                                 Ext.MessageBox.alert({title:"Status",msg:"Client Deleted successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                 me.getStore().load();   
                                             }
                                        },
                                        failure: App.error.xhr
                                    });    
                                }
                            }); 
                        }   
                    }
            );
                    
                     
            config.collapsible          =false;
            if(config.fields==null)     config.fields       = ['co_org_id','co_org_name','person_fname','person_lname','person_name','person_id','person_email','person_phone'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 10;
            if(config.url==null)        config.url          = "/index.php/websites/json_get_clients/"+App.TOKEN;
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 400;
            if(config.groupField==null) config.groupField   = "";
            this.override_edit          =config.override_edit;
            this.override_selectiochange=config.override_selectionchange;
            this.override_itemdblclick  =config.override_itemdblclick;
            this.override_collapse      =config.override_collapse;
            this.override_expand        =config.override_expand;
            
            this.config=config;
            this.callParent(arguments); 
        }                       
 
});

 
