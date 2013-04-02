OnlineReportManage.init();
                      
toolbar = 
{
    items:
    [
        {
            text    : 'Reports', 
            iconCls : 'chart_organisation',
            id      : "reports_btn",
            handler :function()
            {
                Ext.getDom("manage-list-onlinereport").innerHTML='';  
                OnlineReportManage.init();
            }
        }
    ]
}; 
