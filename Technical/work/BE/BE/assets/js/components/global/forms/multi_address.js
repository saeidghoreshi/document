//org name
//default if an org has no fancy formvar 
var fnm = 'Spectrum.forms.multi_address';
if(!App.dom.definedExt(fnm)){
Ext.define(fnm,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) {this.callParent(arguments);},
	address_id:-1,
	org_id:0,
    constructor     : function(config)
    {  
    	if(config.org_id)this.org_id=config.org_id;
		var id='c_basicorgs_form';//+Math.random();
    	if(!config.id){config.id=id};
		
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
    	this.id=config.id;
		if(!config.title)config.title= '';
		
		config.width= 300;
		config.height=250;
	    config.fieldDefaults= {labelWidth: 70,autoFitErrors: false};
	    config.labelAlign= 'top';
    	//if(typeof config.hide_upload == 'undefined') {config.hide_upload=false;}
    	
	    config.defaults= {anchor: '100%'};
	    config.items=
	    [
	    	{
	    		xtype:'button',mode:           'local',
				value:          'null',triggerAction:  'all',forceSelection: true,editable:       false,
				fieldLabel:     'View Address',name:           'address_type',

				text:'Select Address',
				displayField:   'name',valueField:     'value',queryMode: 'local',
				id:'combo_addr_select',
				menu:[]
			}
 			,{
	    		xtype:'fieldcontainer',
	    		title:'Address',
	    		id:'container_multi_addr_',
	    		//hidden:config.hide_addr,
			    //collapsible: true,
				items:Ext.create('Spectrum.forms.person_address',
				{ 
					id:'my_multi_address__'
					,address_ptr:'address_id_inner'
				}) 
			}
	    ];
 
        this.callParent(arguments);   
        this.getAddrCombo();
	}
	,getAddrCombo:function()
	{
 		var url='index.php/endeavor/json_org_address_menu/'+App.TOKEN;
 		YAHOO.util.Connect.asyncRequest('GET',url, {scope:this,success:function(o)
 		{
			var addrs = YAHOO.lang.JSON.parse(o.responseText,false);
 
			var itemClick = function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
				this.address_id=id;
        		Ext.getCmp('combo_addr_select').setText(name);
        		Ext.getCmp('address_id_inner' ).setValue(id);

        		this.getAddress();
			};
 			var a_menu = new Array();
			for(i in addrs)if(addrs[i] && addrs[i]['name']) //Always check this for IE
			{
				name = addrs[i]['name'];
				id   = addrs[i]['value'];
 
        		a_menu.push({text:name,value:id,handler:itemClick,scope:this/*,iconCls:icon*/});
			}
			Ext.getCmp('combo_addr_select').menu=Ext.create('Spectrum.btn_menu',{items:a_menu,width:400});

 		}});
	}
	
	,getAddress:function()
	{
 
		var post='address_id='+this.address_id+"&org_id="+this.org_id;
		var url='index.php/endeavor/json_org_single_address/'+App.TOKEN;
 		YAHOO.util.Connect.asyncRequest('POST',url, {scope:this,success:function(o)
 		{
			var r=YAHOO.lang.JSON.parse(o.responseText);
 
			var addr_data = Ext.ModelManager.create(r, 'User');
 			
			//this.loadRecord(addr_data);
			//this.getForm().loadRecord(addr_data);
			Ext.getCmp(this.id).loadRecord(addr_data);
			Ext.getCmp('my_multi_address__').loadRecord(addr_data);
 		}},post);
	}
	 
});}

