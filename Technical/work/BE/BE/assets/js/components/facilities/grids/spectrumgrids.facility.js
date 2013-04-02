if(!App.dom.definedExt('Ext.spectrumgrids.facility')){
Ext.define('Ext.spectrumgrids.facility',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
    
    ,config     :null
    ,final_form :null
    ,sw         :null   
                       
    ,constructor : function(config) 
    {   
        var me=this;
        config.columns  =
        [    
             {
                text: ''
                ,dataIndex: 'icon'
                ,width:30
            }
            ,{
                text        : 'Facility'
                ,dataIndex  : 'facility_name'
                ,width      :200                                
                ,editor     :{xtype:'textfield',allowBlank: false}
            }
            ,{
                text        : 'Address',
                xtype       : 'templatecolumn',
                tpl         : '{address_street}, {address_city}, {region_abbr}, {country_name}, {postal_value} ',
                width       :450
            }
            ,{
                text        : '# of Venues'
                ,dataIndex  : 'venues_count'
                ,flex       :1
            }
                                            
        ];
        //Fresh top/bottom components list
        if(!config.topItems)     config.topItems=[];
        if(!config.bottomLItems) config.bottomLItems=[];
        if(!config.bottomRItems) config.bottomRItems=[];
        
        config.PressedClass     ='';//"x-btn-default-toolbar-small-pressed btn_active";
        config.unPressedClass   ='';//"x-btn-default-toolbar-small-pressed btn_inactive";
        config.inActiveRedClass ='';//"x-btn-default-toolbar-small btn_inactive_red";
               
        config.topItems.push
        (
                //My Facilities  0
                {
                    
                    id      : 'myfacilities_btn',
                    cls     : config.PressedClass,
                    xtype   : 'button',
                    text    : 'My Facilities',
                    tooltip : 'My Facilities',
                    margins : '0 0 0 5',
                    width   : 80,
                    handler : function()
                    {   
                        var thisButton  ='myfacilities_btn';
                        Ext.getCmp(thisButton).removeCls(config.unPressedClass); 
                        Ext.getCmp(thisButton).addClass(config.PressedClass); 
                        
                        var btn, otherBtn=['facilities_25_btn','facilities_50_btn','facilities_100_btn','facilities_all_btn']
                        //Make Others Inactive
                        for(var i=0;i<otherBtn.length;i++)
                        {
                        	//SB:fix for IE9 for in loop bug, was getting some undefiend elements here 
                        	btn=Ext.getCmp(otherBtn[i]);
                        	if(btn){
	                            btn.removeCls(config.PressedClass);     
	                            btn.addClass(config.unPressedClass);  
							}  
                        }   
                        
                        me.getStore().proxy.extraParams.dist    = -2;
                        me.getStore().load();
                    }
                },
                //Facilities 25 KM 1
                {
                    
                    id      : 'facilities_25_btn',
                    cls     : config.unPressedClass,
                    xtype   : 'button',
                    text    : 'Within 25 KM',
                    tooltip : 'All Facilities Within 25 KM',
                    margins : '0 0 0 5',
                    width   : 80,
                    handler : function()
                    {   
                        var thisButton='facilities_25_btn';
                        Ext.getCmp(thisButton).removeCls(config.unPressedClass); 
                        Ext.getCmp(thisButton).addClass(config.PressedClass); 
                        
                        var otherBtn=['myfacilities_btn','facilities_50_btn','facilities_100_btn','facilities_all_btn']
                        //Make Others Inactive
                        for(var i=0;i<otherBtn.length;i++)
                        {
                            Ext.getCmp(otherBtn[i]).removeCls(config.PressedClass);     
                            Ext.getCmp(otherBtn[i]).addClass(config.unPressedClass);    
                        }   
                        
                        me.getStore().proxy.extraParams.dist    = 25;
                        me.getStore().load();
                    }
                },
                //Facilities 50 KM  2
                {
                    
                    id      : 'facilities_50_btn',
                    cls     : config.unPressedClass,
                    xtype   : 'button',
                    text    : 'Within 50 KM',
                    tooltip : 'All Facilities Within 50 KM',
                    margins : '0 0 0 5',
                    width   : 80,
                    handler : function()
                    {   
                        var thisButton='facilities_50_btn';
                        Ext.getCmp(thisButton).removeCls(config.unPressedClass); 
                        Ext.getCmp(thisButton).addClass(config.PressedClass); 
                        
                        var otherBtn=['facilities_25_btn','myfacilities_btn','facilities_100_btn','facilities_all_btn']
                        //Make Others Inactive
                        for(var i=0;i<otherBtn.length;i++)
                        {
                            Ext.getCmp(otherBtn[i]).removeCls(config.PressedClass);     
                            Ext.getCmp(otherBtn[i]).addClass(config.unPressedClass); 
                        }   
                        
                        me.getStore().proxy.extraParams.dist    = 50;
                        me.getStore().load();
                    }
                },
                //Facilities 100 KM   4
                {
                    
                    id      : 'facilities_100_btn',
                    cls     : config.unPressedClass,
                    xtype   : 'button',
                    text    : 'Within 100 KM',
                    tooltip : 'All Facilities Within 100 KM',
                    margins : '0 0 0 5',
                    width   : 80,
                    handler : function()
                    {   
                        var thisButton='facilities_100_btn';
                        Ext.getCmp(thisButton).removeCls(config.unPressedClass); 
                        Ext.getCmp(thisButton).addClass(config.PressedClass); 
                        
                        var otherBtn=['facilities_25_btn','facilities_50_btn','myfacilities_btn','facilities_all_btn']
                        //Make Others Inactive
                        for(var i=0;i<otherBtn.length;i++)
                        {
                            Ext.getCmp(otherBtn[i]).removeCls(config.PressedClass);     
                            Ext.getCmp(otherBtn[i]).addClass(config.unPressedClass); 
                        }   
                        
                        me.getStore().proxy.extraParams.dist    = 100;
                        me.getStore().load();
                    }
                },
                //All Facilities  5
                {               
                    id      : 'facilities_all_btn',
                    cls     : config.unPressedClass,
                    xtype   : 'button',
                    text    : 'All Facilities',
                    tooltip : 'All Facilities ',
                    margins : '0 0 0 5',
                    width   : 80,
                    handler : function()
                    {   
                        var thisButton='facilities_all_btn';
                        Ext.getCmp(thisButton).removeCls(config.unPressedClass); 
                        Ext.getCmp(thisButton).addClass(config.PressedClass); 
                        
                        var otherBtn=['facilities_25_btn','facilities_50_btn','facilities_100_btn','myfacilities_btn']
                        //Make Others Inactive
                        for(var i=0;i<otherBtn.length;i++)
                        {
                            Ext.getCmp(otherBtn[i]).removeCls(config.PressedClass);     
                            Ext.getCmp(otherBtn[i]).addClass(config.unPressedClass); 
                        }   
                        
                        me.getStore().proxy.extraParams.dist    = -1;
                        me.getStore().load();
                    }
                }
        );
        config.bottomLItems.push
        (
                //Create New
                {
                    iconCls     :"stl_sport_diamond--plus",
                    text        : '',
                    tooltip     :"Add New Facility",
                    handler: function()
                    {
                            var map=new Ext.googlemap(
                            {          
                                 latitude    :0,
                                 longitude   :0,
                                 icon_char   :'a',
                                 
                                 enable_click:true,
                                 owner       :me
                            });
                            var search_handler=function()
                            {
                                me.findFormAddress(map);
                            } 
                            var facility_config=
                            {
                                width   : 800,
                                height  : 400,
                                bottomItems:   
                                [
                                    '->'
                                    ,{   
                                         xtype   :"button",
                                         text    :"Save",
                                         iconCls :'table_save',
                                         pressed :true,
                                         tooltip :'Save',
                                         handler :function()
                                         {                     
                                             if (me.final_form.getForm().isValid()) 
                                             {   
                                                 var post={}
                                                 post                       =me.final_form.getForm().getValues();
                                                 post["postal_value"]       =post["postal_value"].toUpperCase();
                                                 post["facility_latitude"]  =map.selected_lat;
                                                 post["facility_longitude"] =map.selected_lng;
                                                 
                                                 var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                 Ext.Ajax.request(
                                                 {
                                                        url     : 'index.php/facilities/post_save_new_facility/TOKEN:'+App.TOKEN+'/',
                                                        params  : post,
                                                        success : function(response)
                                                        {
                                                             box.hide();
                                                             var res=YAHOO.lang.JSON.parse(response.responseText);
                                                             if(res.result=="1")
                                                             {
                                                                 win.Hide();
                                                                 me.getStore().load();
                                                                 Ext.MessageBox.alert({title:"Status",msg:"Facility <b>'"+post["facility_name"]+"'</b> Saved Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                 
                                                                 me.getStore().proxy.extraParams.dist    = -2;
                                                                 me.getStore().load();
                                                             }                             
                                                        }
                                                 });    
                                             }   
                                         }                          
                                    }
                                ]
                            }     
                            
                            var countryStore    =new simpleStoreClass().make(['country_id','country_name'],"index.php/facilities/json_get_country_store/TOKEN:"+App.TOKEN,{test:false});              
                            var regionStore     =null;
                            var win=null;
                            
                            countryStore.on("load",function()
                            {
                                var rec     =countryStore.getAt(0);
                                regionStore     =new simpleStoreClass().make(['region_id','region_name'],"index.php/facilities/json_get_region_store/TOKEN:"+App.TOKEN,{country_id:rec.data.country_id});                
                                
                                var facility_form=facilitiesForms.formFacilityDetails(search_handler,countryStore,regionStore);
                                me.final_form=new Ext.spectrumforms.mixer(facility_config,[facility_form,map],['form','form']);
                                
                                var win_cnf=
                                {
                                        title       : 'Create New Facility',
                                        final_form  : me.final_form
                                }
                                win=new Ext.spectrumwindow.facilities(win_cnf);
                                win.show();
                                
                                Ext.getCmp("country_abbr").select(rec);    
                            });
                    }
                }
        );
        
        config.bottomRItems.push
        (
                //show venues
                {
                    iconCls     :"fugue_map-pin",
                    text        : '',
                    tooltip     :"Manage Facility Venues",
                    handler     : function()
                    {
                        if(me.getSelectionModel().getSelection().length==0)
                        {
                            //Ext.MessageBox.alert({title:"Error",msg:"Please Select a Facility", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;   
                        }
                        var record=me.getSelectionModel().getSelection()[0].data;       
                        me.collapse();                              
                    }
                }
                
                //View Map
                ,{
                    iconCls     :"map_magnify",
                    tooltip     :"View Facility Map",
                    handler: function()
                    {
                        if(me.getSelectionModel().getSelection().length==0)
                        {
                           // Ext.MessageBox.alert({title:"Error",msg:"Please select a Facility", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        var record=me.getSelectionModel().getSelection()[0].data;
                                            
                        var map=new Ext.googlemapv3(
                        {                      
                             latitude    :record.facility_latitude,
                             longitude   :record.facility_longitude,
                             icon_char   :record.icon_char,
                             
                             enable_click:false,
                             owner       :me
                        });
                        
                        var facility_config=
                        {
                            width       :800,
                            height      :375
                        }
                        me.final_form=new Ext.spectrumforms.mixer(facility_config,[map],['form']);   
                        var win_cnf=
                        {
                                title       : 'View Facility Map ('+record.facility_name+')',
                                final_form  : me.final_form
                        }
                        var win=new Ext.spectrumwindow.facilities(win_cnf);
                        win.show();
                                                         
                        //var address=record.country_name+' '+record.region_abbr+' '+record.address_city+' '+record.address_city;
                        //map.find_location(address,13);                    
                    }
                }
                
                //Edit
                ,{
                    iconCls     :"stl_sport_diamond--pencil",
                    text        : '',
                    tooltip     :"Modify Facility",
                    handler     : function()
                    {
                        me.sw=0;
                        if(me.getSelectionModel().getSelection().length==0)
                        {
                            Ext.MessageBox.alert({title:"Error",msg:"Please select a Facility", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        var record  =me.getSelectionModel().getSelection()[0].data;
                        var map     =new Ext.googlemap(
                        {          
                             latitude    :record.facility_latitude,
                             longitude   :record.facility_longitude,
                             icon_char   :record.icon_char,
                             
                             enable_click:true,
                             owner       :me
                        });
                         
                        var search_handler=function()
                        {
                            me.findFormAddress(map);
                        } 
                        var facility_config=
                        {
                            width   : 800,
                            height  : 400,
                            bottomItems:   
                            [
                                '->'
                                ,{   
                                     xtype   :"button",
                                     text    :"Update",
                                     iconCls :'table_save',
                                     pressed :true,
                                     tooltip :'Update Facility',
                                     handler :function()
                                     {   
                                         //Find lat lang
                                         me.findFormAddress(map);
                                         //********************        
                                         var record= me.getSelectionModel().getSelection()[0].data;
                                         if (me.final_form.getForm().isValid()) 
                                         {   
                                             var post={}
                                             post                       =me.final_form.getForm().getValues();
                                             post["facility_id"]        =record.facility_id;
                                             post["facility_latitude"]  =map.selected_lat;
                                             post["facility_longitude"] =map.selected_lng;
                                             
                                             var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                             Ext.Ajax.request(
                                             {
                                                url     : 'index.php/facilities/post_save_update_facility/TOKEN:'+App.TOKEN,
                                                params  : post,
                                                success : function(response)
                                                {
                                                     box.hide();
                                                     var res=YAHOO.lang.JSON.parse(response.responseText);
                                                     if(res.result=="1")
                                                     {
                                                         win.Hide();
                                                         Ext.MessageBox.alert({title:"Status",msg:"Facility <b>'"+post["facility_name"]+"'</b> Updated Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                         me.getStore().load();
                                                     }                             
                                                }
                                             });    
                                         }   
                                     }                          
                                }
                            ]
                        }          
                        var countryStore    =new simpleStoreClass().make(['country_id','country_name'],"index.php/facilities/json_get_country_store/TOKEN:"+App.TOKEN,{test:false});              
                        var regionStore     =null;
                        var win=null;                         
                        
                        countryStore.on("load",function()
                        {       
                                var regionStore     = new simpleStoreClass().make(['region_id','region_name'],"index.php/facilities/json_get_region_store/TOKEN:"+App.TOKEN,{country_id:record.country_id});                
                                
                                var facility_form=facilitiesForms.formFacilityDetails(search_handler,countryStore,regionStore);
                                me.final_form=new Ext.spectrumforms.mixer(facility_config,[facility_form,map],['form','form']);
                                
                                var win_cnf=
                                {
                                        title       : "Edit Facility '"+record.facility_name+"'",
                                        final_form  : me.final_form
                                }
                                win=new Ext.spectrumwindow.facilities(win_cnf);
                                win.show();
                                                                             
                                //Load record
                                regionStore.on("load",function()//Just runs 2 times for the firstTime    
                                {             
                                    if(me.sw>=2)
                                    {
                                        document.getElementsByName("region_abbr")[0].value='';
                                        return;
                                    }                                                
                                    me.sw++;  
                                    
                                    facility_form.loadRecord(Ext.ModelManager.create(
                                    {
                                        "facility_name"     : record.facility_name,
                                        "country_abbr"      : record.country_id,
                                        "region_abbr"       : record.region_id,
                                        "address_city"      : record.address_city,
                                        "address_street"    : record.address_street,
                                        "postal_value"      : record.postal_value.toUpperCase()
                                        
                                    }, "model_" +config.generator));       
                                    
                                    //var address=record.country_name+' '+record.region_abbr+' '+record.address_city+' '+record.address_street+' '+record.postal_value;
                                    //map.find_location(address,13);
                                });
                                
                        });   
                    }
                }
                ,'-'
                //delete
                ,{
                    iconCls :"fugue_minus-button",
                    text: '',
                    scope:this,
                    tooltip:"Remove Facility",
                    handler: function(which)
                    {
                        var selected=this.getSelectionModel().getSelection();
                        if(selected.length==0)
                        {
                            //Ext.MessageBox.alert({title:"Alert",msg:"No record Selected", icon: Ext.Msg.WARNING,buttons: Ext.MessageBox.OK});
                            return;   
                        }
                        var record=selected[0].data;
                        
                        var post={}
                        post["facility_id"]=record.facility_id;
                        
                        Ext.MessageBox.confirm('Delete Action', "Are you sure?  This will also delete all venues within the facility.", function(answer)
                        {     
                            if(answer=="yes")
                            {
                                var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                Ext.Ajax.request(
                                {
                                    url     : 'index.php/facilities/json_delete_facility/TOKEN:'+App.TOKEN,
                                    params  : post,
                                    success : function(response)
                                    {
                                        box.hide();
                                        var res=YAHOO.lang.JSON.parse(response.responseText);
                                         if(res.result=="-2")
                                            Ext.MessageBox.alert({title:"Error",msg:"This Facility or its Venues has been scheduled", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                         if(res.result=="-1")
                                            Ext.MessageBox.alert({title:"Error",msg:"You are trying to delete a facility that is being used by other leagues, or has been set up by another league.", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                         else
                                            me.getStore().load();
                                    }
                                });    
                            }
                        });  
                    }
                }    
        );
        
        if(config.fields==null)     config.fields       = ['facility_id','icon', 'facility_name','address','facility_latitude','facility_longitude','icon_char',"venues_count", 'address_street','address_city','region_id','region_abbr','country_id','country_name','country_abbr','postal_value'];
        if(config.sorters==null)    config.sorters      = config.fields;
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = "index.php/facilities/json_get_facilities/TOKEN:"+App.TOKEN;
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
        if(config.height==null)     config.height       = 300;
        
        
        //get Active Org Address
        Ext.Ajax.request(
        {
               url     : 'index.php/facilities/json_getEntityAddress/TOKEN:'+App.TOKEN+'/',
               params  : {test:'test'},
               success : function(response)
               {                                       
                    var res     =YAHOO.lang.JSON.parse(response.responseText);
                    
                    var lat     =res.result[0].address_lat;
                    var lng     =res.result[0].address_lon;
                                             
                    if(lat==null || lng==null)
                    {                                            
                        Ext.MessageBox.alert({title:"League Address not found",msg:"Unable to Use Distance Calculation filter buttons, because the league address is either incomplete, or not found on google maps."
                        	+" To edit your league address, use the 'My Org' button on the right panel. ", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});

                        //Disable Distance Filter Buttons
                        var otherBtn=['facilities_25_btn','facilities_50_btn','facilities_100_btn']
                        for(var i=0;i<otherBtn.length;i++)
                        {
                            Ext.getCmp(otherBtn[i]).setDisabled(true);
                            Ext.getCmp(otherBtn[i]).addClass(config.inActiveRedClass);
                        }
                    }   
               }
        });    
        
        this.callParent(arguments);  
     },
     transferData:function(address)
     {
         var me=this;
         var gen=Math.random();
         Ext.define('model_'+gen, {extend: 'Ext.data.Model',fields:[{type: 'string'},{type: 'string'},{type: 'string'}]});
         me.final_form.loadRecord(Ext.ModelManager.create(
         {
                 "address_city"      : address.city,     
                 "address_street"    : address.street,
                 "postal_value"      : address.postalcode
                 
         }, "model_" +gen)); 
         
         var rec    =Ext.getCmp('country_abbr').store.findRecord( 'country_name', address.countryName);
         Ext.getCmp('country_abbr').select(rec); 
         
         //for both case if (1)master combo loads slave or (2)not                                               
         //(1)
         var rec    =Ext.getCmp('region_abbr').store.findRecord( 'region_name', address.region);
         Ext.getCmp('region_abbr').select(rec);      
         //(2)
         Ext.getCmp('region_abbr').store.on("load",function()
         {
            var rec    =Ext.getCmp('region_abbr').store.findRecord( 'region_name', address.region);
            Ext.getCmp('region_abbr').select(rec);      
         });
     },
     findFormAddress:function(map)
     {
         var me=this;
         var formValues=me.final_form.getForm().getValues();
                                                               
         var country    =document.getElementsByName("country_abbr")[0].value;
         var region     =document.getElementsByName("region_abbr")[0].value;
                                                                             
         var city       =formValues.address_city;
         var postal     =formValues.postal_value;
         var address    =formValues.address_street;
         
         if(country=='' && region=='' && city=='' && postal=='' && address=='')
         {
         	 //Not an error.
             Ext.MessageBox.alert({title:"Cannot Proceed",msg:"Please Fill In the Form", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
             return;
         }
         
         var _address=country+' '+region+' '+city+' '+postal+' '+address;
         map.find_location(_address,15);
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
                    post["facility_id"]        =record.facility_id;
                    post["facility_name_new"]  =record.facility_name;
                     
                    var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                    Ext.Ajax.request(
                    {
                            url     : 'index.php/facilities/json_update_facility_name/'+App.TOKEN,
                            params  : post,
                            success : function(response)
                            {
                                 box.hide();
                                 var res=YAHOO.lang.JSON.parse(response.responseText);
                                 if(res.result=="1")
                                 {
                                     me.getStore().load();
                                     Ext.MessageBox.alert({title:"Status",msg:"Facility <b>'"+record.facility_name+"'</b> Renamed Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                     e.record.commit();
                                 }                     
                                 else 
                                     e.record.reject();
                            }
                    });    
                }); 
            }      /*                                 
            if(!this.override_collapse)                              
            {   
                this.on("collapse",function(){});
            }
            if(!this.override_expand)
            {   
                this.on("expand",function(){});
            }       */                          
            
            this.callParent(arguments);         
     }                       
});
}