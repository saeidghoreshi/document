var maintenanceManageClass= function(){this.construct();};
maintenanceManageClass.prototype=
{
     treeview                           :null,
     construct:function()
     {                    
         var me=this;     
     }, 
     getSysMenuList:function()
     {          
        var me  =   this;
        var config=
        {
                generator       : Math.random(),
                renderTo        : "container-main",
                title           : 'System Menu Hirarchy',
                
                collapsible     : false,
                //customized Components
                searchBar       : false
        }
        me.treeview = Ext.create('Ext.spectrumtreeviews.sysmenu',config);
     }   
} 
