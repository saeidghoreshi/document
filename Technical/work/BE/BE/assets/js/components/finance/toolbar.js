var transactionManage   =new transactionManageClass();
var invoiceManage       =new invoiceManageClass();
var payDepositManage    =new payDepositManageClass();
var cardPayment         =new cardPaymentClass();
var chargeItemsManage   =new chargeItemsManageClass();

var incomeStatementManage   = new incomeStatementManageClass();
var balanceSheetManage      = new balanceSheetManageClass();
var paymentListManage       = new paymentListManageClass();
var walletBalanceManage     = new walletBalanceManageClass();
var resetManage             = new resetManageClass();

var paymentTestCaseManage   =new paymentTestCaseManageClass(); 

invoiceManage.initInvoices();


toolbar = 
{
    items:
    [
        {
            text: 'Transaction History', iconCls: 'chart_organisation',
            handler:function()
            {
                transactionManage.init();
            }
        }
        ,{
            text    : 'Invoice',
            iconCls : 'chart_organisation',
            handler:function()
            {   
                invoiceManage.initInvoices();
            }
                /*,{   
                    text: 'Pending Invoices',iconCls:'application_error',handler:function()
                    {
                        invoiceManage.initPendingInvoices();
                    }
                } */
                /*,{   
                    text: 'Draft Invoices',iconCls:'application_side_boxes',handler:function()
                    {
                        invoiceManage.initDraftInvoices();
                    }
                } */
            

        }
        /*,{
            text: 'Pay Deposit', iconCls: 'chart_organisation',
            handler:function()
            {
                payDepositManage.init();
            }
        }  */
        ,{
            text: 'Payment History', iconCls: 'money', 
            handler:function()
            {
                paymentListManage.init(); 
            }
        }
        ,{
            text: 'Report', iconCls: 'page_white_acrobat',
            menu: 
            [
                {   
                    text: 'Balance Sheet', iconCls: 'page_white_database', 
                    handler:function()
                    {
                        balanceSheetManage.init(); 
                    }
                },
                {   
                    text: 'Income Statement', iconCls: 'page_white_cup', 
                    handler:function()
                    {
                        incomeStatementManage.init(); 
                    }
                }       
            ]           
        }   
        ,{
            text: 'Setup', iconCls: 'database',
            menu: 
            [
                {   
                    text: 'Charge Items', iconCls: 'database', 
                    handler:function()
                    {
                        chargeItemsManage.init(); 
                    }
                },
                /*{   
                    text: 'SetUp required Deposit', iconCls: 'money', tooltip:"Setup which Entity needs to pay Deposit to which Entity ",
                    handler:function()
                    {
                        payDepositManage.init(); 
                    }
                },*/       
                {   
                    text: 'Add Currency', iconCls: 'money_add', tooltip:"Choose the entity and Add Wallet Money",
                    handler:function()
                    {
                        walletBalanceManage.init(); 
                    }
                }
            ]           
        }
        /*,
        {
            text: 'Reset', iconCls: 'lightning', tooltip: 'Reset All transactions and Invoices and Items Not Drafts',
            handler:function()
            {
                resetManage.init(); 
            }
        }
        ,
        {
            text: 'Payment Test', iconCls: '',
            handler:function()
            {
                paymentTestCaseManage.initPaymentTestCase();
            }
        }
        ,{
            text    : 'Cancellation Test Case', iconCls: '',
            handler:function()
            {   
                paymentTestCaseManage.initCancellationTestCase();                
            }
        }*/
    ]
}; 
