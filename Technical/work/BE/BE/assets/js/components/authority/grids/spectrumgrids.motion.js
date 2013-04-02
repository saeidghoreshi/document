Ext.define('Ext.spectrumgrids.motion',    
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
                    text        : "Initiator"
                    ,dataIndex  : 'created_by_name'
                    ,width      :100
                }
                ,{
                    text        : "Initiated On"
                    ,dataIndex  : 'created_on_display'
                    ,width      :100
                }
                ,{
                    text    : 'Vote Details',
                    columns :
                    [
                        {
                            text        : "#Positive Votes Received"
                            ,dataIndex  : 'num_pos_votes_received'
                            ,width      :150
                        }
                        ,{
                            text        : "#Negative Votes Received"
                            ,dataIndex  : 'num_neg_votes_received'
                            ,width      :150
                        }
                        ,{
                            text        : "#Votes Required"
                            ,dataIndex  : 'num_votes_required'
                            ,width      :150
                        }
                    ]
                }
                ,{
                    text        : "Action"
                    ,dataIndex  : 'action'
                    ,flex       :1
                }
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            config.topItems.push
            (
            );       
            config.bottomLItems.push
            (
                
            );       
            config.bottomRItems.push
            (
                 //Make a positive vote
                 {
                        iconCls : 'thumb_up',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'positive vote',
                        handler : function()
                        {
                            if(me.getSelectionModel().getSelection().length==0)
                            {
                            	//this is not an error
                                Ext.MessageBox.alert({title:"Cannot Vote",msg:"No Motion selected", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            var record=me.getSelectionModel().getSelection()[0].data;
                            var post={}
                            post["motion_id"]=record.motion_id;
                            post["vote"]='y';
                            
                            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                            Ext.Ajax.request({
                                url     : 'index.php/finance/json_new_motion_vote/TOKEN:'+App.TOKEN+'/',
                                params  : post,
                                success : function(o)
                                {
                                    box.hide();
                                    var res=YAHOO.lang.JSON.parse(o.responseText);
                                    if(res.result=="1")
                                    {
                                        Ext.MessageBox.alert({title:"Status",msg:"Your vote registerd for selected motion", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});    
                                        me.getStore().load();
                                    }
                                    if(res.result=="-1")
                                    {
                            	//this is not an error
                                        Ext.MessageBox.alert({title:"Cannot Vote",msg:"You already voted for this motion", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    } 
                            },
                            failure: function( action){box.hide();App.error.xhr(action);}
                            });    
                        }
                 }   
                 //Make a negative vote
                 ,{
                        iconCls : 'thumb_down',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Negative vote',
                        handler : function()
                        {
                            if(me.getSelectionModel().getSelection().length==0)
                            {
                            	//this is not an error
                                Ext.MessageBox.alert({title:"Cannot Vote",msg:"No Motion selected", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            var record=me.getSelectionModel().getSelection()[0].data;
                            var post={}
                            post["motion_id"]=record.motion_id;
                            post["vote"]='n';
                            
                            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');        
                            Ext.Ajax.request({
                                url     : 'index.php/finance/json_new_motion_vote/TOKEN:'+App.TOKEN+'/',
                                params  : post,
                                success : function(o)
                                {
                                    box.hide();
                                    var res=YAHOO.lang.JSON.parse(o.responseText);
                                    if(res.result=="1")
                                    {
                                        Ext.MessageBox.alert({title:"Status",msg:"Your vote registerd for selected motion", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});    
                                        me.getStore().load();
                                    }
                                    if(res.result=="-1")
                                    {
                                        Ext.MessageBox.alert({title:"Cannot Vote",msg:"You already voted for this motion", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    }                                                                  
                                    
                            },
                            failure: function( o){box.hide();App.error.xhr(o);}
                            });    
                        }
                 }   
                 
                 //View pending motion record
                 ,{
                        iconCls : 'magnifier',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'View Pending Motion Record',
                        handler : function()
                        {
                            if(me.getSelectionModel().getSelection().length==0)
                            {
                            	//this is not an error
                                Ext.MessageBox.alert({title:"Cannot View",msg:"No Motion selected", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            var record=me.getSelectionModel().getSelection()[0].data;
                            var post={}
                            post["motion_id"]=record.motion_id;
                            
                            var title;    
                            var _generator=Math.random();
                            if(record.motion_type_id==1)//Assignment
                            {   
                                title='(SA Assignment)';
                                var config1=
                                {
                                    generator       : _generator,
                                    owner           : me,
                                    extraParamsMore : {isEnabled:false},
                                    collapsible     :false,
                                    imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
                                    //customized Components
                                    rowEditable     :false,
                                    groupable       :false,
                                    bottomPaginator :true,
                                    searchBar       :true                                  
                                }
                                var g= Ext.create('Ext.spectrumgrids.sa_assignment',config1);                                
                            }
                            if(record.motion_type_id==2)//Withdraw
                            {
                                title='(Withdraw)';
                                var _generator=Math.random();                                
                                var config2=
                                {
                                    generator       : _generator,
                                    owner           : me,
                                    extraParamsMore : {isEnabled:false},
                                    imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
                                    collapsible     :false,
                                    //customized Components
                                    rowEditable     :false,
                                    groupable       :false,
                                    bottomPaginator :true,
                                    searchBar       :true                                       
                                }
                                var g= Ext.create('Ext.spectrumgrids.withdraw',config2);
                            }
                            if(record.motion_type_id==3)//BankAccount
                            {
                                title='(BankAccount)';
                                var config4=
                                {
                                    generator       : _generator,
                                    owner           : me,
                                    imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
                                    extraParamsMore : {isEnabled:false},
                                    collapsible     :false,
                                    //customized Components
                                    rowEditable     :false,
                                    groupable       :false,
                                    bottomPaginator :true,
                                    searchBar       :true                                        
                                }
                                var g= Ext.create('Ext.spectrumgrids.bankaccount',config4);
                            }
                            if(record.motion_type_id==4)//Rule
                            {
                                title='(Rule)';
                                var config3=
                                {
                                    generator       : _generator,
                                    owner           : me,
                                    imageBaseUrl    :'http://endeavor.servilliansolutionsinc.com/global_assets/silk/',
                                    extraParamsMore : {isEnabled:false},
                                    collapsible     :false,
                                    //customized Components
                                    rowEditable     :false,
                                    groupable       :false,
                                    bottomPaginator :true,
                                    searchBar       :true                                       
                                }
                                var g= Ext.create('Ext.spectrumgrids.rule',config3);
                            }
                            
                            var _conf=
                            {
                                    width   : 400,
                                    height  : 275
                            }
                            var final_form=new Ext.spectrumforms.mixer(_conf,[g],['grid']);
                            
                            var win_cnf=
                            {
                                title       : 'Pending Record '+title,
                                final_form  : final_form
                            }
                            var win=new Ext.spectrumwindow.authority(win_cnf);
                            win.show();
                        }
                 }   
               
            );   
            
            
            if(config.fields==null)     config.fields       = ['motion_id','motion_type_id','motion_type_name','motion_status_id','motion_status_name','description','created_by','created_by_name','created_on','created_on_display','num_pos_votes_received','num_neg_votes_received','num_votes_required','action'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = "VOID";
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
        ,afterRender: function() 
        {  
            var me=this;
           /* if(!this.override_edit)
            {   
                this.on("edit",function(e)
                {   
                }); 
                
            }
            if(!this.override_selectionchange)                              
            {   
                this.on("selectionchange",function(sm,records)
                {   
                });
            }
            if(!this.override_collapse)                              
            {   
                this.on("collapse",function(){});
            }
            if(!this.override_expand)
            {   
                this.on("expand",function(){});
            }  */
            this.on('itemmouseenter', function(view, record, HTMLElement , index, e, Object ) 
            {                                                                                                                     
               view.tip = Ext.create('Ext.tip.ToolTip', 
                {
                    target: view.getEl(),
                    delegate: view.itemSelector,
                    trackMouse: true,
                    anchor  :'right',
                    listeners: 
                    {
                            beforeshow: function(tip) 
                            {
                                var record=view.getRecord(tip.triggerElement).data;
                                if(record.description!='')    
                                    tip.update(record.description);
                                else 
                                    return false;
                            }
                    }
                });
            });                                
            this.callParent(arguments);         
        }
});
