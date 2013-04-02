//Controller Payment Test Case
var paymentTestCaseManageClass= function(){this.construct();};
paymentTestCaseManageClass.prototype=
{
     construct:function()
     {
         var me=this;
     }, 
     init:function()
     {
        var me=this;                                    
     }, 
                                       
     initPaymentTestCase:function()
     {  
         
        var me=this;                  
        Ext.onReady(function()
        {
            var formPaymentTestCaseConfig=
            {
                width   :600,
                height  :500
            }
            var formPaymentTestCaseContent=
            '<iframe frameborder=0 src="https://cc.servilliansolutionsinc.com/endeavor/views/finance/finance.cc.TestCase.php?'+
                       'invoice_id='           +"Test"
                       +'&currency_type_id='   +"1"
                       +'&currency_type_name=' +"CAD"
                       +'&_amount='            +'41'
                       +'&master_entity_id='   +"Test"
                       +'&charge_type_id='     +"1"
                       +'&slave_entity_id='    +"Test"
                       
                       +'&changable_amt='      +"true"
                       +'&_random='            +Math.random()  
                       +'" width="570px" height="500px" >'
            +'</iframe>';
                       
            var formPaymentTestCase         =financeForms.form_cc_payment(formPaymentTestCaseContent);
            var formPaymentTestCaseFinal    =new Ext.spectrumforms.mixer(formPaymentTestCaseConfig,[formPaymentTestCase],['form']);
            var formPaymentTestCaseWinConfig=
            {
                title       : 'Credit Card Payment Test Case',
                final_form  : formPaymentTestCaseFinal
            }
            var formPaymentTestCaseWin   =new Ext.spectrumwindow.finance(formPaymentTestCaseWinConfig);
            formPaymentTestCaseWin.show();
        });  
     },
     initCancellationTestCase:function()
     {  
        var me=this;                  
        Ext.onReady(function()
        {
            var me=this;
            var formCancellationTestCaseConfig=
            {
                width       :600,
                height      :100,
                bottomItems :   
                [
                    '->',
                    //Cancel Payment
                    {
                        iconCls : 'delete',
                        xtype   : 'button',
                        text    : '',
                        pressed : true,
                        tooltip : 'Cancel Payment',
                        handler : function()
                        {
                             var payment    =Ext.getCmp("payment").getValue();
                             
                             Ext.MessageBox.confirm('Payment Cancellation Action', "Are you sure ?", function(answer)
                             {     
                                    if(answer=="yes")
                                    {
                                         var post={}                 
                                         var paymentSplitted    =payment.split(',');
                                         post["txrefnum"]       =paymentSplitted[0];
                                         post["orderId"]        =paymentSplitted[1];
                                         post["amount"]         =paymentSplitted[2];
                                               
                                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                         Ext.Ajax.request(
                                         {
                                             url     : 'index.php/finance/json_cancel_EXT_payment_TEST/TOKEN:'+App.TOKEN, 
                                             params  : post,
                                             success : function(response)
                                             {
                                                    box.hide();
                                                    var res=YAHOO.lang.JSON.parse(response.responseText);
                                                    
                                                    alert(YAHOO.lang.dump(res.result));
                                             }
                                         });                                            
                                    }
                             });  
                        }
                    }
                ]
            } 
                       
                 
            var paymentListStore                    =new simpleStoreClass().make(['details','description'],"index.php/finance/json_get_paymentListStore/TOKEN:"+App.TOKEN+'/',{test:true});
            
            var formCancellationTestCase            =financeForms.formCancellationTestCase(paymentListStore);
            var formCancellationTestCaseFinal       =new Ext.spectrumforms.mixer(formCancellationTestCaseConfig,[formCancellationTestCase],['form']);
            var formCancellationTestCaseWinConfig   =
            {
                title       : 'Cancellation Test Case',
                final_form  : formCancellationTestCaseFinal
            }
            var formCancellationTestCaseWin   =new Ext.spectrumwindow.finance(formCancellationTestCaseWinConfig);
            formCancellationTestCaseWin.show();
        });  
     }
     
}         