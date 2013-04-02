
var bfw = 'Spectrum.windows.login';
if(!App.dom.definedExt(bfw)){
Ext.define(bfw,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    
    constructor: function(config)
    { 
    	//cannot overwrite anything, except we take the items 
    	var f = config.items;
		config = 
		{
			
			title		: 'Spectrum Login',
			id			: 'global_winLogin',
			height		: 400,
			width		: 520,
			closable	: false,
			draggable	: false,
			resizable	: false,
			modal		: true,
			layout		: 'border',
			
			items: 
			[
				{
				    // Login Form
				    border		: 0,
				    id			: 'login_mainform',
				    region		: 'west',
				    width		: 300,
				    height		: 320,
				    split		: false,
				    collapsible	: false,
				    floatable	: false,
				    title		: false,
				    items		: f //the form
				}, 
				{
				    // Spectrum Logo (Top)
				    // Google Ad (Bottom)
				    border		: 0,
				    region		: 'center',
				    width		: 220,
				    height		: 400,
				    id			: 'login_adspace',
				  //  html		: '<center><br/><img src="http://playerspectrum.com/templates/spectrum/images/spectrum_tiny.png" height="36"/></center><br/>'+
				    html		: '<center><br/><img src="assets/images/spectrum.png" height="96"/></center><br/>'//+
				    			  //'<iframe id="loginad" width="220" height="200" src="/index.php/ads/get/200x200" scrolling="no" frameborder="0"></iframe>'
				}
			]
		};


		this.callParent(arguments);

	}
});}

