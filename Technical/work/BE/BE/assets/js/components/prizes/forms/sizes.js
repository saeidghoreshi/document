
var szform='Spectrum.forms.sizes';
if(!App.dom.definedExt(szform)){
Ext.define(szform,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    save:function()
    {
		var form = this.getForm();
	    if (!form.isValid()) {return;}
	                           
	    var data=[];
	    //post all data
	    var valid=true;
	    Ext.iterate(form.getValues(), function(key, value) 
	    {
			if( value.split(' ').join('')==''){valid=false;}
			value=escape(value);
	        data.push(key+"="+value);
	    }, this);
	    if(!valid)
	    {
	        Ext.MessageBox.alert('Error','Enter a valid Name');
	        return;
	    }
	    var post = data.join("&");
 
	    var url='index.php/prize/post_new_size/'+App.TOKEN;
	    var callback={scope:this,failure:App.error.xhr,success:function(o)
	    {
	        var r=o.responseText;
			if(isNaN(r)||r<=0)
			{
				Ext.MessageBox.show({title:'Could not create :',msg:r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
			}
			else
			{
				Ext.MessageBox.show({title:'Success',msg:'Save complete',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK});
				//form.reset();
				//this.up('form').getForm().reset();//do not reset, because for EDIT this makes weird issues
			}
			
	    }};

	    YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
    },
    constructor     : function(config)
    {  
		var id='c_sizesg_form';

    	if(!config.id){config.id=id};
    	if(Ext.getCmp(config.id)){ Ext.getCmp(config.id).destroy(); }
    	 
		var prize_id=-1;
		if(config.prize_id){prize_id=config.prize_id;}
		var size_id=-1;
		if(config.size_id){size_id=config.size_id;}
 
	    config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
	    //config.defaults= {anchor: '100%'};
	    config.items=
	    [
		    {
		    	xtype: 'hidden'
		    	,name : 'size_id'
		    	,value:size_id
		    }//loadRecord will override this value
		    ,{
		    	xtype: 'hidden'
		    	,name : 'prize_id'
		    	,value:prize_id
		    }
 
	        ,{
	        	xtype: 'textfield'
	        	,flex : 1
	        	,name : 'size_name'
	        	,   fieldLabel: 'Name'
	        	,emptyText:'Custom Size'
	        	,value:''
	        	,allowBlank: false
	        }
	        ,{
	        	xtype: 'textfield'
	        	,flex : 1
	        	,name : 'size_abbr'
	        	,   fieldLabel: 'Abbreviation'
	        	,emptyText:'CS'
	        	,value:''
	        	,allowBlank: true
	        }
	    ];
	    config.bottomItems=
	    [
	    	'->',
 			{
 				text   : 'Save'
 				,iconCls:'disk'
 				,scope:this
 				,handler: function() 
		        {
		            this.save();
		        }
	        }        
	    ];
	        
 	
        this.callParent(arguments);    	    	
	}
	
	
	
});}

