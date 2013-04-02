Ext.define('Ext.spectrumdataviews.imagePicker',    
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
            if(config.fields==null)     config.fields       = ['id','template_name','template_title','template_image','created_by','created_by_name','modified_by','modified_by_name','created_on_display','modified_on_display'];
            if(config.url==null)        config.url          = "VOID"
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 300;
            if(config.tpl==null)        config.tpl          = Ext.create('Ext.XTemplate',
                                                                    '<tpl for=".">',
                                                                        '<div class="phone" >',
                                                                            (!Ext.isIE6? '<img width="64" height="64" src="{[values.template_image]}" />' :
                                                                             '<div style="width:74px;height:74px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'{[values.template_image]}\',sizingMethod=\'scale\')"></div>'),
                                                                             
                                                                             '<strong>{template_title}</strong>',
                                                                             '<strong>{created_by_name}</strong>',
                                                                             
                                                                        '</div>',
                                                                    '</tpl>'
                                                              ); 
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            config.topItems.push
            (   
                {
                     id         : "spectrumdataview_template_fileupload_comp",
                     xtype      : 'button',
                     text       : '',
                     iconCls    : 'page_white_get',
                     width      : 30,
                     handler    : config.uploadhandler
                }
                ,{
                     id         : "spectrumdataview_template_file_comp",
                     xtype      : 'filefield',
                     name       : 'newfile',
                     fieldLabel : '',
                     labelWidth : 50,
                     anchor     : '100%',
                     buttonText : 'Select Photo...',
                     flex       :1
                 }                            
            );      
            config.bottomLItems.push
            (
                 {
                     id         :"spectrumdataview_template_url_comp",
                     xtype      : 'textfield',
                     name       : '',
                     fieldLabel : '',
                     allowBlank : true,
                     width      : 450
                 }                            
            );
            config.bottomRItems.push
            (
               
            );
            if(config.clickselectEvent==null)
            config.clickselectEvent=
            {                 
                        selectionchange: function(dataview, selections)
                        {                                
                            if(selections.length==0)return;
                            var record=selections[0].data;   
                            Ext.getCmp("spectrumdataview_template_url_comp").setValue('/'+record.template_image)
                        },
                        itemclick: function(dataview, selections)
                        {                                  
                            if(selections.length==0)return;           
                            var record=selections.data;      
                            Ext.getCmp("spectrumdataview_template_url_comp").setValue('/'+record.template_image)
                        }
            }
            this.config=config;
            this.callParent(arguments); 
        }   
        ,afterRender: function() 
        {  
            var me=this;
            
            this.callParent(arguments);         
        }
});