var lgf="Spectrum.forms.link";
if(!App.dom.definedExt(lgf)){
Ext.define(lgf,
{
	extend: 'Ext.spectrumforms', 
    initComponent: function(config) {this.callParent(arguments);},
    
    window_id:null,           
    
    constructor     : function(config)
    { 
    	config.centerAll=true;
    	this.url=config.url;
    	this.window_id=config.window_id;
    	
		this.record=config.record;
    	
    	if(!config.id) config.id=Math.random();//??
    	
    	var store_paged_articles =new simpleStoreClass().make(['id','name']
    					,"index.php/websites/json_get_paged_articles/TOKEN:"+App.TOKEN+'/',{});

    	var collection=Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data : [
            {"id":"1", "name":"Url"},
            {"id":"2", "name":"Page"},
            
        ]});
    	config=         
    	{   
            //autowidth   : true,
           //autoHeight  : true,
            style       : {border:0},
          //  layout      : {type: 'vbox',align: 'stretch'},
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
                    xtype: 'fieldset',
                    title: '',
                    collapsible: false,
                    style:{border:0},
                    border      : false,
                    defaults: 
                    {
                        labelWidth: 89,
                        anchor: '100%',
                        layout: {
                            type: 'hbox',
                            defaultMargins: {top: 5, right: 5, bottom: 5, left: 5}
                        }    
                    },
                    items:
                    [
                         //title
                         {                        
                            xtype       : 'textfield',
                            labelStyle  : 'font-weight:bold;padding:0',
                            name        : 'title',
                            emptyText   : '',
                            fieldLabel  : 'Title',
                            labelWidth  : 150,          
                            flex        : 1,
                            allowBlank  : false
                        }

                        //type (page/url) and related component
                        ,{
                            xtype: 'fieldcontainer',
                            fieldLabel: '',
                            combineErrors: true,
                            msgTarget: 'side',          
                            labelStyle: 'font-weight:bold;padding:0',
                            layout: 'hbox',        
                            fieldDefaults: {labelAlign: 'top'}
                            ,items:
                            [
                                {   
                                    id          : 'form_link_details_type_id',                    
                                    xtype       : 'combo',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'type_id',
                                    emptyText   : '',
                                    fieldLabel  : 'Link Type',
                                    labelWidth  : 150,    
                                    forceSelection: false,
                                    editable    : false,
                                    displayField: 'name',
                                    valueField  : 'id',
                                    queryMode   : 'local',     
                                    flex        : 1,
                                    allowBlank  : false,
                                    triggerAction: 'all',
									value       :'1',//default / initial value
                                    store       : collection,
                                    
                                    listeners: {
                                        change: {
                                            fn: function(conf,selected_id)
                                            {
                                                if(selected_id==2/*Page*/)
                                                {
                                                    Ext.getCmp("form_link_details_article_id").show();
                                                    Ext.getCmp("form_link_details_article_id").setDisabled(false);
                                                    Ext.getCmp("form_link_details_url").hide();
                                                    Ext.getCmp("form_link_details_url").setDisabled(true);
                                                }
                                                else
                                                {
                                                    Ext.getCmp("form_link_details_article_id").hide();
                                                    Ext.getCmp("form_link_details_article_id").setDisabled(true);    
                                                    Ext.getCmp("form_link_details_url").show();    
                                                    Ext.getCmp("form_link_details_url").setDisabled(false);    
                                                }
                                                
                                            },
                                            scope: this, 
                                            buffer:500                  
                                        }
                                    }
                                } 
                                
                                ,{              
                                    id          : 'form_link_details_url',          
                                    xtype       : 'textfield',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'url',
                                    emptyText   : '',
                                    vtype       : "url",
                                    fieldLabel  : 'URL',
                                    labelWidth  : 150,          
                                    flex        : 1,
                                    allowBlank  : true,
                                    value       : "http://",//default with this prefix given
                                    margins     : '0 0 0 10'
                                }
                            ]
                       }
                       
                       //last field is articles: may be hidden or not
                       ,{  
                            id          : 'form_link_details_article_id',                                          
                            xtype       : 'combo',
                            labelStyle  : 'font-weight:bold;padding:0',
                            name        : 'article_id',
                            value       :this.article_id,
                            emptyText   : '',
                            fieldLabel  : 'Paged Articles (Announcements)',
                            labelWidth  : 150,    
                            forceSelection: false,
                            editable    : false,
                            displayField: 'name',
                            valueField  : 'id',
                            queryMode   : 'local',     
                            flex        : 1,
                            allowBlank  : true,
                            store       : store_paged_articles,
                            margins     : '0 0 0 10',
                            hidden      :true
                        }
                    ]
                }
                
            ]
            
            ,buttons:
            [
            {

                iconCls :'disk',
                text    :"Save",
	            cls     :'x-btn-default-small',
                width   :70,
                scope   :this,
                handler:function()
                {

                	var form=this.getForm();
                    var values=form.getValues();
                    if(!values["article_id"] && values['type_id']=='2')//only do this if paged is selected!!!
                    {
                        Ext.MessageBox.alert({title:"Error",msg:"Please select a \"Paged\" article (Announcement)", 
                        					icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    if (!form.isValid()) {return;}
                	
                    var url=this.url;//default is save, but can overwrite with edit
                    if(!url) url='index.php/websites/json_new_link/'+App.TOKEN;
                    form.submit({
                        url: url,
                      //  waitMsg: 'Processing...',
                       // params: {article_id:values["article_id"]},
                       scope:this,
                        success: function(form, action)
                        {
                            var res=YAHOO.lang.JSON.parse(action.response.responseText);
                            if(res.result=="1")
                            {             
                            	//.log(this.window_id);
                            	var win=Ext.getCmp(this.window_id);          
                                if(win)win.hide(); 
                                
                            }                        
                        },
                        failure: function(form, action){App.error.xhr(action.response);}
                    });
                               
                }                          
                }
            ]
         }
    	
 
    	this.callParent(arguments);    
    	 //load record
    	if(this.record)
    	{
			store_paged_articles.on('load',function()//but only after combo box data is ready
    		{ 
    			var gen=Math.random();
    			//model for articles does not exist, so make it up
    			 Ext.define('LR'+gen, {extend: 'Ext.data.Model',fields:
				 	[{type: 'string'},{type: 'string'},{type: 'string'},{type: 'string'},{type: 'string'},{type: 'string'}]});
				 	
				 this.loadRecord(Ext.ModelManager.create(
				{
					   'title'          : this.record.title
					  ,'type_id'        : this.record.type_id
					  ,'url'            : this.record.url
					  ,'article_id'     : this.record.article_id
				}, "LR"+gen)); 	
    		},this);	
    	 
 
    	}
	}
	
	
});}
