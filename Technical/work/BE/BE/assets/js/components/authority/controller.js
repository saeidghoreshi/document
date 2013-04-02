//****************************************************************************** Bankaccount
var bankaccountManageClass= function(){this.construct();};
bankaccountManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
         
     }, 
    
     init:function()
     {
        var me=this;                                    
       // Ext.onReady(function(){
        	me.load_grid();
       // });  
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
            renderTo        : "manage-list2",
            title           : 'Bank Accounts',
            extraParamsMore : {},
            collapsible     :false,
            
            //customized Components
            rowEditable     :false,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false 
            
            
        }
        me.grid = Ext.create('Ext.spectrumgrids.bankaccount',config);
        me.grid.show();
 
        /*
        me.grid.on("edit",function(e)
        {
            //e.record.data//e.record.data[e.field];
        }); 
        me.grid.on("expand",function()
        {                                    
            
        }); 
        me.grid.on('itemmouseenter', function(view, record, HTMLElement , index, e, Object ) 
        {                                                                                                                     
       
        }); */
        /*me.grid.getView().on('render', function(view) {});  */ 
     }
}                                                        
//****************************************************************************** Motion
var motionManageClass= function(){this.construct();};
motionManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
         
     }, 
    
     init:function()                                                                                    
     {
        var me=this;                                    
         
        me.load_grid();
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            renderTo        : "manage-list2",
            title           : 'Motion List',
            extraParamsMore : {},
            collapsible     :true,
            imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
            url             : "/index.php/finance/json_get_motions/"+App.TOKEN+"?status_type_name=Pending",
            groupField      : "motion_type_name",
            //customized Components
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false
            
            
        }
        me.grid = Ext.create('Ext.spectrumgrids.motion',config);
 
     }
}                                                        
//****************************************************************************** Rule
var ruleManageClass= function(){this.construct();};
ruleManageClass.prototype=
{
     grid               :null,       
     rule_type_store    :null,
     construct:function()
     {
         var me=this;
         me.rule_type_store =new simpleStoreClass().make(['rule_type_id','rule_type_name'],"index.php/finance/json_get_rules_2/TOKEN:"+App.TOKEN+'/',{test:true});        
     }, 
    
     init:function()
     {
        var me=this;                                    
        me.load_grid();//Ext.onReady(function(){});  
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
            renderTo        : "manage-list2",
            title           : 'Rule',
            extraParamsMore : {},
            collapsible     :true,
     
            rule_type_store :me.rule_type_store,       
            //customized Components
            rowEditable     :false,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false  
            
            
        }
        me.grid = Ext.create('Ext.spectrumgrids.rule',config);
 
     }
}                                                        
//****************************************************************************** SA ASSIGNMENT
var saAssignmentManageClass= function(){this.construct();};
saAssignmentManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
         
     }, 
    
     init:function()
     {
         
        var me=this;                                    
        me.load_grid();//Ext.onReady(function(){me.load_grid();});  
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
            renderTo        : "manage-list2",
            title           : 'Signing Authorities (Un)Assignment',
            extraParamsMore : {},
            collapsible     :true,
            
            //customized Components
            rowEditable     :false,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false  
            
            
        }
        me.grid = Ext.create('Ext.spectrumgrids.sa_assignment',config);
 
     }
}                     
//****************************************************************************** WITHDRAW
var withdrawManageClass= function(){this.construct();};
withdrawManageClass.prototype=
{
     grid               :null,       
     construct:function()
     {
         var me=this;
         
     }, 
    
     init:function()
     {
        var me=this;                                    
        me.load_grid();//Ext.onReady(function(){me.load_grid();});  
     },                                   
     load_grid:function()
     {  
        var me=this;                  
        var _generator=Math.random(); 
         
        var config=
        {
            generator       : _generator,
            owner           : me,
            imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
            renderTo        : "manage-list2",
            title           : 'Withdraw',
            extraParamsMore : {},
            collapsible     :true,
            
            //customized Components
            rowEditable     :false,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true,    
            //Function appendable or overridble
            
            override_edit           :false,
            override_itemdblclick   :false,
            override_selectionchange:false,
            override_expand         :false,
            override_collapse       :false   
            
            
        }
        
        me.grid = Ext.create('Ext.spectrumgrids.withdraw',config);
 
     }
} 
//****************************************************************************** Reset SA
var resetSAManageClass= function(){this.construct();};
resetSAManageClass.prototype=
{
     construct:function()
     {
         var me=this;
     },              
     init:function()
     {
        var me=this;                                    
        //Ext.onReady(function()
       // {
             var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
             Ext.Ajax.request(
             {
                 url     : 'index.php/finance/json_reset_sa/TOKEN:'+App.TOKEN, 
                 params  : {test:true},
                 success : function(response)
                 {
                      box.hide();
                      var res=YAHOO.lang.JSON.parse(response.responseText);
                      if(res.result=="1")           
                          Ext.MessageBox.alert({title:"Status",msg:"Reset Done Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                 }
             });    
       // });  
     }
} 