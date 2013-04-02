if(!App.dom.definedExt('Ext.spectrumtreeviews.link'))
{
	Ext.define('Ext.spectrumtreeviews.link',    
	{
	        extend: 'Ext.spectrumtreeviews', 
	        initComponent: function(config) 
	        {       
	            this.callParent(arguments);
	        }
	        
	        ,config                 :null
	        ,org_type               :null
            ,org_id                 :null
	        ,constructor : function(config) 
	        {                        
	            var me=this;
	            config.title='Website Links & Navigation';
	            config.columns  =
	            [    
	                 {
	                    xtype       : 'treecolumn',
	                    text        : 'Title',
	                    dataIndex   : 'link_name',
	                    sortable    : false,
	                    width       : 500
	                }
	                ,{
	                    text        : "Type",
	                    dataIndex   : 'link_type_name',
	                    sortable    : false,
	                    flex        : 1   
	                }
	            ];
	            if(config.topItems==null)     config.topItems=[];
	            if(config.bottomLItems==null) config.bottomLItems=[];
	            if(config.bottomRItems==null) config.bottomRItems=[];
	            
	            config.linkTypeStore           =new simpleStoreClass().make(['type_id','type_name'],"index.php/websites/json_get_linkTypes/TOKEN:"+App.TOKEN+'/',{test:true});
	            config.linkTypeStoreFormDetails=new simpleStoreClass().make(['type_id','type_name'],"index.php/websites/json_get_linkTypes/TOKEN:"+App.TOKEN+'/',{test:true});
	            config.pagedArticlesStore      =new simpleStoreClass().make(['type_id','type_name'],"index.php/websites/json_get_paged_articles/TOKEN:"+App.TOKEN+'/',{});
	            config.topItems.push
	            (
	               //Link Type
	               {
	                                   id              : 'links_link_types_combo',  
	                                   xtype           : 'combo',
	                                   tooltip         : 'Link Types',
	                                   emptyText       : '',
	                                   mode            : 'local',
	                                   triggerAction   : 'all',
	                                   forceSelection  : false,
	                                   editable        : false,
	                                   name            : 'link_types',
	                                   displayField    : 'type_name',
	                                   valueField      : 'type_id',
	                                   queryMode       : 'local',
	                                   typeAhead       : true,
	                                   store           : config.linkTypeStore,
	                                  
	                                   listeners: {
	                                        change: {
	                                            fn: function(conf,selected_id)
	                                            {
	                                                me.getStore().load({params:{link_type_id:selected_id}});
	                                            },
	                                            scope: this, 
	                                            buffer:200                  
	                                        }
	                                    }                                               
	               } 
	            );
	            config.bottomLItems.push
	            (
	                //create new link
	                {
	                    id          : "spectrumgrids_link_create_btn",
	                    iconCls     : 'fugue_chain--plus',
	                    xtype       : 'button',
	                    text        : '',
	                    scope       : this,
	                    tooltip     : 'Create New Link',
	                    handler     : function()
	                    {   
	                            var formLinkDetailsConfig=
	                            {
	                                width   : 330,
	                                height  : 150,
	                                bottomItems:   
	                                [
	                                    '->'
	                                    ,{   
	                                         xtype   :"button",
	                                         text    :"Save",
	                                         iconCls :'table_save',
	                                         tooltip :'Save',
	                                         handler :function()
	                                         {                     
	                                             if (!formLinkDetails.getForm().isValid())return;
	                                             
	                                                var form    =formLinkDetails.getForm();
	                                                var values  =form.getValues();
	                                                
	                                                //only do this if paged is selected!!!
	                                                if(!values["article_id"] && values['type_id']=='2')
	                                                {
	                                                    Ext.MessageBox.alert({title:"Error",msg:"Please Select a <b>Paged</b> Article", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
	                                                    return;
	                                                }

	                                                var post    =values;
	                                                
	                                                var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
	                                                Ext.Ajax.request(
	                                                {
	                                                    url     : 'index.php/websites/json_new_link/'+App.TOKEN,
	                                                    params  : post,
	                                                    success : function(o)
	                                                    {
	                                                        box.hide();
	                                                        var res=YAHOO.lang.JSON.parse(o.responseText);
	                                                        if(res.result=="1")
	                                                        {
	                                                            formLinkDetailsWin.Hide();
	                                                            linkManage.init();
	                                                        }
	                                                            
	                                                    },
	                                                    failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
	                                                }); 
	                                         }                          
	                                    }
	                                ]
	                            }          

	                            var formLinkDetails         =websiteForms.formLinkDetails(config.pagedArticlesStore,config.linkTypeStoreFormDetails);
	                            var formLinkDetailsFinal    =new Ext.spectrumforms.mixer(formLinkDetailsConfig,[formLinkDetails],['form']);
	                            var formLinkDetailsWinConfig=
	                            {
	                                title       : 'Create New Link',
	                                final_form  : formLinkDetailsFinal
	                            }
	                            var formLinkDetailsWin      =new Ext.spectrumwindow.publishing(formLinkDetailsWinConfig);
	                            formLinkDetailsWin.show();    
	                              
	                            //Set Default
	                            var linkTypeCombo   =Ext.getCmp("form_link_details_type_id");
	                            var recIndex        =linkTypeCombo.store.find("type_name","Url", 0, true, false, false);    
	                            linkTypeCombo.select(linkTypeCombo.store.getAt(recIndex)); 
	                            
	                    }   
	                }
	                ,	                 //Apply Ordering to DB
	                 {
	                        id      : "spectrumgrids_link_apply_btn",
	                        iconCls : 'table_link',
	                        xtype   : 'button',
	                        tooltip : 'Save Link Order',
	                        handler : function()
	                        {                     
	                            var post={};
	                            post["link_parent_combo"]   ='';
                                
	                                                     
                                for(var i=0;i<config.Plot.length;i++)
	                                post["link_parent_combo"]+=config.Plot[i][1]+','+((config.Plot[i][0]==null)?-1:config.Plot[i][0])+'==';
                                    
	                            post["link_parent_combo"]        =post["link_parent_combo"].substring(0,post["link_parent_combo"].length-2);
	                                                        
	                            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
	                            Ext.Ajax.request(
	                            {
	                                url     : 'index.php/websites/json_update_links_ordering/TOKEN:'+App.TOKEN,
	                                params  : post,
	                                success : function(o)
	                                {
	                                    box.hide();
	                                    var res=YAHOO.lang.JSON.parse(o.responseText);
	                                    if(res.result=="1")
	                                         me.getStore().load();   
	                                },
	                                failure: App.error.xhr
	                            });
	                        }                        
	                 }
	            );              
	            config.bottomRItems.push
	            (

	                 //un/Hide
	                 {
	                        id      : "spectrumgrids_link_hide_btn",
	                        iconCls : 'control_play_blue', //// if already active:  'control_pause'
	                        xtype   : 'button',
	                        tooltip : 'Activate Selected Link',//if already deactivated  'Deactivate Selected Link'
	                        handler : function()
	                        {
	                            if(me.getSelectionModel().getSelection().length==0)
                                { 
                                    return ;
                                }
                                var record  =me.getSelectionModel().getSelection()[0].data;
                                if(record.owned_by  !=  me.org_id )
                                {
                                    Ext.MessageBox.alert({title:"You do not own this link.",msg:"Unable to process action for this link: <b>["+record.link_name+"]</b>", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    return ;
                                }   
                                                                                                             
                                var post={}
                                post["link_id"]     =record.link_id;
	                            
                         		var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
	                            Ext.Ajax.request({
	                                url     : 'index.php/websites/json_hide_or_unhide_link/'+App.TOKEN+'/',
	                                params  : record,
	                                success : function(o)
	                                {
	                                    box.hide();
	                                    var res=YAHOO.lang.JSON.parse(o.responseText);
	                                    if(res.result=="1"/*deactivated*/)Ext.MessageBox.alert({title:"Status",msg:"Link Hid Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
	                                    if(res.result=="2")Ext.MessageBox.alert({title:"Status",msg:"Link Unhid Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
	                                    me.getStore().load();
	                            },
	                            failure: function(o){box.hide();App.error.xhr(o);}
	                            });    
	                        }
	                 }
	                 ,'-'
	                 //Edit
	                 ,{
	                        id      : 'spectrumgrids_link_edit_btn',
	                        iconCls : 'fugue_blog--pencil',
	                        xtype   : 'button',
	                        scope   : this,
	                        tooltip : 'Modify Selected Link',
	                        handler : function()
	                        {
                        		if(me.getSelectionModel().getSelection().length==0)
                                { 
                                    return ;
                                }
                                var record  =me.getSelectionModel().getSelection()[0].data;
                                if(record.owned_by  !=  me.org_id )
                                {
                                    Ext.MessageBox.alert({title:"You do not own this link.",msg:"Unable to Edit Link <b>["+record.link_name+"]</b>", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    return ;
                                }   
	                            
	                            
	                            var formLinkDetailsConfig=
	                            {
	                                width   : 330,
	                                height  : 150,
	                                bottomItems:   
	                                [
	                                    '->'
	                                    ,{   
	                                         xtype   :"button",
	                                         text    :"Save",
	                                         iconCls :'table_save',
	                                         tooltip :'Save',
	                                         handler :function()
	                                         {                     
	                                             if (!formLinkDetails.getForm().isValid())return;
	                                             
	                                                var form    =formLinkDetails.getForm();
	                                                var values  =form.getValues();
	                                                
	                                                //only do this if paged is selected!!!
	                                                if(!values["article_id"] && values['type_id']=='2')
	                                                {
	                                                    Ext.MessageBox.alert({title:"Error",msg:"Please Select a <b>Paged</b> Article", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
	                                                    return;
	                                                }
	                                                var post            ={}
	                                                post                =values;   
	                                                post["link_id"]     =record.link_id;
	                                                
	                                                var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
	                                                Ext.Ajax.request(
	                                                {
	                                                    url: 'index.php/websites/json_update_link/'+App.TOKEN,
	                                                    params  : post,
	                                                    success : function(o)
	                                                    {
	                                                        box.hide();
	                                                        var res=YAHOO.lang.JSON.parse(o.responseText);
	                                                        if(res.result=="1")
	                                                        {
	                                                            formLinkDetailsWin.Hide();
	                                                            linkManage.init();
	                                                        }   
	                                                    },
	                                                    failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
	                                                }); 
	                                                
	                                         }                          
	                                    }
	                                ]
	                            }          
	                            var formLinkDetails         =websiteForms.formLinkDetails(config.pagedArticlesStore,config.linkTypeStoreFormDetails);
	                            var formLinkDetailsFinal    =new Ext.spectrumforms.mixer(formLinkDetailsConfig,[formLinkDetails],['form']);
	                            var formLinkDetailsWinConfig=
	                            {
	                                title       : 'Edit Link ('+record.label_name+")",
	                                final_form  : formLinkDetailsFinal
	                            }
	                            var formLinkDetailsWin      =new Ext.spectrumwindow.publishing(formLinkDetailsWinConfig);
	                            formLinkDetailsWin.show();    
	                            
	                            //Load record
                                var gen =Math.random();                            
	                            Ext.define('model_'+gen, {extend: 'Ext.data.Model'});
	                            formLinkDetails.loadRecord(Ext.ModelManager.create(
	                            {
	                                       'title'          : record.link_name
	                                      ,'type_id'        : record.link_type_id
	                                      ,'url'            : record.link_url
	                                      ,'article_id'     : record.link_article_id
	                            }, "model_"+gen));
	                        }
	                    }
	               //  ,'-'
	                 //Delete
	                 ,{
	                        id      : "spectrumgrids_link_delete_btn",
	                        iconCls : 'fugue_minus-button',
	                        xtype   : 'button',
	                        tooltip : 'Remove Selected Link',
	                        handler : function()
	                        {
	                            if(me.getSelectionModel().getSelection().length==0)
	                            { 
	                                return ;
	                            }
	                            var record  =me.getSelectionModel().getSelection()[0].data;
	                            if(record.owned_by  !=  me.org_id )
	                            {//this is NOT an error
	                                Ext.MessageBox.alert({title:"You do not own this link.",msg:"Unable to Delete Link <b>["+record.link_name+"]</b>", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
	                                return ;
	                            }   
                                                                                                             
	                            var post={}
	                            post["link_id"]     =record.link_id;
	                            Ext.MessageBox.confirm('Delete Link', "Selected Link <b>["+record.link_name+"]</b> & Dependent Links will be Invisible. Are You sure ?", function(answer)
	                            {     
	                                if(answer=="yes")
	                                {
	                                    var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
	                                    Ext.Ajax.request({
	                                        url     : 'index.php/websites/json_delete_link/TOKEN:'+App.TOKEN,
	                                        params  : post, 
	                                        success : function(o)
	                                        {
	                                             box.hide();
	                                             var res=YAHOO.lang.JSON.parse(o.responseText);
	                                             if(res.result=="1")
	                                             {
	                                                 Ext.MessageBox.alert({title:"Status",msg:"Link <b>["+record.link_name+"]</b> Deleted Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
	                                                 linkManage.init();
	                                             }
	                                        },
	                                        failure:App.error.xhr
	                                    });    
	                                }
	                            }); 
	                        }
	                 }

	            );                                   
	            
	            if(config.fields==null)     config.fields       = ['link_id','link_name','link_url','link_article_id','link_parent_id','link_type_id','owned_by','org_id','isactive' ,'order','link_type_name'];
	            if(config.url==null)        config.url          = "/index.php/websites/json_get_links/"+App.TOKEN;
	            if(config.width==null)      config.width        = '100%';
	            if(config.height==null)     config.height       = 400;
	            
                
                config.viewConfig   = 
                {
                    plugins: 
                    {
                        ptype: 'treeviewdragdrop'
                    },
                    listeners: 
                    {
                        beforedrop  : function(node, data, dropRec, dropPosition) 
                        {   
                            /*
                            *  config.Plot is a 2D array col(0) = parent & col(1) = id & col(2)= active
                            */
                            var dragData=me.getSelectionModel().getSelection()[0].data;
                            
                            if(dragData.link_name=='')     return false;       //Not able to drag fake record
                            if(dropRec.data.link_name=='') return false;       //Not able to drop (under-after-before) fake record
                            
                                     
                            //Append To Parent (Not Ordered)
                            if(dropPosition=='append')
                            {   
                                var dragIndex               =me.indexOf_2D(config.Plot,1,dragData.link_id);
                                config.Plot[dragIndex][0]   =dropRec.data.link_id;
                            }
                            //Append To Sibling
                            else
                            {
                                //Get Target Parent_id
                                var dropIndex               =me.indexOf_2D(config.Plot,1,dropRec.data.link_id);
                                var targetParent_id         =config.Plot[dropIndex][0];
                                //replace drag node parent_id
                                var dragIndex               =me.indexOf_2D(config.Plot,1,dragData.link_id);
                                config.Plot[dragIndex][0]   =targetParent_id;
                                                                               
                                //Move(cut-paste) In the Right Place
                                if(dropPosition=='before')
                                    config.Plot             =me.cutNpaste_in_array(config.Plot,dragIndex,dropIndex-1);
                                if(dropPosition=='after')
                                    config.Plot             =me.cutNpaste_in_array(config.Plot,dragIndex,dropIndex);
                            }   
                            return true;
                        },
                        drop        : function(node, data, dropRec, dropPosition) 
                        {  
                            //alert(YAHOO.lang.dump(config.Plot));
                        }
                    }
                };
                
                Ext.Ajax.request
                ({
                    url     : "/index.php/websites/json_get_links/TOKEN:"+App.TOKEN ,
                    params  : {test:'test'}, 
                    success : function(response)
                    {
                        var result  =YAHOO.lang.JSON.parse(response.responseText);
                        config.Plot =result.source;
                        me.org_type =result.org_type;
                        me.org_id   =result.org_id;
                    }
                });    
	            
	            //One Time Loading                                         
	            config.linkTypeStore.on("load",function()
	            {    
	                Ext.getCmp("links_link_types_combo").store.add({type_id:0,type_name:"All"}); 
	                var index   =Ext.getCmp("links_link_types_combo").store.find("type_name","All");
	                var rec     =Ext.getCmp("links_link_types_combo").store.getAt(index);          
	                Ext.getCmp("links_link_types_combo").select(rec);    
	            });                                                  
	            
	            this.config=config;
	            this.callParent(arguments); 

	        },
	        afterRender: function() 
	        {  
	            var me=this;
                
                
                
	            this.callParent(arguments);
	        },
            indexOf_2D:function(a,searchIndex,value)
            {
                for(var i=0;i<a.length;i++)
                    if(a[i][searchIndex]==value)
                        return i;
            },
            cutNpaste_in_array:function(a,dragIndex,dropIndex)  //   [][][][]  [][]
            {
                var me          =this;
                var specificRow =a.splice(dragIndex,1);
                var left_side   =a.splice(0,dropIndex);
                var right_side  =a;
                            
                var result      =[];
                return result.concat(left_side,specificRow,right_side);                                                                  
            }
	});
}