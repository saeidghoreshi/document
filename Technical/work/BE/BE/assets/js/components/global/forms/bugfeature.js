

var bff = 'Spectrum.forms.bugfeature';
if(!App.dom.definedExt(bff)){
Ext.define(bff,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },

    constructor     : function(config)
    {  
    	this.window_id = config.window_id;
		var id='c_bugfeatures_form';//+Math.random();
    	if(!config.id){config.id=id};

    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.title)config.title= '';
				
	    config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
 
	    
	    config.defaults= {anchor: '100%'};
	    config.items=
	    [
	        {
	        	xtype: 'combo'
	        	,name : 'is_bug'
	        	,typeAhead: true
	        	,value:'true'//default value
	        	,editable:false
	        	,fieldLabel: 'Submission Type'
	        	,store: [ ['true' ,'I found an error or a problem happened (Bug)']
                		 ,['false','I thought of something that Spectrum should have (Feature)']
                		 ]
	        }
	        ,{
	        	xtype: 'textfield',
	        	fieldLabel:'Your Email',
	        	allowBlank: true,
	        	name:'email'				
	        }
	        
	        ,{
	        	xtype: 'textarea'
	        	,name : 'context'
	        	,height:190
	        	,labelAlign: 'top'//only for this one
	        	,id:'id_context'
	        	,fieldLabel: 'Context (Please supply as much detail as possible)'
	        	, allowBlank: false
	        }
	        ,{    
                                   
                xtype       : 'filefield',
                hidden:true,//TODO: implement this on server side, then remove hidden
                name        : 'file_upload',
                emptyText   : 'Upload Screenshot',
                fieldLabel  : '',
                emptyText:'For bugs, it is helpful to send a screenshot of the problem',
                allowBlank  : true
            }
 
	    ];
	    config.bottomItems=
	    [
	    	'->'
	    	,
 			{text   : 'Save',scope:this,id:'id_bf_save_btn',handler: function() 
	        {
	            //disable button for double sumbit, reset it later
	            Ext.getCmp('id_bf_save_btn').setDisabled(true);
	            var form = this.getForm();// 
	            if (!form.isValid()) 
	            {
	            	Ext.getCmp('id_bf_save_btn').setDisabled(false);
	            	return;
	            }
	                                  
	            var data=[];
	            //post all data
	            this.empty=false;
	            Ext.iterate(form.getValues(), function(key, value) 
	            {
					//encode string to escape bad characters
					if(key=='context')
					{
						if(value.split(' ').join('')=='')
						{
							this.empty=true;
							//Ext.getCmp('id_'+key).setValue('');
						}//intervals api views spaces as empty string andd will error out
					} 
					value=escape(value);
	                data.push(key+"="+value);
	                
	            }, this);//must pass scope
	            if(this.empty==true)
	            {
	                //form.reset();
	                form.isValid();//re validate and bug out, since we already replaced spaces with empty string
	                
	            	Ext.getCmp('id_bf_save_btn').setDisabled(false);
					return;
	            }
	            var post = data.join("&");
	             
	            var url='index.php/dispatch/post_bugfeature/'+App.TOKEN;
	            var callback={scope:this,success:function(o)
	            {
	                var r=o.responseText;
					var w=Ext.getCmp(this.window_id);
					if(w) w.hide();
					
					//r is from teh view now
					Ext.MessageBox.show({title:'Your issue was submitted',msg:r,buttons:Ext.MessageBox.OK});
					form.reset();
	            	Ext.getCmp('id_bf_save_btn').setDisabled(false);

	            }};

	            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
	        }
	        }        
	    ];
        this.callParent(arguments);    	    	
	}

});}

