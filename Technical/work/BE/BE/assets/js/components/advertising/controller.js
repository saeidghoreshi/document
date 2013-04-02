//********************************************************************** Banners
var bannerManageClass= function(){this.construct();};
bannerManageClass.prototype=
{
     grid                               :null,       
     
     client_store                       :null,
     client_with_all_store              :null,
     size_store                         :null,
     
     construct:function()
     {
         var me=this;
         
         this.client_store          =new simpleStoreClass().make(['co_org_id','co_org_name'],"index.php/websites/json_get_clients_2/TOKEN:"+App.TOKEN       ,{with_allclient:false});
         this.client_with_all_store =new simpleStoreClass().make(['co_org_id','co_org_name'],"index.php/websites/json_get_clients_2/TOKEN:"+App.TOKEN       ,{with_allclient:true});
         this.size_store            =new simpleStoreClass().make(['size_id','size_name']    ,"index.php/websites/json_get_sizes_2/TOKEN:"+App.TOKEN         ,{});
        
         me.init(); 
     }, 
     init:function()
     {
        var me=this;
        
        Ext.onReady(function()
        {
            var _generator=Math.random(); 
            
            var config=
            {
                generator       : _generator,
                owner           : me,
                
                renderTo        : "advertisement-shared-list",
                title           : 'Advertisements',
                
                collapsible     :true,
                
                client_store            :me.client_store,
                client_with_all_store   :me.client_with_all_store,
                size_store              :me.size_store,
                
                
                //customized Components
                rowEditable     :false,
                groupable       :true,
                bottomPaginator :true,
                searchBar       :true   

            }                                  
            me.grid = Ext.create('Ext.spectrumgrids.banner',config);
            
        
            me.grid.on("expand",function()
            {
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();	
            });           
 
        });  
     }
} 
//**********************************************************************Campaign
var campaignManageClass= function(){this.construct();};
campaignManageClass.prototype=
{
     
    
     client_store                       :null,
     client_with_all_store              :null,
     client_store_specific              :null,
     size_store                         :null,
     
     treeview                           :null,
     construct:function()
     {      
         
         var me=this;
         
         this.client_store          =new simpleStoreClass().make(['co_org_id','co_org_name'],"index.php/websites/json_get_clients_2/TOKEN:"+App.TOKEN+'/'                       ,{with_allclient:true});
         this.client_store_specific =new simpleStoreClass().make(['co_org_id','co_org_name'],"index.php/websites/json_get_clients_assignedto_campaigns/TOKEN:"+App.TOKEN+'/'    ,{with_allclient:true,campaign_id:0});
         this.client_with_all_store =new simpleStoreClass().make(['co_org_id','co_org_name'],"index.php/websites/json_get_clients_2/TOKEN:"+App.TOKEN+'/'                       ,{with_allclient:false});
         this.size_store            =new simpleStoreClass().make(['size_id','size_name']    ,"index.php/websites/json_get_sizes_2/TOKEN:"+App.TOKEN+'/'                         ,{});
         
         me.init();          
     }, 
     init:function()
     {          
        var me=this;

        var gen=Math.random(); 
        var config=
        {
                generator       : gen,
                renderTo        : "advertisement-shared-list",
                title           : 'Campaigns',
                
                collapsible     : true,
                
                client_store            :me.client_store,
                client_store_specific   :me.client_store_specific,
                client_with_all_store   :me.client_with_all_store,
                size_store              :me.size_store,
                
                
                //customized Components
                searchBar       : true
        }
        me.treeview = Ext.create('Ext.spectrumtreeviews.campaign',config);
       
        me.treeview.on("expand",function()
        {
			this.setHeight(App.MAX_GRID_HEIGHT);
			this.doLayout();	
        });        
    
     }   
     
} 
//**********************************************************************Client
var clientManageClass= function(){this.construct();};
clientManageClass.prototype=
{
     grid               :null,       
     
     construct:function()
     {
         var me=this;
         me.init(); 
     }, 
     init:function()
     {
        var me=this;
 
        var _generator=Math.random(); 
        
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            renderTo        : "advertisement-shared-list",
            title           : 'Clients',
            extraParamsMore : {},
            collapsible     :true,
            
            //customized Components
            rowEditable     :false,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true
            //Function appendable or overridble
      
            
        }
                   
        me.grid = Ext.create('Ext.spectrumgrids.client',config);
        me.grid.on("expand",function()
	    {
			this.setHeight(App.MAX_GRID_HEIGHT);
			this.doLayout();	
	    });    

        
     }
} 

 

 
