var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
    var maintenanceManage   =   new maintenanceManageClass(); 
    maintenanceManage.getSysMenuList();
box.hide();

toolbar = 
{
    items:
    [
        {
            text: 'System Menu List', iconCls: 'chart_organisation',
            handler:function()
            {
                var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                    maintenanceManage.getSysMenuList();
                box.hide();
            }
        }
    ]
}; 
