
if(!App.dom.definedExt('Ext.spectrumgrids.venue')){ //avoid :  define the same thing twice
Ext.define('Ext.spectrumgrids.venue',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
    
    ,config                 :null
    ,final_form             :null
    ,constructor : function(config) 
    {   
    	var me=this;
        if(!config.hide_icon)   
            config.hide_icon=false;//optroinal parameter to hide this column from other components
        config.columns  =
        [    
             {
                text        : ''
                ,hidden:config.hide_icon
                ,dataIndex  : 'icon'
                ,width      :30
                
            }
            ,{
                text        : 'Name'
                ,dataIndex  : 'venue_name'
                ,width      :200                                
                ,editor     :
                {
                    xtype       :'textfield'
                    ,allowBlank : false
                }
            }
            ,{
                text        : 'Venue Type'
                ,dataIndex  : 'lu_descr'                                        
                ,flex       :1
            }          
                                            
        ];
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
               
               
        config.topItems.push
        (
            
        );
        
        config.bottomLItems.push
        (
            //Create New
            {
                    iconCls     :"stl_sport_diamond--plus",
                    text        : '',
                    id          :'venues_grid_add_btn_',
                    tooltip     :"Add New Venue",
                    handler: function()
                    {
                        //Load venue map  and zoom infacility Location
                    	var facilityRec =me.owner.getSelectionModel().getSelection()[0].data;
                        var map=new Ext.googlemap(
                        {          
                                 latitude    :facilityRec.facility_latitude,
                                 longitude   :facilityRec.facility_longitude,
                                 icon_char   :'',
                                 enable_click:true,
                                 owner       :me
                        });
                        
                        //Create New Venu Screen
                        var _config=
                        {
                               width   : 800,
                               height  : 400,
                               bottomItems:   
                               [
                                   '->'
                                   ,{   
                                        xtype   : "button",
                                        text    : "Save",
                                        iconCls : 'table_save',
                                        pressed : true,       
                                        tooltip : 'Save',
                                        handler : function()
                                        {        
                                             if(final_form.getForm().isValid())
                                             {
                                                 var post={}
                                                 post=form.getValues();
                                                 post["venue_latitude"] = map.selected_lat;
                                                 post["venue_longitude"]= map.selected_lng;
                                                 post["facility_id"]    = facilityRec.facility_id;
                                                 
                                                 var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                 Ext.Ajax.request(
                                                 {
                                                    url     : 'index.php/facilities/post_save_new_venue/'+App.TOKEN+'/',
                                                    params  : post,
                                                    success : function(response)
                                                    {
                                                        var res=YAHOO.lang.JSON.parse(response.responseText);
                                                        if(res.result=="1")
                                                        {
                                                            box.hide();
                                                            Ext.MessageBox.alert({title:"Status",msg:"New Venue Named <b>'"+post["venue_name"]+"'</b> for <b>'"+facilityRec.facility_name+"'</b> Facility Created Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                            win.Hide();
                                                            me.getStore().load();
                                                        }
                                                    }
                                                 });    
                                             }        
                                         }
                                   }
                               ]
                        }         
                        
                        var form=facilitiesForms.formVenueDetails();
                        var final_form=new Ext.spectrumforms.mixer(_config,[form,map],['form','form']);
                        
                        var win_cnf=
                        {
                           title       : "'"+facilityRec.facility_name+"' Venues",
                           final_form  : final_form
                        }
                        var win=new Ext.spectrumwindow.facilities(win_cnf);
                        win.on('hide',function()
                        {
                            me.reloadFacilityGrid();
                        });
                        win.show();
                    }
            }
        );
 
        config.bottomRItems.push
        (
        
 
               //View Map
                {
                    iconCls     : "map_magnify",
                    text        : '',
                    tooltip     : "View Venue Map",
                    handler     : function()
                    {
                        if(me.getSelectionModel().getSelection().length==0)
                        {
                         //   Ext.MessageBox.alert({title:"Error",msg:"Please Select a Venue", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        var record  =me.getSelectionModel().getSelection()[0].data;
                        var map     =new Ext.googlemap(
                        {          
                             latitude    :record.venue_latitude,
                             longitude   :record.venue_longitude,
                             icon_char   :record.icon_char,
                             
                             enable_click   :false,
                             owner          :me
                        });
                        var venue_config=
                        {
                            width       :800,
                            height      :400
                        }
                        me.final_form=new Ext.spectrumforms.mixer(venue_config,[map],['form']);
                        var win_cnf=
                        {
                                title       : "View Venue <b>'"+record.venue_name+"'</b> Map",
                                final_form  : me.final_form
                        }
                        var win=new Ext.spectrumwindow.facilities(win_cnf);
                        win.show();
                    }
                }
             
                //Edit
                ,{
                    iconCls     : "stl_sport_diamond--pencil",
                    text        : '',
                    tooltip     : "Modify venue",
                    handler     : function()
                    {
                        if(me.getSelectionModel().getSelection().length==0)
                        {
                           // Ext.MessageBox.alert({title:"Error",msg:"Please Select a Venue", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        var record=me.getSelectionModel().getSelection()[0].data;
                        var map=new Ext.googlemap(
                        {          
                             latitude    :record.venue_latitude,
                             longitude   :record.venue_longitude,
                             icon_char   :record.icon_char,
                             
                             enable_click:true,
                             owner       :me
                        });
                         
                        var venue_config=
                        {
                            width   : 800,
                            height  : 400,
                            bottomItems:   
                            [
                                   '->'
                                   ,{   
                                        xtype   : "button",
                                        text    : "Update",
                                        iconCls : 'table_save',
                                        pressed : true,       
                                        tooltip : 'Update',
                                        handler : function()
                                        {        
                                             if(final_form.getForm().isValid())
                                             {
                                                 var post={}
                                                 post=form.getValues();
                                                 post["venue_latitude"] = map.selected_lat;
                                                 post["venue_longitude"]= map.selected_lng;
                                                 post["venue_id"]       = record.venue_id;
                                                 post["facility_id"]    = record.facility_id;
                                                 
                                                 var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                 Ext.Ajax.request(
                                                 {
                                                    url     : 'index.php/facilities/post_save_new_venue/'+App.TOKEN+'/',
                                                    params  : post,
                                                    success : function(response)
                                                    {
                                                        var res=YAHOO.lang.JSON.parse(response.responseText);
                                                        if(res.result=="1")
                                                        {
                                                            box.hide();
                                                            Ext.MessageBox.alert({title:"Status",msg:"Venue <b>'"+post["venue_name"]+"'</b> Updated Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                            win.Hide();
                                                            me.getStore().load();
                                                        }
                                                    }
                                                 });    
                                             }        
                                         }
                                   }
                            ]
                        }          
                        var form        =facilitiesForms.formVenueDetails();
                        var final_form  =new Ext.spectrumforms.mixer(venue_config,[form,map],['form','form']);
                        
                        var win_cnf=
                        {
                        	title       : "Edit Venue <b>'"+record.venue_name+"'</b>",
                            final_form  : final_form
                        }
                        var win=new Ext.spectrumwindow.facilities(win_cnf);
                        win.on('hide',function()
                        {
                            me.reloadFacilityGrid();
                        });
                        win.show();
                        
                        
                        //Load Record
                        final_form.loadRecord(Ext.ModelManager.create(
                        {
                            "venue_name"        : record.venue_name,
                            "venue_type_id"     : record.venue_type,
                            "venue_id"          : record.venue_id     
                            
                        }, "model_" +config.generator));
                    }
                }
                ,'-'
                //delete
                ,{
                    iconCls     : "fugue_minus-button",
                    text        : '',
                    tooltip     : "Remove venue",
                    handler     : function()
                    {
                        if(me.getSelectionModel().getSelection().length==0)
                        {                                                          
                           // Ext.MessageBox.alert({title:"Error",msg:"Please Select a Venue", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;   
                        }
                        var record=me.getSelectionModel().getSelection()[0].data;
                        Ext.MessageBox.confirm('Delete Action', "Are you sure?  ", function(answer)
                        {     
                            if(answer=="yes")
                            {      
                                var post={}
                                post["venue_id"]=record.venue_id;
                                
                                //var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                Ext.Ajax.request(
                                {
                                    url     : 'index.php/facilities/json_delete_venue/TOKEN:'+App.TOKEN,
                                    params  : post,
                                    success : function(response)
                                    {
                                        var res=YAHOO.lang.JSON.parse(response.responseText);
                                        //box.hide();
                                        
                                        if(res.result=="-2")
                                        {
                                            Ext.MessageBox.alert({
                                            	title:"Cannot Delete"
                                            	,msg:"Venue <b>'"+record.venue_name
                                            	+"'</b> has been Scheduled for Games.  Not Able to Remove."
                                            	, icon: Ext.Msg.ERROR
                                            	,buttons: Ext.MessageBox.OK});     
										}         
                                        else if(res.result==-1)
                                        {
											//not owned by us
											Ext.MessageBox.alert({
                                            	title:"Cannot Delete"
                                            	,msg:"Your Spectrum account does not have permission to delete venue<b>'"
                                            		+record.venue_name
                                            	+"'</b>.  It may have been created by another league or orgaization."
                                            	, icon: Ext.Msg.ERROR
                                            	,buttons: Ext.MessageBox.OK});  
                                        }
                                        else
                                        {
                                            me.getStore().load();
                                        	/*Ext.MessageBox.alert({
                                        		title:"Status"
                                        		,msg:"Venue <b>'"+record.venue_name
                                        		+"'</b> Removed Successfully"
                                        		, icon: Ext.Msg.INFO
                                        		,buttons: Ext.MessageBox.OK
                                        	});*/
                                        }
                                            
                                    }
                                });    
                            }
                        });  
                    }
                }    
        );
        if(config.fields==null)     config.fields       = ['venue_id','facility_id','lu_descr','venue_type','venue_longitude','venue_latitude','venue_name','icon','icon_char'];
        if(config.sorters==null)    config.sorters      = config.fields;
        if(config.pageSize==null)   config.pageSize     = 1000;
        if(config.url==null)        config.url          = "index.php/facilities/json_get_venues/"+App.TOKEN;
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
    },                       
    transferData:function(address)
    {
        //Does nothing 
    },
     
    afterRender: function() 
    {  
        var me=this;
        if(!me.override_edit)
        {   
            me.on("edit",function(e)
            {
                var record=e.record.data;
                var post={}
                post["venue_id"]        =record.venue_id;
                post["venue_name_new"]  =record.venue_name;
                 
                var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                Ext.Ajax.request(
                {
                        url     : 'index.php/facilities/json_update_venue_name/'+App.TOKEN,
                        params  : post,
                        success : function(response)
                        {
                             box.hide();
                             var res=YAHOO.lang.JSON.parse(response.responseText);
                             if(res.result=="1")
                             {
                                 me.getStore().load();
                                 Ext.MessageBox.alert({title:"Status",msg:"Venue <b>'"+record.venue_name+"'</b> Renamed Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                 e.record.commit();
                             }                     
                             else 
                                 e.record.reject();
                        }
                });    
            });
        }
    
        this.callParent(arguments);         
    },
    reloadFacilityGrid:function()
    {
        var me          =this;
        var record      =me.owner.getSelectionModel().getSelection()[0].data;
        me.owner.getStore().load();   
        
        var x=function()
        {   
            var recIndex;                                                                   
            recIndex    =me.owner.getStore().find( "facility_id", record.facility_id, 0, true, false, false);    
            
            me.owner.getSelectionModel().select( recIndex, true, false); 
            me.owner.getStore().removeListener('load', x);
        }
        me.owner.getStore().on("load",x);
    }
    
});}
