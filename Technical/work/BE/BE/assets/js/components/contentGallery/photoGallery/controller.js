
var photoGalleryManageClass= function(){this.construct();};
photoGalleryManageClass.prototype=
{
     dataViewSlider :null,
     
     construct:function()
     {
         Ext.tip.QuickTipManager.init(); 
         Ext.QuickTips.init();
         Ext.ToolTip();
         
         var me=this;
         me.init(); 
     }, 
     init:function()
     {
        var me=this;
        var statusStore=new simpleStoreClass().make(['type_id','type_name'],"index.php/contentgallery/json_get_action_status/TOKEN:"+App.TOKEN,{test:true});        
        statusStore.on("load",function()
        {
            statusStore.add({type_id:-1,type_name:"All"});                
        });
        
        var baseViewPortConfig=
        {
            width       :"100%",
            height      :400,
            generator   :'M',
            renderTo    :"container-photoGallery"
        }
        var baseViewPort            =new Ext.spectrumviewport.viewBase3(baseViewPortConfig);
        
        var parentLeftId    =baseViewPort.getSectionsId("west");
        var parentRightId   =baseViewPort.getSectionsId("east");
                         
        YAHOO.util.Event.onAvailable(parentLeftId,function()
        { 
            var uniqueNum=(Number(Math.random()*10000)).toFixed(0);
                           
            var subPanelId=parentLeftId+'-1';
            var componentPanel=
            {
                
                        html    : '<div style="test-align:center" id='+subPanelId+'></div>',
                        height  : "100%",
                        dockedItems :
                        [   
                            {
                                dock    : 'top',
                                xtype   : 'toolbar',
                                items   :
                                [
                                    {
                                         id          : 'pholtogallery_filter',
                                         name        : "pholtogallery_filter",   
                                         xtype       : 'combo',
                                         fieldLabel  : '',
                                         labelWidth  : 50,
                                         width       : 150,
                                         allowBlank  : true,
                                         mode        : 'local',
                                         forceSelection: true,
                                         editable    : false,
                                         displayField: 'type_name',
                                         valueField  : 'type_id',
                                         queryMode   : 'local',
                                         labelStyle  : 'font-weight:bold',
                                         store       : statusStore,
                                         listeners   :
                                         {
                                            buffer:100,
                                            change:function(obj,selected_id)
                                            {   
                                                me.dataViewSlider.load(
                                                {
                                                    "target_status_id"  :selected_id   
                                                });
                                            }
                                         }                                          
                                    }       
                                ]
                            },
                            {
                                dock    : 'bottom',
                                xtype   : 'toolbar',
                                items   :
                                [
                                    //upload
                                    {        
                                            icon        : "http://endeavor.servilliansolutionsinc.com/global_assets/fugue/fill-090.png",
                                            text        : "",
                                            tooltip     : "Upload Photo",
                                            handler     : function()
                                            {
                                                var uploadphotoFormConfig=
                                                {
                                                    width   : 800,
                                                    height  : 350,
                                                    bottomItems:   
                                                    [
                                                        '->'
                                                        ,{   
                                                             xtype   :"button",
                                                             text    :"Upload",
                                                             iconCls :'table_save',
                                                             pressed :true,
                                                             tooltip :'Upload Photo',
                                                             handler :function()
                                                             {   
                                                                 if (uploadphotoFormFinal.getForm().isValid()) 
                                                                 {   
                                                                    if(Ext.getCmp("photogallery_acceptterms").checked==false)
                                                                    {
                                                                        Ext.MessageBox.alert({title:"Status",msg:"Please Read Terms and Conditions and Accept", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                        return;
                                                                    } 
                                                                    uploadphotoFormFinal.getForm().submit
                                                                    ({          
                                                                           url      : '/index.php/contentgallery/json_upload_photo/'+App.TOKEN,
                                                                           success  : function(form, action)
                                                                           {
                                                                                Ext.getCmp("photogallery_upload").setRawValue('');
                                                                                 
                                                                                 var res    =YAHOO.lang.JSON.parse(action.response.responseText);
                                                                                 if(res.result=="1")
                                                                                 {    
                                                                                     Ext.MessageBox.alert({title:"Status",msg:"Uploaded Successfully.", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                                     uploadphotoFormFinal.getForm().reset();
                                                                                     me.dataViewSlider.load();
                                                                                     
                                                                                     uploadphotoWin.hide();
                                                                                     Ext.MessageBox.alert({title:"Status",msg:"Your Request Bas Been Placed", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                                     me.hideBtns();
                                                                                     me.reLoad();
                                                                                 }                             
                                                                           },
                                                                           failure: function(form, action){}
                                                                     });     
                                                                 }   
                                                             }                          
                                                        }
                                                    ]
                                                }   
                                                 
                                                var uploadphotoFormFinal    =Ext.createWidget("clsForms",uploadphotoFormConfig); 
                                                
                                                
                                                var baseViewPortConfig=
                                                {
                                                    width       :800,
                                                    height      :320,
                                                    generator   :'M'+Math.random()
                                                }
                                                var baseViewPort            =new Ext.spectrumviewport.viewBase2(baseViewPortConfig);
                                                uploadphotoFormFinal.add(baseViewPort).show();
                                                var uploadphotoWinConfig=
                                                {
                                                        title       : "Upload Content",
                                                        final_form  : uploadphotoFormFinal
                                                }
                                                
                                               
                                                var uploadphotoWin=new Ext.spectrumwindow.photoGallery(uploadphotoWinConfig);
                                                uploadphotoWin.show();    
                                                
                                                YAHOO.util.Event.onAvailable(baseViewPort.getSectionsId("west"),function()
                                                {
                                                    var uploadphotoForm_north   =photoGalleryForms.uploadphotoDetails_north();
                                                    var uploadphotoForm_west    =photoGalleryForms.uploadphotoDetails_west();
                                                    var uploadphotoForm_east    =photoGalleryForms.uploadphotoDetails_east();
                                                    
                                                    Ext.getCmp(baseViewPort.getSectionsId("north")).add(uploadphotoForm_north).show();
                                                    Ext.getCmp(baseViewPort.getSectionsId("west")).add(uploadphotoForm_west).show();
                                                    Ext.getCmp(baseViewPort.getSectionsId("east")).add(uploadphotoForm_east).show();
                                                });                                                                                 
                                            }
                                        },
                                    '->',
                                    
                                    //Play/Pause
                                    {
                                            id          : "contentgallery_playorpause",
                                            iconCls     : "control_play",
                                            text        : "",
                                            tooltip     : "Play/Pause",
                                            handler     : function()
                                            {
                                                me.dataViewSlider.getSelection();
                                                var selectedIds=me.dataViewSlider.selectedIds;
                                                if(selectedIds.length==0)
                                                {
                                                    Ext.MessageBox.alert({title:"Error",msg:"Please Select a Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    return;
                                                }
                                                var record=selectedIds[0].data;
                                                                                
                                                
                                                var post={}
                                                post["content_id"]      =record.id;
                                                post["playorpause"]     =((record.content_status_id==2)?1:0);
                                                
                                                if(record.content_status_id==2)
                                                    Ext.getCmp("contentgallery_playorpause").setIconCls('control_pause');
                                                Ext.getCmp("contentgallery_playorpause").setIconCls('control_play');
                                                
                                                Ext.Ajax.request(
                                                {
                                                     url     : 'index.php/contentgallery/json_playorpause_content/TOKEN:'+App.TOKEN,
                                                     params  : post,
                                                     success : function(response)
                                                     {
                                                          var result=YAHOO.lang.JSON.parse(response.responseText).result;
                                                          
                                                          Ext.MessageBox.alert({title:"Status",msg:"Your Request Bas Been Placed", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                          me.hideBtns();
                                                          me.reLoad();
                                                     }
                                                });    
                                            }
                                    },
                                    //Delete
                                    {
                                            id          : "contentgallery_delete",
                                            iconCls     : "delete",
                                            text        : "",
                                            tooltip     : "Delete",
                                            handler     : function()
                                            {
                                                me.dataViewSlider.getSelection();
                                                var selectedIds=me.dataViewSlider.selectedIds;
                                                if(selectedIds.length==0)
                                                {
                                                    Ext.MessageBox.alert({title:"Error",msg:"Please Select a Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    return;
                                                }
                                                var record=selectedIds[0].data;
                                                
                                                var post={}
                                                post["content_id"]  =record.id;
                                                post["file_name"]   =record.file_name;
                                                
                                                Ext.Ajax.request(
                                                {
                                                     url     : 'index.php/contentgallery/json_delete_content/TOKEN:'+App.TOKEN,
                                                     params  : post,
                                                     success : function(response)
                                                     {
                                                          var result=YAHOO.lang.JSON.parse(response.responseText).result;
                                                          Ext.MessageBox.alert({title:"Status",msg:"Your Request Bas Been Placed", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                          me.hideBtns();
                                                          me.reLoad();
                                                     }
                                                });    
                                            }
                                    },
                                    //Approve
                                    {
                                            id          : "contentgallery_approve",
                                            iconCls     : "tick",
                                            text        : "",
                                            tooltip     : "Approve",
                                            handler     : function()
                                            {
                                                me.dataViewSlider.getSelection();
                                                var selectedIds=me.dataViewSlider.selectedIds;
                                                if(selectedIds.length==0)
                                                {
                                                    Ext.MessageBox.alert({title:"Error",msg:"Please Select a Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    return;
                                                }
                                                var record=selectedIds[0].data;
                                                                 
                                                if(me.a_o_t_id==1)//SSI Level
                                                {
                                                    var post={}
                                                    post["content_id"] =record.id;
                                                    
                                                    Ext.Ajax.request(
                                                    {
                                                        url     : 'index.php/contentgallery/json_approve_content/TOKEN:'+App.TOKEN,
                                                        params  : post,
                                                        success : function(response)
                                                        {
                                                             var result =YAHOO.lang.JSON.parse(response.responseText).result;
                                                             Ext.MessageBox.alert({title:"Status",msg:"SSI Approved Selectd Content. Pleae Check Your Email", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                             me.hideBtns();
                                                             me.reLoad();
                                                        }
                                                    });                               
                                                    return;  
                                                }
                                                var approveFormConfig=
                                                {
                                                    width   : 600,
                                                    height  : 250,
                                                    bottomItems:   
                                                    [
                                                        '->'
                                                        ,{   
                                                             xtype   :"button",
                                                             text    :"Approve",
                                                             iconCls :'table_save',
                                                             pressed :true,
                                                             tooltip :'',
                                                             handler :function()
                                                             {   
                                                                 var pressedClass    ="x-btn-default-toolbar-small-pressed";
                                                                 var playBtnDom         =Ext.get("btn_play");
                                                                 var pauseBtnDom        =Ext.get("btn_pause");
                                                                 
                                                                 
                                                                 if(Ext.getCmp("photogallery_acceptterms").checked==false)
                                                                 {
                                                                        Ext.MessageBox.alert({title:"Status",msg:"Please Read Terms and Conditions and Accept", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                        return;
                                                                 } 
                                                                 
                                                                 if(!playBtnDom.hasCls(pressedClass) && !pauseBtnDom.hasCls(pressedClass))
                                                                 {
                                                                     Ext.MessageBox.alert({title:"Status",msg:"Please Select Play or Pause State", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                     return;
                                                                 }                 
                                                                 
                                                                 var playOrpause=((pauseBtnDom.hasCls(pressedClass))?0:1);
                                                                 
                                                                 
                                                                 var post={}
                                                                 post["playorpause"]=playOrpause;
                                                                 post["content_id"] =record.id;
                                                                 
                                                                 Ext.Ajax.request(
                                                                 {
                                                                     url     : 'index.php/contentgallery/json_approve_content/TOKEN:'+App.TOKEN,
                                                                     params  : post,
                                                                     success : function(response)
                                                                     {
                                                                          var result=YAHOO.lang.JSON.parse(response.responseText).result;
                                                                          
                                                                          approveWin.hide();
                                                                          approveWin.destroy();
                                                                          Ext.MessageBox.alert({title:"Status",msg:"Your Request Bas Been Placed", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                          me.hideBtns();
                                                                          me.reLoad();
                                                                     }
                                                                 });    
                                                             }                          
                                                        }
                                                    ]
                                                }   
                                                                
                                                var approveFormFinal    =Ext.createWidget("clsForms",approveFormConfig); 
                                                
                                                
                                                                 
                                                var baseViewPortConfig=
                                                {
                                                    width       :600,
                                                    height      :220,
                                                    generator   :'M'+Math.random()
                                                }
                                                var baseViewPort            =new Ext.spectrumviewport.viewBase3(baseViewPortConfig);
                                                approveFormFinal.add(baseViewPort).show();
                                                var approveWinConfig=
                                                {
                                                        title       : "Approve",
                                                        final_form  : approveFormFinal
                                                }
                                                
                                               
                                                approveWin=new Ext.spectrumwindow.photoGallery(approveWinConfig);
                                                approveWin.show();    
                                                
                                                YAHOO.util.Event.onAvailable(baseViewPort.getSectionsId("west"),function()
                                                {                                                                            
                                                    var uploadphotoForm_west        =photoGalleryForms.uploadphotoDetails_west();
                                                    var uploadphotoForm_playPause   =photoGalleryForms.playPauseChoiceForm();
                                                    
                                                    Ext.getCmp(baseViewPort.getSectionsId("west")).add(uploadphotoForm_west).show();
                                                    Ext.getCmp(baseViewPort.getSectionsId("east")).add(uploadphotoForm_playPause).show();
                                                });                                          
                                            }
                                        },
                                    //Reject
                                    {
                                            id          : "contentgallery_reject",
                                            icon        : "http://endeavor.servilliansolutionsinc.com/global_assets/fugue/cross-button.png",
                                            text        : "",
                                            tooltip     : "Reject",
                                            handler     : function()
                                            {
                                                me.dataViewSlider.getSelection();
                                                var selectedIds=me.dataViewSlider.selectedIds;
                                                if(selectedIds.length==0)
                                                {
                                                    Ext.MessageBox.alert({title:"Error",msg:"Please Select a Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    return;
                                                }
                                                var record=selectedIds[0].data;
                                                
                                                var post={}
                                                post["content_id"]=record.id;
                                                
                                                Ext.Ajax.request(
                                                {
                                                     url     : 'index.php/contentgallery/json_reject_content/TOKEN:'+App.TOKEN,
                                                     params  : post,
                                                     success : function(response)
                                                     {
                                                          var result=YAHOO.lang.JSON.parse(response.responseText).result;
                                                          Ext.MessageBox.alert({title:"Status",msg:"Your Request Bas Been Placed", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                          me.hideBtns();
                                                          me.reLoad();
                                                     }
                                                });    
                                            }
                                        },
                                        
                                    //Report
                                    {                                       
                                            id          : "contentgallery_report",
                                            iconCls     : "exclamation",
                                            text        : "",
                                            tooltip     : "Report Acc",
                                            handler     : function()
                                            {
                                                me.dataViewSlider.getSelection();
                                                var selectedIds=me.dataViewSlider.selectedIds;
                                                if(selectedIds.length==0)
                                                {
                                                    Ext.MessageBox.alert({title:"Error",msg:"Please Select a Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                    return;
                                                }
                                                var record=selectedIds[0].data;
                                                
                                                var accFormConfig=
                                                {
                                                     width   : 200,
                                                     height  : 100,
                                                     bottomItems:   
                                                     [
                                                         '->'
                                                         ,{   
                                                              xtype   :"button",
                                                              text    :"Flag the Content",
                                                              iconCls :'table_save',
                                                              pressed :true,
                                                              tooltip :'Flag the Content',
                                                              handler :function()
                                                              {      
                                                                    me.dataViewSlider.getSelection();
                                                                    var selectedIds=me.dataViewSlider.selectedIds;
                                                                    if(selectedIds.length==0)
                                                                    {
                                                                        Ext.MessageBox.alert({title:"Error",msg:"Please Select a Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                                        return;
                                                                    }
                                                                    var record=selectedIds[0].data;
                                                                    
                                                                    var formValues      =accFormFinal.getForm().getValues();
                                                                    var post            ={}
                                                                    post["content_id"]  =record.id;
                                                                    post["acc_type_id"] =formValues.acc_types;
                                                                    
                                                                    Ext.Ajax.request(
                                                                    {
                                                                         url     : 'index.php/contentgallery/json_report_content/TOKEN:'+App.TOKEN,
                                                                         params  : post,
                                                                         success : function(response)
                                                                         {
                                                                              var result    =YAHOO.lang.JSON.parse(response.responseText).result;
                                                                              
                                                                              if(result==-1)
                                                                              {
                                                                                    Ext.MessageBox.alert({title:"Error",msg:"Not Able to Use this Option", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});      
                                                                                    return;
                                                                              }
                                                                              
                                                                              accWin.hide();
                                                                              Ext.MessageBox.alert({title:"Status",msg:"Your Request Bas Been Placed", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                              me.hideBtns();
                                                                              me.reLoad();
                                                                         }
                                                                    });        
                                                              }                          
                                                         }
                                                     ]
                                                 };   
                                                  
                                                 var accTypeStore   =new simpleStoreClass().make(['type_id','type_name'],"index.php/contentgallery/json_get_acc_types/"+App.TOKEN+'/',{content_id:record.id});
                                                 var accFormFinal   =Ext.createWidget("clsForms",accFormConfig); 
                                                 var accForm        =photoGalleryForms.accPanelForm(accTypeStore); 
                                                                                          
                                                 accFormFinal.add(accForm);
                                                 var accWinConfig=
                                                 {
                                                         title       : "Choose Adult Content Classification",
                                                         final_form  : accFormFinal
                                                 }
                                                 
                                                 accWin=new Ext.spectrumwindow.photoGallery(accWinConfig);
                                                 accWin.show();
                                            }
                                        }
                                
                                ]
                            }
                        ]
            }
            Ext.getCmp(parentLeftId).add(componentPanel);
            
            YAHOO.util.Event.onAvailable(subPanelId,function()
            {
                var config=
                {
                    moduleId    :subPanelId,
                    baseViewPort:baseViewPort,
                    owner       :me
                }
                me.dataViewSlider  =new dataViewSliderPhotoGalleryClass(config);
                me.dataViewSlider.init('/index.php/contentgallery/json_get_photoGalleries/TOKEN:'+App.TOKEN);    
            });
                                                       
            var detailsForm     =photoGalleryForms.rightPanelForm();
            Ext.getCmp(parentRightId).add(detailsForm);
        });
        
        
        //Current User/Org 
        Ext.Ajax.request(
        {
            url     : 'index.php/permissions/json_get_active_org_and_type/'+App.TOKEN,
            params  : {test:'test'},
            success : function(response)
            {
                var res        =YAHOO.lang.JSON.parse(response.responseText);
                me.a_o_t_id    =res.result.org_type_id;
                me.a_o_id      =res.result.org_id;
                me.a_o_name    =res.result.org_name;
                
                me.hideBtns();
            },
            failure: function( action){App.error.xhr(action);}
        }); 
     },
     reLoad:function()
     {
        var me=this;
        me.dataViewSlider.load(
        {
            "target_status_id"  :Ext.getCmp("pholtogallery_filter").getValue()
        });  
     },
     hideBtns:function()
     {
        var playorpauseBtn  =Ext.getCmp("contentgallery_playorpause");
        var deleteBtn       =Ext.getCmp("contentgallery_delete");
        var approveBtn      =Ext.getCmp("contentgallery_approve");
        var rejectBtn       =Ext.getCmp("contentgallery_reject");
        var reportBtn       =Ext.getCmp("contentgallery_report");
        
        
        playorpauseBtn.hide();
        deleteBtn.hide();
        approveBtn.hide();
        rejectBtn.hide();
        reportBtn.hide();
     },
     showBtn:function(btnName)
     {
        var playorpauseBtn  =Ext.getCmp("contentgallery_playorpause");
        var deleteBtn       =Ext.getCmp("contentgallery_delete");
        var approveBtn      =Ext.getCmp("contentgallery_approve");
        var rejectBtn       =Ext.getCmp("contentgallery_reject");
        var reportBtn       =Ext.getCmp("contentgallery_report");
        
        switch(btnName)
        {
            case 'playorpause':
                playorpauseBtn.show(); 
            break;
            case 'delete':
                deleteBtn.show();
            break;
            case 'approve':
                approveBtn.show();
            break;
            case 'reject':
                rejectBtn.show();   
            break;
            case 'report':
                reportBtn.show();   
            break;
        }
     },
     handleButtons:function(selectedItemData)
     {
         var me=this;
         me.hideBtns();
         
         /*
         
         Actions
         1    Upload
         2    Approve
         3    Pause
         4    Play
         5    Reject
         6    Delete
         7    SendToOrg
         8    SendToSSI
         
         Status
         1    Pending
         2    Paused
         3    Played
         5    Rejected
         4    Deleted
          
         */
         
         //LOGIC
         var a  =selectedItemData.content_action_id;
         var s  =selectedItemData.content_status_id;
         
         if( a == 1 && s == 1)
         {
            me.showBtn('approve');
            me.showBtn('delete');
            me.showBtn('reject');
         }
         if( a == 2 && s == 1)
            me.showBtn('reject');
         if( a == 5 && s == 5)
         {
            me.showBtn('approve');
            me.showBtn('delete');
         }
         if( a == 4 && s == 3)    
         {
             me.showBtn('report');
             me.showBtn('delete');
             me.showBtn('reject');
             me.showBtn('playorpause');
             
             if(s==2)
                Ext.getCmp("contentgallery_playorpause").setIconCls('control_play');
             else
                Ext.getCmp("contentgallery_playorpause").setIconCls('control_pause');
         }    
            
         if( a == 3 && s == 2)
         {
             me.showBtn('report');
             me.showBtn('delete');
             me.showBtn('reject');
             me.showBtn('playorpause');
             
             if(s==2)
                Ext.getCmp("contentgallery_playorpause").setIconCls('control_play');
             else
                Ext.getCmp("contentgallery_playorpause").setIconCls('control_pause');
         }
            
         if( a == 7 && s == 1)
         {
            me.showBtn('approve');
            me.showBtn('delete');
            me.showBtn('reject');
         }
         if( a == 8 && s == 1)
         {
            me.showBtn('reject'); 
            me.showBtn('approve'); 
         }
            
     },
     showPreview:function(title,content)
     {    
        Shadowbox.open
        ({
            content :   '<img src='+content+' />',
            player  :   "html",
            title   :   title,
            width   :   400,
            height  :   400
        });           
     }                      
} 

var photoGalleryManage=new photoGalleryManageClass();