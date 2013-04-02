var PressedClass                ="";//"x-btn-default-toolbar-small-pressed btn_shinysilver";
var unPressedClass              ="";//"x-btn-default-toolbar-small-pressed btn_inactive";

var incomeStatementManage   = new incomeStatementManageClass();
var balanceSheetManage      = new balanceSheetManageClass();

incomeStatementManage.init();


toolbar = 
{
    items:
    [
        {   
            id      : 'incomestatement_btn',
            text    : 'Income Statement', 
            iconCls : 'page_white_cup', 
            cls     : PressedClass,
            handler :function()
            {
                incomeStatementManage.init();                
                //Activate Invoices Button              
                var thisButton          ='incomestatement_btn';
                Ext.getCmp(thisButton).removeCls(unPressedClass); 
                Ext.getCmp(thisButton).addClass(PressedClass); 
                
                var otherBtn=['balancesheet_btn']
                for(var i=0;i<otherBtn.length;i++)
                {
                    Ext.getCmp(otherBtn[i]).removeCls(PressedClass);     
                    Ext.getCmp(otherBtn[i]).addClass(unPressedClass);    
                }
                //Activate Invoices Button
            }
        },
        {   
            id      : 'balancesheet_btn',
            text    : 'Balance Sheet',
            iconCls : 'page_white_database', 
            cls     : unPressedClass,
            handler :function()
            {
                balanceSheetManage.init();
                //Activate Invoices Button              
                var thisButton          ='balancesheet_btn';
                Ext.getCmp(thisButton).removeCls(unPressedClass); 
                Ext.getCmp(thisButton).addClass(PressedClass); 
                
                var otherBtn=['incomestatement_btn']
                for(var i in otherBtn)
                {
                    Ext.getCmp(otherBtn[i]).removeCls(PressedClass);     
                    Ext.getCmp(otherBtn[i]).addClass(unPressedClass);    
                }
                //Activate Invoices Button 
            }
        }      
    ]
}; 
