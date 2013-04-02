var PressedClass                ="";//"x-btn-default-toolbar-small-pressed btn_shinysilver";
var unPressedClass              ="";//"x-btn-default-toolbar-small-pressed btn_inactive";

var transactionManage       = new transactionManageClass();
var paymentListManage       = new paymentListManageClass();

transactionManage.init();
                      
toolbar = 
{
    items:
    [
        {
            text    : 'Transaction History', 
            iconCls : 'chart_organisation',
            id      : "transaction_history_btn",
            cls     : PressedClass,
            handler :function()
            {
                transactionManage.init();
                //Activate Invoices Button              
                var thisButton          ='transaction_history_btn';
                Ext.getCmp(thisButton).removeCls(unPressedClass); 
                Ext.getCmp(thisButton).addClass(PressedClass); 
                
                var btn,otherBtn=['payment_history_btn']
                for(var i in otherBtn) if(otherBtn[i])
                {
                	btn=Ext.getCmp(otherBtn[i]);
                	//SB:fix for IE9, was getting some undefiend elements here 
                	if(btn){
	                    btn.removeCls(PressedClass);     
	                    btn.addClass(unPressedClass);    
					}
                }
                //Activate Invoices Button 
            }
        }
        ,{
            text    : 'Payment History',
            iconCls : 'money', 
            id      : "payment_history_btn",
            cls     : unPressedClass,
            handler :function()
            {
                paymentListManage.init(); 
                //Activate Invoices Button              
                var thisButton          ='payment_history_btn';
                Ext.getCmp(thisButton).removeCls(unPressedClass); 
                Ext.getCmp(thisButton).addClass(PressedClass); 
                
                var btn,otherBtn=['transaction_history_btn']
                for(var i=0;i<otherBtn.length;i++)
                {
                	btn=Ext.getCmp(otherBtn[i]);
                	//SB:fix for IE9, was getting some undefiend elements here 
                	if(btn){
	                    Ext.getCmp(otherBtn[i]).removeCls(PressedClass);     
	                    Ext.getCmp(otherBtn[i]).addClass(unPressedClass);   
					} 
                }
                //Activate Invoices Button 
            }
        }
    ]
}; 
