var bankaccountManage=new bankaccountManageClass();//why are these in the toolbar and not the controller?
var saAssignmentManage=new saAssignmentManageClass();
var ruleManage=new ruleManageClass();
var withdrawManage=new withdrawManageClass();
var motionManage=new motionManageClass();
var resetSAManage=new resetSAManageClass();


            
toolbar = 
{
    items:[
    {
        text: 'Rule'
        , iconCls: 'star',
        handler:function()
        {
            ruleManage.init();
        }
    }
    ,{
        text: 'Bank Account'
        , iconCls: 'database_table',
        handler:function()
        { 
            bankaccountManage.init();
        }
    }
    ,{
        text: 'Assignment'
        , iconCls: 'user_add',
        handler:function()
        {
            saAssignmentManage.init();
        }
    }
    ,{
        text: 'Withdrawal'
        , iconCls: 'money_delete',
        handler:function()
        {
            withdrawManage.init();
        }
    } 
    ,{
        text: 'Pending Motions'
        , iconCls: 'hourglass', 
        handler:function()
        {
            motionManage.init(); 
        }
    }  
    /*              
    ,{
        text: 'Reset'
        , iconCls: 'lightning', 
        handler:function()
        {
            resetSAManage.init(); 
        }
    }
    
 	,{
        text: 'Test Beanstream CREATE'
        ,iconCls: 'lightning'
        //,hidden:false// change to TRUE when this goes live
        ,handler:function()
        { 
        	var h=function(o)
        	{ 
				alert(o.responseText);
        	}
 			YAHOO.util.Connect.asyncRequest('POST','index.php/finance/test_beanstream_chron/'+App.TOKEN,
 			{success:h,failure:h},'type=c');
 			
 			           
        }
	 }
	 ,{
        text: 'Test Beanstream UPLOAD'
        ,iconCls: 'lightning'
        ,hidden:true// change to TRUE when this goes live
        ,handler:function()
        { 
        	var h=function(o)
        	{ 
				alert(o.responseText);
        	}
 			YAHOO.util.Connect.asyncRequest('POST','index.php/finance/test_beanstream_chron/'+App.TOKEN,
 			{success:h,failure:h},'type=u');
 			
 			           
        }
	 }
	 ,{
        text: 'Test Beanstream Report'
        ,iconCls: 'lightning'
        //,hidden:false// change to TRUE when this goes live
        ,handler:function()
        {
        	var h=function(o)
        	{ 
				alert(o.responseText);
        	}
 			YAHOO.util.Connect.asyncRequest('POST','index.php/finance/test_beanstream_chron/'+App.TOKEN,
 			{success:h,failure:h},'type=r');
 			
 			           
        }
	 }
	 ,{
        text: 'Test EFT'
        ,iconCls: 'lightning'
        //,hidden:false// change to TRUE when this goes live
        ,handler:function()
        {
        	var h=function(o)
        	{ 
        		var w = window.open("",'','target=_blank');
        		w.document.write(o.responseText);
        	}
 			YAHOO.util.Connect.asyncRequest('POST','index.php/finance/test_eft_data/'+App.TOKEN,
 			{success:h,failure:h});
 			
 			           
        }
	 }*/
	 //get_eft
    ]
}; 


//default to SA as first section showing
saAssignmentManage.init();
