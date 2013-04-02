Ext.define('Ext.spectrumdataviews.template',    
{
        extend: 'Ext.spectrumdataviews', 
        initComponent: function(config) 
        {       
            this.callParent(arguments);
        }           
        ,config                 :null
        ,constructor : function(config) 
        {                                                                      
            var me=this;
            config.title='Website Design';
            if(config.fields==null)     config.fields       = ['id','template_id','template_title','website_url','template_logo','created_by','created_by_name','modified_by','modified_by_name','created_on_display','modified_on_display','isfree','isavailable','isactive'];
            if(config.url==null)        config.url          = "void";
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 300;
            if(config.tpl==null)        config.tpl          = Ext.create('Ext.XTemplate',
                                                                    '<tpl for=".">',
                                                                        '<div class="phone {[values.isactive == null ? "" : "star"]} ">',
                                                                            (!Ext.isIE6? '<img width="74" height="74" src="{[values.template_logo]}" />' :
                                                                             '<div style="width:84px;height:84px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'{[values.template_logo]}\',sizingMethod=\'scale\')"></div>'),    
                                                                             '<strong>{template_title}</strong>',
                                                                             '<strong>{created_by_name}</strong>',
                                                                             
                                                                        '</div>',
                                                                    '</tpl>'
                                                              );
            config.multiSelect=false;                                                          
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
 
            config.bottomRItems.push
            (
                {
                    id      : "spectrumdataview_template_purchase_comp",
                    xtype   : 'button',
                    iconCls : 'cart_add',
                    text    : '',
                    tooltip : 'Purchase',
                    hidden:true,//hidden for 2.1
                    handler: function()
                    {  
                        Ext.MessageBox.alert({title:"Status",msg:"Purchase not enabled", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});                                                                                        
                    }   
                }
                ,{
                    id      : "spectrumdataview_template_preview_comp",
                    xtype   : 'button',
                    iconCls : 'fugue_ application-search-result',
                    text    : '',
                    tooltip : 'Preview Selected Template',
                    disabled:true,
                    handler: function()
                    { 
                        var sel=config.dataview.getSelectionModel().getSelection();
                        if(!sel.length){return;}
                        var record=sel[0]; 
                         
                        //window.open('http://'+record.website_url+'/?template_id='+record.template_id); 
                        window.open('http://'+record.get('website_url')+'/?theme='+record.get('template_title')); 
                    }   
                }
                ,{
                    id      : "spectrumdataview_template_activate_comp",
                    xtype   : 'button',
                    iconCls : 'application_start',
                    text    : '',
                    tooltip : 'Activate Selected Template',
                    disabled:true,
                    handler: function()
                    { 
                        if(config.dataview.getSelectionModel().getSelection().length==0)
                        {
                            //Ext.MessageBox.alert({title:"Error",msg:"Please select a template", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        var record=config.dataview.getSelectionModel().getSelection()[0].data;
                        if(record.isavailable=='f')
                        {
                        	//THIS IS NOT AN ERROR
                            Ext.MessageBox.alert({title:"Camnot Activate",msg:"This template is not available", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        if(record.isfree=='f')
                        {
                        	//THIS IS NOT AN ERROR
                            Ext.MessageBox.alert({title:"Cannot Activate",msg:"Need to be purchased", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        
                        var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                        Ext.Ajax.request(
                        {
                            url     : 'index.php/websites/json_activate_template/TOKEN:'+App.TOKEN+'/',
                            params  : record,
                            success : function(o)
                            {   
                                box.hide();
                                config.dataview.getStore().load();
                            },
                        failure: App.error.xhr
                        });
                    }   
                }
            );
            if(config.clickselectEvent==null)
            config.clickselectEvent=
            {                 
                        
                selectionchange: function(dataview, selections)
                {                                
                    if(selections.length==0)return;
                    var record=selections[0].data;   
                    Ext.getCmp("spectrumdataview_template_activate_comp").setDisabled(false);
                    Ext.getCmp("spectrumdataview_template_preview_comp").setDisabled(false);
                    Ext.getCmp("spectrumdataview_template_purchase_comp").setDisabled(false);
                },
                itemclick: function(dataview, selections)
                {                                  
                    if(selections.length==0)return;           
                    var record=selections.data;      
                    Ext.getCmp("spectrumdataview_template_activate_comp").setDisabled(false);
                    Ext.getCmp("spectrumdataview_template_preview_comp").setDisabled(false);
                    Ext.getCmp("spectrumdataview_template_purchase_comp").setDisabled(false);
                    
                }
            }  
            
            this.config=config;
            this.callParent(arguments); 
        }
});