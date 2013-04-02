//**************************************************Article
var articleManageClass= function(){this.construct();};
articleManageClass.prototype=
{
     grid                   :null,
     article_types_store    :null,     
     
     construct:function()
     {    
         var me=this;
         
         me.init();
     }, 
     init:function()
     { 
        var me=this;
 
        //document.getElementById("publishing-shared-list2-container").style.height="0";
        //document.getElementById("publishing-shared-list1-container").style.height="100%";
        document.getElementById("publishing-shared-list2-container").style.display="none";
        document.getElementById("publishing-shared-list1-container").style.display="block";
                                           
        var config=
        {
            generator       : Math.random(),
            owner           : me,
            renderTo        : "publishing-shared-list1",
            title           : 'My Articles',
            extraParamsMore : {},
            collapsible     :false,

            //customized Components
            rowEditable     :true,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true

        }
        me.grid = Ext.create('Ext.spectrumgrids.article',config);

        me.grid.on("expand",function()
        {                                    
            me.setHeight(App.MAX_GRID_HEIGHT);
           // me.grid.doComponentLayout('100%','400px',true);
			me.doLayout();
        }); 
        me.grid.on('itemmouseenter', function(view, record, HTMLElement , index, e, Object ) 
        {        
           view.tip = Ext.create('Ext.tip.ToolTip', 
            {
                target: view.getEl(),
                delegate: view.itemSelector,
                trackMouse: true,
                anchor  :'right',
                listeners: 
                {
                    beforeshow: function(tip) 
                    {                                                                                                                                   
                        var record=view.getRecord(tip.triggerElement).data;
                        var text="<table style='font-size:9px'><tr><td>Creator :</td><td>"
	                        +record.created_by_name+"</td></tr>   <tr><td>Modifier : </td><td>"
	                        +record.modified_by_name+"</td></tr></table>"
                        tip.update(text);
                    }
                }
            });
            
        }); 
 
       
     }                               
}
//**************************************************Link
var linkManageClass= function(){this.construct();};
linkManageClass.prototype=
{
     treeview:null,
     construct:function()
     {    
         var me=this;
         me.init();
     }, 
     init:function()
     {
        var me=this;                                    
 
        //document.getElementById("publishing-shared-list2-container").style.height="0";
      //  document.getElementById("publishing-shared-list1-container").style.height="100%";
        document.getElementById("publishing-shared-list2-container").style.display="none";
        document.getElementById("publishing-shared-list1-container").style.display="block";
        
        var gen=Math.random(); 
        
        var config=
        {
            generator       : gen,
            renderTo        : "publishing-shared-list1",
            title           : 'My Links',
            extraParamsMore : {},
            collapsible     :false,
            //customized Components
            searchBar       :false
        }
        me.treeview = Ext.create('Ext.spectrumtreeviews.link',config);
       
        
       
        //Set combo
        var index=Ext.getCmp('links_link_types_combo').store.find( 'type_id', '0', 0, true, false, false);
        var rec=Ext.getCmp('links_link_types_combo').store.getAt(index);
        Ext.getCmp('links_link_types_combo').select(rec);
        
        
        me.treeview.on("load",function()
        {
           me.treeview.collapseAll(); 
        });
     }                         
     

} 


//**************************************************Module
var moduleManageClass= function(){this.construct();};
moduleManageClass.prototype=
{
     grid               :null,
     construct:function()
     {            
 
     }, 
     init:function()
     {
     	 
     	 this.locationStore=new simpleStoreClass().make(['type_id','type_name'],"index.php/websites/json_get_location_pos/TOKEN:"+App.TOKEN+'/',{test:"test"});
         
        var me=this;                                    
        
        var gen=Math.random(); 
        
        var config=
        {
            generator       : gen,
            owner           : me,
            renderTo        : 'publishing-shared-list1',

            title           : 'My Modules',
            url             : "/index.php/websites/json_get_websiteModules/"+App.TOKEN,
            extraParams     : {},
            collapsible     :true,
     
         	bottomRItems:
         	[
         	//module assets
		        {
					id:'module_assets_gridbtn'+gen
					,xtype:'button'
					,iconCls:'picture'
					,handler:function()
					{
						var sel = moduleManage.grid.getSelectionModel().getSelection();
						if(!sel.length){return;}
						//.log(sel[0].data);
						
						if(sel[0].data.module_id != 6)
						{
							Ext.MessageBox.alert('Not Enabled','Assets and Images not enabled for this module');
							return;
						}
						
						
						moduleManage.grid.collapse();
						moduleManage.assets.w_m_id = sel[0].data.w_m_id;
						moduleManage.assets.show();
						moduleManage.assets.get();
					}
		        }
         	],
            locationStore   :me.locationStore,       
            
            
            viewConfig      :
            {
                    plugins: 
                    {
                        ptype       : 'gridviewdragdrop',
                        dragGroup   : 'firstGridDDGroup',
                        dropGroup   : 'firstGridDDGroup'
                    },
                    listeners: 
                    {
                        drop        : function(node, data, dropRec,dropPosition) 
                        {   
                            return true;
                        },
                        beforedrop  : function(node, data, dropRec,dropPosition) 
                        {   
                            return true; 
                        }   
                    }
                },
            ordering_handler:
            function()
            {                
                    var store=me.grid.getStore();
                    
                    //Order Array
                    var orderArray=new Array();
                    for(var i =0;i<store.data.items.length;i++)
                        orderArray.push(i+1);
                    
                    //wmid array
                    var wmIds='';
                    for(var i=0;i<store.data.items.length;i++)
                    {
						wmIds+=store.data.items[i].data.w_m_id+',';
                    } 
                    wmIds=wmIds.substring(0,wmIds.length-1);
                    
                    //module_ids
                    var mIds='';
                    for(var i=0;i<store.data.items.length;i++)
                    {
						
                        mIds+=store.data.items[i].data.module_id+',';
                        }

                    mIds=mIds.substring(0,mIds.length-1);
                            
                    var _location_id=Ext.getCmp('type_id'+config.generator).getValue();            
                    
                    //var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                    Ext.Ajax.request(
                    {
                        url     : "/index.php/websites/json_update_websiteModules_ordering/TOKEN:"+App.TOKEN,
                        params  : 
                        {
                            wmIds       :wmIds,
                            mIds        :mIds,
                            wmOrders    :orderArray.join(',')
                        },
                        success : function(response)
                        {                            
                          //  box.hide();
                            me.grid.getStore().load({params:{location_id:_location_id}});
                        }
                    });        
                },
            addremove_handler:
            function()
            {            
                 var _location_id=Ext.getCmp('type_id'+config.generator).getValue();
                 var add_config=
                 {
                         owner               :me.grid,
                         title               : '',
                         generator           : Math.random(),
                         url                 :"/index.php/websites/json_get_modules/TOKEN:"+App.TOKEN,
                         extraParams         : {location_id:_location_id},
                         width               :400,
                         height              :400
                 }
                 var module_dv= Ext.create('Ext.spectrumdataviews.module',add_config);
                 var form_config=
                 {
                     width:400,
                     height:475,
                     bottomItems:
                     [
                         'Select Modules to make modification'
                     ]
                 }
                 add_config.final_form=new Ext.spectrumforms.mixer(form_config,[module_dv]);
                 add_config.module_win=Ext.create('Ext.spectrumwindows',
                 {width:412,height:510,items:add_config.final_form,title:'Modules'});  
                 add_config.module_win.show();  
            }

        }
 
        me.grid = Ext.create('Ext.spectrumgrids.module',config);
        me.grid.on('expand',function()
        {
			//var g=Ext.getCmp(moduleManage.assets.grid_id);
			//if(g) g.collapse();
			moduleManage.assets.hide();
			
			
			this.setHeight(300);
			this.doLayout();
			
        });
        me.grid.on('collapse',function()
        {
        	
			var g=Ext.getCmp(moduleManage.assets.grid_id);
			if(g) g.expand();
			
			moduleManage.assets.hide();//hide it
			
			
			
        });
        //setup combo
        config.locationStore.on("load",function()
        {
            var index=Ext.getCmp('type_id'+config.generator).store.find( 'type_id', '1', 0, true, false, false);
            var rec=config.locationStore.getAt(index);
            Ext.getCmp('type_id'+config.generator).select(rec);
        });
            
      
     }
     
     ,assets:
     {
     	 grid_id:'_module_asset_grid_'
     	 ,w_m_id:-1
     	 ,get:function()
     	 {
			var g=Ext.getCmp(moduleManage.assets.grid_id);
			if(g)
			{
				//pas the new id to the grid, and refresh the grids data
				g.w_m_id=moduleManage.assets.w_m_id;
				g.refresh();
			} 
     	 }
		,grid:function()
		{
			//
			//.log('assets grid being created!!!');
			var g=Ext.create('Spectrum.grids.websites.module_asset',{
				renderTo:'module-asset-div'
				,id:moduleManage.assets.grid_id
				
			});
			//.log(g);
			g.hide();
		} 
		,show:function()
		{
			var g=Ext.getCmp(moduleManage.assets.grid_id);
			//.log(g);
			if(g)
			{
				g.show();
				g.expand();
			}
			Ext.get('module-asset-ctr').removeCls('dghidden')
		}
		,hide:function()
		{
			var g=Ext.getCmp(moduleManage.assets.grid_id);
			//.log(g);
			
			if(g)
			{
				g.hide();
			}
			Ext.get('module-asset-ctr').addCls('dghidden')
			
		}
		 
		 
     }
     
     
     
}

//**************************************************Quickpost
var quickPostClass= function(){this.construct();};
quickPostClass.prototype=
{
     article_types_store    :null,     
     
     construct:function()
     {   
         var me=this;
         
         this.article_types_store=new simpleStoreClass().make(['id','name'],"index.php/websites/json_get_article_types/TOKEN:"+App.TOKEN+'/',{test:"test"});
         //me.init();
     }, 
     init:function()
     {
        var me=this;
           
         var handler_addImages=function()
         {
             var me=this;
             var gen=Math.random(); 
             var config_dv=
             {
                     generator       : Math.random(),
                     title           : 'select',
                     url             : "index.php/websites/json_get_articleImages/TOKEN:"+App.TOKEN,
                     uploadhandler   : function()
                     {                  
                                          _final_form.getForm().submit(
                                          {
                                                     url: 'index.php/websites/json_upload_article_image/TOKEN:'+App.TOKEN,
                                                     waitMsg: 'Uploading...',
                                                     params: {test:true},
                                                     success: function(form, action)
                                                     {
                                                         var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                         dv.store.load(); 
                                                     },
                                                     failure: function(form, action){alert(YAHOO.lang.dump(action.response));}
                                          });                                                       
                     }
             }
             var dv= Ext.create('Ext.spectrumdataviews.imagePicker',config_dv);   
             var config_form=
             {
                     width   :400,
                     height  :375,
                     bottomItems:
                     [
                         '->'
                         ,{
                             id      : '',
                             iconCls : 'table_save',
                             text    : 'Apply',
                             xtype   : 'button',
                             tooltip : 'Apply to Content',
                             handler : function()
                             {
                                Ext.getCmp("form_article_details_editor").setValue
                                (
                                    Ext.getCmp("form_article_details_editor").getValue()+
                                    '<img src='+Ext.getCmp("spectrumdataview_template_url_comp").getValue()+' >'
                                ); 
                             }
                         }
                     ]    
             }
             var _final_form=new Ext.spectrumforms.mixer(config_form,[dv]);
             var _win=Ext.create('Ext.spectrumwindows',{width:412,height:410,items:_final_form,title:'Image Repository'});
             _win.show();
             //Ext.getCmp("spectrumdataview_template_preview_comp").hide()
             //Ext.getCmp("spectrumdataview_template_activate_comp").hide()
             //Ext.getCmp("spectrumdataview_template_purchase_comp").hide()
         }
         var form=websiteForms.create_form_article_details(handler_addImages,me.article_types_store);
         var new_article_config=
         {
             width   :800,
             height  :475,
             bottomItems:   
             [
                 "->"
                 ,{
                                 xtype   :"button",
                                 iconCls :'save',
                                 text    :"Save",
                                 cls:'x-btn-default-small',
                                 width   :70,
                                 handler:function()
                                 {
                                     var post =form.getForm().getValues();
                                     post["article_content_revised"] =post["article_content"].replace(/"/gi,'');
                                     if (form.getForm().isValid()) 
                                     {   
                                         form.getForm().submit({
                                             url: 'index.php/websites/json_new_article/TOKEN:'+App.TOKEN,
                                             waitMsg: 'Processing...',
                                             params: post,
                                             success: function(form, action)
                                             {
                                                 var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                 if(res.result=="1")
                                                 {
                                                     Ext.MessageBox.alert({title:"Status",msg:"Article Created successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                     win.Hide();
                                                     me.getStore().load();
                                                 }
                                                 
                                             },
                                             failure: function(form, action){alert(YAHOO.lang.dump(action.response));}
                                         });
                                     }            
                                 }                          
                         }
             ]                   
         }                         
         
         var final_form=new Ext.spectrumforms.mixer(new_article_config,[form]);
         var win_cnf=
         {
             title       : 'Create New Article',
             final_form  : final_form
         }
         var win=new Ext.spectrumwindow.publishing(win_cnf);
         win.show();
         
         me.article_types_store.on("load",function()
         {
            var index=me.article_types_store.find( 'id', '1', 0, true, false, false);
            var rec=me.article_types_store.getAt(index);
            Ext.getCmp("create_form_article_details_article_type_combo").select(rec);
         });
             
         
     }                               
}
var quickPost=new quickPostClass();
//**************************************************Template
var templateManageClass= function(){this.construct();};
templateManageClass.prototype=
{
     dv:null,
     construct:function()
     {   
         var me=this;
         me.init();
     }, 
     init:function()
     {
         var me=this;
         //document.getElementById("publishing-shared-list2-container").style.height="0";
        // document.getElementById("publishing-shared-list1-container").style.height="100%";
         document.getElementById("publishing-shared-list2-container").style.display="none";
         document.getElementById("publishing-shared-list1-container").style.display="block";
         
         var gen=Math.random(); 
            
         var config=
         {
                generator       : gen,
                renderTo        : "publishing-shared-list1",
                url             : "index.php/websites/json_get_templates/TOKEN:"+App.TOKEN+'/',
                title           : 'Templates'
         }
         var dv= Ext.create('Ext.spectrumdataviews.template',config);   
         
         
     }      
}




