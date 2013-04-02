var PressedClass                ="";//"x-btn-default-toolbar-small-pressed btn_shinysilver";
var unPressedClass              ="";//"x-btn-default-toolbar-small-pressed btn_inactive";

var walletBalanceManage     = new walletBalanceManageClass();
var resetManage             = new resetManageClass();
var paymentTestCaseManage   = new paymentTestCaseManageClass(); 
var chargeItemsManage       = new chargeItemsManageClass();
                          
toolbar = 
{                                                                    
    items:
    [
        {   
            id      : 'invoice_items_btn',
            text    : 'Charge Items',
            iconCls : 'database', 
            cls     : PressedClass,
            handler :function()
            {
                chargeItemsManage.init(); 
                handleButtons('invoice_items_btn',['add_currency_btn','reset_btn','payment_test_btn','cancel_test_btn']);
            }
        },
        {   
            id      : 'add_currency_btn',
            text    : 'Add Currency',
            iconCls : 'money_add', 
            tooltip :"Choose the entity and Add Wallet Money",
            cls     : unPressedClass,
            handler :function()
            {
                walletBalanceManage.init(); 
                handleButtons('add_currency_btn',['invoice_items_btn','reset_btn','payment_test_btn','cancel_test_btn']);
            }
        }/*
        //DO NOT SEND THESE LIVE!!!!!!
        ,
        {
            id      : 'reset_btn',
            text    : 'Reset', 
            iconCls : 'lightning', 
            tooltip : 'Reset All transactions and Invoices and Items Not Drafts',
            cls     : unPressedClass,
            handler :function()
            {
                resetManage.init();
                handleButtons('reset_btn',['invoice_items_btn','add_currency_btn','payment_test_btn','cancel_test_btn']); 
            }
        }
        ,
        {
            id      : 'payment_test_btn',
            text    : 'Payment Test', 
            iconCls : '',
            cls     : unPressedClass,
            handler :function()
            {
                paymentTestCaseManage.initPaymentTestCase();
                handleButtons('payment_test_btn',['invoice_items_btn','add_currency_btn','reset_btn','cancel_test_btn']);
            }
        }
        ,{
            id      : 'cancel_test_btn',
            text    : 'Cancellation Test Case', 
            iconCls : '',
            cls     : unPressedClass,
            handler :function()
            {   
                paymentTestCaseManage.initCancellationTestCase();                
                handleButtons('cancel_test_btn',['invoice_items_btn','add_currency_btn','reset_btn','payment_test_btn']);
            }
        }*/
    ]
}; 
function handleButtons(thisButton,otherBtn)
{
    //Activate Invoices Button              
    var i,btn;
    Ext.getCmp(thisButton).removeCls(unPressedClass); 
    Ext.getCmp(thisButton).addClass(PressedClass); 
    
    for( i in otherBtn)
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