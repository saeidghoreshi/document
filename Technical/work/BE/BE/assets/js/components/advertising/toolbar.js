var campaignManage=new campaignManageClass(); 
var bannerManage=null;
var clientManage=null;

toolbar = 
{
    items:[
    {
        text: 'Manage Advertisement Campaigns', iconCls: 'fugue_chart',
        handler:function()
        {
            campaignManage=new campaignManageClass(); 
        }
    }
    ,{
        text: 'Manage Advertisement Units', iconCls: 'layout', 
        handler:function()                                                     
        {
            bannerManage=new bannerManageClass();
        }
    }
    ,{
        text: 'Clients', iconCls: 'fugue_user-business-boss', 
        handler:function()
        {
            clientManage=new clientManageClass();
        }
    }
    ]
}; 
