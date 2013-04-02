var articleManage=null;
//var articleManage=new articleManageClass();

var linkManage=null;
var templateManage=null;
var moduleManage=null;
 
Ext.Ajax.request
({
    url: 'index.php/permissions/json_get_active_org_and_type/'+App.TOKEN,
    params: {test:'test'},
    success: function(response)
    { 
        var res=YAHOO.lang.JSON.parse(response.responseText);
        if(res.result.org_type_id!=3)
        {
            Ext.getCmp('modules_tab').hide();
            Ext.getCmp('links_tab').hide();
            Ext.getCmp('templates_tab').hide();   
        }
    }
});
toolbar = 
{
    items:[
    {
        text: 'Website Articles', 
        iconCls: 'fugue_blogs',
        handler:function()
        {
            articleManage=new articleManageClass();
            moduleManage.assets.hide();
        }
    }
    ,{
        id  :'links_tab',
        text: 'Website Links & Navigation', 
        iconCls: 'fugue_chain', 
        handler:function()                                                     
        {
            linkManage=new linkManageClass();
            moduleManage.assets.hide();
        }
    }
    ,{
        id  :'templates_tab',
        text: 'Website Design', 
        iconCls: 'fugue_application-image', 
        handler:function()
        {
            templateManage=new templateManageClass();
            moduleManage.assets.hide();
        }
    }
    ,{
        id  :'modules_tab',
        text: 'Website Add-Ons', 
        iconCls: 'fugue_puzzle', 
        handler:function()
        {
            moduleManage.init();
            moduleManage.assets.hide();
            
        }
    } 
    
    ]
}; 

moduleManage=new moduleManageClass();
moduleManage.assets.grid();
//now default to publishing showing
articleManage=new articleManageClass();