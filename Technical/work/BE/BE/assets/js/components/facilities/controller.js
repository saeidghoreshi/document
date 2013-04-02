//*********************************************************************************************Facilities
var facilitiesManageClass= function(){this.construct();};
facilitiesManageClass.prototype=
{
     grid               :null,
     construct:function()
     {
         var me=this;
         this.init('all'); 
     }, 
     init:function()
     {
        var me=this;
        
        var config=
        {
            generator       : Math.random(),
            owner           : me,
            
            renderTo        : "facilities_list",
            title           : 'Facilities',
            extraParamsMore : {dist        : -2},
            collapsible     : true,

            //customized Components
            rowEditable     :true,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false
        }
            
        me.grid = Ext.create('Ext.spectrumgrids.facility',config);
        
        var venues_manage=null;
        me.grid.on("expand",function()
        {   
            if(venuesManage != null)
                venuesManage.grid.hide();
             
            me.grid.setHeight(App.MAX_GRID_HEIGHT);  
            me.grid.doLayout();             
        });           
        me.grid.on("collapse",function()
        {            
        	var records=me.grid.getSelectionModel().getSelection();
        	if(!records.length){return;}//in case zero are selected by user
            var record  =records[0].data;
            venuesManage=new venuesManageClass(me.grid);
            venuesManage.init(record.facility_id,record.facility_name);	                            
            
        });                 
     }
} 
var facilitiesManage=new facilitiesManageClass();

//*********************************************************************************************Venues
var venuesManageClass= function(owner){this.construct(owner);};
venuesManageClass.prototype=
{
     grid               :null, 
     owner              :null,
     venue_type_list    :null,
     //facility_list      :null,      
     
     construct:function(owner)
     {
        var me      =this;
        me.owner    =owner;
        
        //this.facility_list  =new simpleStoreClass().make(['facility_id','facility_name'],"index.php/facilities/json_get_facilities_store/TOKEN:"+App.TOKEN,{test:false});
        this.venue_type_list=new simpleStoreClass().make(['venue_type','lu_descr'],"index.php/facilities/json_get_venuetype_store/TOKEN:"+App.TOKEN,{test:false});
     }, 
     
     init:function(facility_id,facility_name)
     {
        var me=this;
        Ext.onReady(function(){me.load_grid(facility_id,facility_name);});  
     },                                   
     load_grid:function(facility_id,facility_name)
     {  
        var me=this;                        
        var config=
        {
            generator       : Math.random(),
            owner           : me.owner,
            
            facility_id     :facility_id,
            pageSize        : 100,
            renderTo        : "venues_list",
            title           : 'Venues for ('+facility_name+')',
            extraParamsMore : {facility_id :facility_id},
            collapsible     : true,
            
            //customized Components
            rowEditable     :true,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false,      
            
            
            venue_type_list         :me.venue_type_list
            
        }
            
        //this.grid=new Ext.spectrumgrids.leagues(config);
        me.grid = Ext.create('Ext.spectrumgrids.venue',config);
        
          
        me.grid.on("collapse",function()
        {
            facilitiesManage.grid.expand();
            
            facilitiesManage.grid.setHeight(App.MAX_GRID_HEIGHT);  
            facilitiesManage.grid.doLayout();              
           // facilitiesManage.grid.doComponentLayout('100%','350px',true);   
        });  
     }  
} 
var facilitiesManage=new facilitiesManageClass();
