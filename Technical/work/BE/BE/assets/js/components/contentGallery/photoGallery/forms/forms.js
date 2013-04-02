var photoGalleryFormsClass= function(){this.construct();};
photoGalleryFormsClass.prototype=
{
    construct:function()
    {
                             
    },       
    
    uploadphotoDetails_north:function()
    {   
        
         var me=this;
         
         var form=new Ext.form.Panel(
         {   
                            autoWidth   : true,
                            height      : 400,
                            fileUpload  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px;  background-color: #DFE8F6',
                            border      : false,
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {       
                                        xtype       : 'filefield',    
                                        id          : 'photogallery_upload',             
                                        name        : 'photogallery_upload',
                                        labelStyle  : 'font-weight:bold;padding:0',
                                        emptyText   : 'Browse Photo',
                                        fieldLabel  : '',
                                        labelWidth  : 150,          
                                        allowBlank  : false,
                                        flex        :1
                                }
                            ]
         });                                                          
         return  form;
    },
    
    uploadphotoDetails_west:function()
    {   
         var me=this;
         var form=new Ext.form.Panel(
         {   
                            autoWidth   : true,
                            height      : 400,
                            
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {
                                        xtype       : 'displayfield',
                                        id          : 'photogallery_terms',
                                        name        : 'photogallery_terms',
                                        fieldLabel  : 'Terms & Conditions',
                                        labelWidth  : 150,
                                        autoWidth   : true,
                                        height      : 120,
                                        allowBlank  : false,
                                        value       : 'terms and Condition terms and Condition terms and Condition terms and Condition terms and Condition terms and Condition terms and Condition terms and Condition terms and Condition terms and Condition '
                                },
                                {
                                        xtype       : 'checkbox',
                                        id          : 'photogallery_acceptterms',
                                        name        : 'photogallery_acceptterms',
                                        fieldLabel  : 'Accept',
                                        labelWidth  : 150,
                                        autoWidth   : true,
                                        allowBlank  : false
                                }
                            ]
         });                                                          
         return  form;
    },
    
    uploadphotoDetails_east:function()
    {   
        
         var me=this;
         
         var form=new Ext.form.Panel(
         {   
                            autoWidth   : true,
                            height      : 400,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {
                                        xtype       : 'textfield',
                                        id          : 'photogallery_name',
                                        name        : 'photogallery_name',
                                        fieldLabel  : 'Title',
                                        labelWidth  : 150,
                                        autoWidth   : true,
                                        allowBlank  : false
                                },
                                {
                                        xtype       : 'textareafield',
                                        id          : 'photogallery_description',
                                        name        : 'photogallery_description',
                                        fieldLabel  : 'Description',
                                        labelWidth  : 150,
                                        autoWidth   : true,
                                        height      : 180,
                                        allowBlank  : false
                                }
                            ]
         });                                                          
         return  form;
    },
    
    rightPanelForm:function()
    {   
        
         var me=this;
         
         var form=new Ext.form.Panel(
         {   
                            autoWidth   : true,
                            height      : 400,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {
                                        xtype       : 'displayfield',
                                        id          : 'itemDetails',
                                        name        : 'itemDetails',
                                        autoWidth   : true,
                                        padding     : 5
                                        
                                }
                            ]
         });                                                          
         return  form;
    },
    
    playPauseChoiceForm:function()
    {   
        
         var me=this;
         
         var form=new Ext.form.Panel(
         {   
                            autoWidth   : true,
                            height      : 400,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {
                                        xtype       : 'button',
                                        iconCls     : 'play',
                                        id          : 'btn_play',
                                        name        : 'btn_play',
                                        text        : 'Play',
                                        width       : 100,
                                        margin      : 15,
                                        listeners   :
                                        {
                                            click:function()
                                            {
                                                var playActiveClass     ="x-btn-default-toolbar-small-pressed play_active";
                                                var pauseActiveClass    ="x-btn-default-toolbar-small-pressed pause_active";
                                                
                                                var playEl  =Ext.getCmp("btn_play");
                                                var pauseEl =Ext.getCmp("btn_pause");
                                                                                                                            
                                                pauseEl.removeCls(pauseActiveClass);
                                                playEl.addCls(playActiveClass);
                                            }
                                        }
                                },
                                {
                                        xtype       : 'button',
                                        iconCls     : 'pause',
                                        id          : 'btn_pause',
                                        name        : 'btn_pause',
                                        text        : 'Pause',
                                        width       : 100,
                                        margin      : 15,
                                        listeners   :
                                        {
                                            click:function()
                                            {    
                                                var playActiveClass     ="x-btn-default-toolbar-small-pressed play_active";
                                                var pauseActiveClass    ="x-btn-default-toolbar-small-pressed pause_active";
                                                
                                                var playEl  =Ext.getCmp("btn_play");
                                                var pauseEl =Ext.getCmp("btn_pause");
                                                
                                                
                                                playEl.removeCls(playActiveClass);
                                                pauseEl.addCls(pauseActiveClass);
                                            }    
                                        }
                                }
                            ]
         });                                                          
         return  form;
    },
    
    accPanelForm:function(accTypeStore)
    {   
        
         var me=this;
         
         var form=new Ext.form.Panel(
         {   
                            autoWidth   : true,
                            height      : 400,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {  
                                     xtype       : 'combo',
                                     labelStyle  : 'font-weight:bold;padding:0',
                                     name        : 'acc_types',
                                     emptyText   : '',
                                     fieldLabel  : '',
                                     labelWidth  : 150,    
                                     forceSelection: false,
                                     editable    : false,
                                     displayField: 'type_name',
                                     valueField  : 'type_id',
                                     queryMode   : 'local',     
                                     allowBlank  : false,
                                     store       : accTypeStore
                                     
                                 }
                            ]
         });                                                          
         return  form;
    },
    
    
}
var photoGalleryForms=new photoGalleryFormsClass();
