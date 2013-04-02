var bfw = 'Spectrum.windows.browsers';
if(!App.dom.definedExt(bfw)){
Ext.define(bfw,
{
	extend: 'Ext.spectrumwindows', 
	initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config)
    {  
    	if(!config.id)  config.id = 'browsers_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.title) config.title= "Internet Explorer Detection";
		config.closable = false;
		config.width  = 400;
		config.height = 340

		config.items = 
		[
			{
				html:"<p style='padding: 15px;'>We've detected that you are using Internet Explorer.  "
					+"While Spectrum works on Internet Explorer 9, "
					+"Internet Explorer is not the optimal browser for Spectrum. "
					+"For the optimal Spectrum Experience, download Firefox of Google Chrome "
					+"by clicking the respective logo below. If you wish to continue with "
					+"Internet Explorer, please click 'Continue Anyway'</p> "
					+"<br/>"
					+"<table width='100%' ><tr>"
						+"<td align='center'><a href='http://www.google.com/chrome'>"
							+"<img src='/assets/images/browsers/chrome.png'  width='64px' /> "
							+"<p>Download Chrome</p>"
						+"</a></td>" 
						+"<td align='center'><a href='http://www.mozilla.org/en-US/firefox/new'>"
							+"<img src='/assets/images/browsers/firefox.png' width='64px' />"
							+"<p>Download Firefox</p>"
						+"</a></td>"
					+"</tr></table>"
					+"<br/>"
					+"<div style='clear:both;'></div>"
					+"<br/>"
					+"<div align='center'>"
						+"<a id='btnRegRed' href='javascript:App.login.show();' >Continue Anyway</a>"
					+"</div>"
			}
		
		];
		
		this.callParent(arguments);
	}
});}
