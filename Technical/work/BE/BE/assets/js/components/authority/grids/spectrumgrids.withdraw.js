Ext.define('Ext.spectrumgrids.withdraw',    
{
        extend: 'Ext.spectrumgrids', 
        initComponent: function(config) 
        {       
            this.callParent(arguments);
        },
        
         override_edit          :null
        ,override_selectiochange:null
        ,override_itemdblclick  :null
        ,override_collapse      :null
        ,override_expand        :null
        
        ,config                 :null
        ,constructor : function(config) 
        {                         
            var me=this;
           
            config.columns  =
            [    
            
                {
                    text    : 'Withdrawal Details',
                    columns :
                    [
                        {
                            text        : "Bank Account Owner"
                            ,dataIndex  : 'bankaccount_name'
                            ,width      :100
                            
                        }
                        ,{
                            text        : "Bank Name"
                            ,dataIndex  : 'bankname'
                            ,width      :100
                        }      
                        ,{
                            text        : "Institution"
                            ,dataIndex  : 'institution'
                            ,width      :100
                        }
                        ,{
                            text        : "Transit Code"
                            ,dataIndex  : 'transit'
                            ,width      :100
                        }
                        ,{
                            text        : "Account Number"
                            ,dataIndex  : 'account'
                            ,width      :100
                        }
                        //
                        ,{
                            text        : "Amount"
                            ,dataIndex  : 'amount'
                            ,width      :100
                        }     
                        ,{
                            text        : "Fees"
                            ,dataIndex  : 'fees'
                            ,width      :100
                        }
                        ,{
                            text        : "Total"
                            ,dataIndex  : 'total'
                            ,width      :100
                        }    
                    ]
                }
                ,{
                    text        : "Motion Status"
                    ,xtype      :'templatecolumn'
                    ,tpl        :'<div style=text-align:center;>{[values.motion_status_id == "1" ? "<img src='+config.imageBaseUrl+'hourglass.png>" :(values.motion_status_id  == "2" ? "<img src='+config.imageBaseUrl+'tick.png>" : (values.motion_status_id == "3" ? "<img src='+config.imageBaseUrl+'delete.png>" :"<img src='+config.imageBaseUrl+'lightning.png>"))]}'      
                    ,width      : 70
                }
                ,{
                    text        : "EFT Status"
                
                   ,xtype      :'templatecolumn'
                   ,tpl        :'<div style=text-align:center;>{[values.eft_status_id == "1"||values.eft_status_id == "2"  ? "<img src='+config.imageBaseUrl+'hourglass.png>" :(values.eft_status_id  == "5" ? "<img src='+config.imageBaseUrl+'tick.png>" : (values.eft_status_id == "3" ? "<img src='+config.imageBaseUrl+'delete.png>" :"<img src='+config.imageBaseUrl+'lightning.png>"))]}'      
                    ,width      : 70
                }
                ,{
                    text        : "Action"
                    ,dataIndex  : 'action'
                    ,width      :100
                }
                ,{
                    text        : "Created by"
                    ,dataIndex  : 'created_by_name'
                    ,flex       :1     
                }                               
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            var motionStatus    =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_motion_status/"+App.TOKEN+'/',{test:true});
            var eftStatus       =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_eft_status/"+App.TOKEN+'/',{test:true});
            config.topItems.push
            (
                //  FILTER BASED ON MOTION-RULE STATUS
                {  
                    xtype       : 'combo',
                    labelStyle  : 'font-weight:bold;padding:0',
                    name        : 'motion_status_id',
                    emptyText   : '',
                    fieldLabel  : 'Motion Status',
                    labelWidth  : 100,    
                    forceSelection: false,
                    editable    : false,
                    displayField: 'type_name',
                    valueField  : 'type_id',
                    queryMode   : 'local',     
                    allowBlank  : false,
                    store       : motionStatus,
                    listeners   :
                    {
                        change:function(_this,selected_id)
                        {             
                            me.getStore().proxy.extraParams.motion_status_id=selected_id;
                            me.getStore().load();
                        }                        
                        
                    }    
                },
                //  FILTER BASED ON EFT STATUS
                {  
                    xtype       : 'combo',
                    labelStyle  : 'font-weight:bold;padding:0',
                    name        : 'eft_status_id',
                    emptyText   : '',
                    fieldLabel  : 'EFT Status',
                    labelWidth  : 100,    
                    forceSelection: false,
                    editable    : false,
                    displayField: 'type_name',
                    valueField  : 'type_id',
                    queryMode   : 'local',     
                    allowBlank  : false,
                    store       : eftStatus,
                    margins     : '0 0 0 10',
                    listeners   :
                    {
                        change:function(_this,selected_id)
                        {             
                            me.getStore().proxy.extraParams.eft_status_id=selected_id;
                            me.getStore().load();
                        }                        
                        
                    }    
                }
            );
            
            config.bottomRItems.push
            (    
            );
            
            config.bottomLItems.push
            (
                 //make motion request
                 {
                        iconCls : 'application_form_add',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Apply for a motion',
                        handler : function()
                        {   
                            //GET EFT RATE AND AMOUTN
                            Ext.Ajax.request
                            (
                            {
                                    url     : 'index.php/finance/json_get_eftfees/TOKEN:'+App.TOKEN,
                                    params  : {test:'test'},
                                    success : function(response)
                                    {
                                        var result      =YAHOO.lang.JSON.parse(response.responseText);
                                        
                                        var eft_rate    =result.result.fee_rate;
                                        var eft_amount  =result.result.fee_amount;
                                        
                                        
                                        //Active BankAccount grid
                                        var gen=Math.random();
                                        var grid_conf=
                                        {
                                            url         : "/index.php/finance/json_get_bankaccounts/TOKEN:"+App.TOKEN,
                                            generator   : gen,
                                            title       : 'My Bankaccounts',
                                            collapsible : false,
                                            owner       : me,
                                            extraParamsMore :{enabled:true},              
                                            columns     :
                                            [  
                                                {
                                                        text    : 'Bank Account Details',
                                                        columns :
                                                        [
                                                            {
                                                                text        : "Bank Account Owner"
                                                                ,dataIndex  : 'bankaccount_name'
                                                                ,width      :100
                                                            }
                                                            ,{
                                                                text        : "Bank Name"
                                                                ,dataIndex  : 'bankname'
                                                                ,width      :100
                                                            }      
                                                            ,{
                                                                text        : "Institution"
                                                                ,dataIndex  : 'institution'
                                                                ,width      :100
                                                            }
                                                            ,{
                                                                text        : "Transit Code"
                                                                ,dataIndex  : 'transit'
                                                                ,width      :100
                                                            }
                                                            ,{
                                                                text        : "Account Number"
                                                                ,dataIndex  : 'account'
                                                                ,width      :100
                                                            }
                                                        ]
                                                }  
                                            ],
                                            //customized Components
                                            rowEditable     :false,
                                            groupable       :false,
                                            bottomPaginator :true,
                                            searchBar       :true    
                                        } 
                                        var bankaccount_grid=new Ext.spectrumgrids.bankaccount(grid_conf);

                                        var _config=
                                        {
                                            lwidth  :"30%",
                                            rwidth  :"70%",
                                            width   : 800,
                                            height  : 375,
                                            collapsible :false,
                                            bottomItems:   
                                            [
                                                '->'
                                                ,{   
                                                     xtype   :"button",
                                                     text    :"Withdraw Fund",
                                                     iconCls :'table_save',
                                                     pressed :true,
                                                     tooltip :'Withdraw Fund',
                                                     handler :function()
                                                     {                     
                                                         if(bankaccount_grid.getSelectionModel().getSelection().length==0)
                                                         {
                                                             Ext.MessageBox.alert({title:"Cannot Withdraw",msg:"Please select a bankaccount", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                             return;
                                                         }
                                                         var record=bankaccount_grid.getSelectionModel().getSelection()[0].data;
                                                         
                                                         if (form.getForm().isValid()) 
                                                         {   
                                                             var enteredAmount      =Ext.getCmp("sa_eft_amount").getValue();
                                                             var post={}
                                                             post                   =form.getForm().getValues();
                                                             post["bankaccount_id"] =record.bankaccount_id;
                                                             post["total"]          =Ext.getCmp("sa_eft_total").getValue();
                                                             
                                                             //calculate Fees
                                                             var finalValue     =parseFloat(new Number(parseFloat(new Number(eft_rate).toFixed(4)*parseFloat(enteredAmount))+parseFloat(new Number(eft_amount).toFixed(2))).toFixed(2));
                                                             post["fees"]   =new Number(finalValue).toFixed(2);
                                                             
                                                             
                                                             form.getForm().submit(
                                                             {
                                                                 url     : 'index.php/finance/json_new_motion_withdraw/TOKEN:'+App.TOKEN,
                                                                 waitMsg : 'Processing ...',
                                                                 params  : post,
                                                                 success : function(form, action)
                                                                 {
                                                                     var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                                     if(parseInt(res.result)>=1)
                                                                     {
                                                                         win.Hide();
                                                                         Ext.MessageBox.alert({title:"Status",msg:"Request Sent Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                         me.getStore().load();
                                                                     }           
                                                                     if(res.result=="-1")
                                                                     {
                                                                         win.Hide();
                                                                         Ext.MessageBox.alert({title:"Cannot Withdraw",msg:"Still one pending Withdraw Motion exists in system", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                                     } 
                                                                 },
                                                                 failure: function(form, action){App.error.xhr(action.response);}
                                                             }); 
                                                         
                                                         }   
                                                     }                          
                                                }
                                            ]
                                        }
                                        var form=financeForms.form_withdraw_motion(eft_rate,eft_amount);
                                        var final_form=new Ext.spectrumforms.mixer(_config,[form,bankaccount_grid],['form','grid']);
                                        
                                        var win_cnf=
                                        {
                                                title       : 'Request New Withdrawal Motion',
                                                final_form  : final_form
                                        }
                                        var win=new Ext.spectrumwindow.authority(win_cnf);
                                        win.show();
                                        
                                        Ext.getCmp("bankaccount_del_motion").setVisible(false);
                                        Ext.getCmp("bankaccount_add_motion").setVisible(false);
                                    }
                            });
                        }
                    }
            );   
            
            
            if(config.fields==null)     config.fields       = ['withdraw_id','bankaccount_id','bankaccount_id','bankaccount_name','bankname','institution','transit','account','entity_id','amount','description','fees','total','created_by','created_by_name','created_on','motion_status_id','motion_status_name','action','eft_status_id'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = "/index.php/finance/json_get_withdraws/"+App.TOKEN;
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
             
            if(config.groupField==null) config.groupField   = "";
            
            this.override_edit          =config.override_edit;
            this.override_selectiochange=config.override_selectionchange;
            this.override_itemdblclick  =config.override_itemdblclick;
            this.override_collapse      =config.override_collapse;
            this.override_expand        =config.override_expand;
            
            this.config=config;
            this.callParent(arguments); 
        }
});
