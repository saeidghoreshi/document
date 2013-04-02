/**
* 
* 
* 
* this is depreciated but left intact. use spectrumwindow.js
* 
* this simply extends the class in the file above without touching anything at all.
* exceptt hat it sets closeaction to 'hide' , which again should not do anythning
* 
*/




if(!App.dom.definedExt('Ext.spectrumwindows')){//workaround for IE and sometimes chrome
Ext.define('Ext.spectrumwindows',
{
	//SB: created this when the other class did not exist
    extend: 'Ext.spectrumwindow', //now I extend it so it is the real base class
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {    
    	if(!config.width) config.width=300;
		if(!config.height)config.height=300;
  		config.closeAction='hide';
		
    	/*
    	//default values.  check if null or undefined before we overwrite input values
        if(config.closable==null   || typeof config.closable      == 'undefined') config.closable=true;
        if(config.closeAction==null|| typeof config.closeAction   == 'undefined') config.closeAction='hide';
        if(config.modal==null      || typeof config.modal         == 'undefined') config.modal=true;
        if(config.draggable==null  || typeof config.draggable     == 'undefined') config.draggable=true;
        if(config.plain==null      || typeof config.plain         == 'undefined') config.plain=true;
        if(config.headerPosition==null||typeof config.headerPosition== 'undefined') config.headerPosition= 'top';
        if(config.layout==null     ||typeof config.layout        == 'undefined') config.layout='fit';
        if(config.frame==null     ||typeof config.frame          == 'undefined') config.frame=false;
        if(config.resizable==null     ||typeof config.resizable  == 'undefined') config.resizable=false;
        config.bodyStyle   = ' background-color: #FFFFFF ; border:0;';
 
        //parameter final_form may not exist. but if it is, pass it to items
        if(config.final_form)
        {
			config.width            =parseInt(config.final_form.width)+12;
            config.height           =parseInt(config.final_form.height)+35;
            config.items=config.final_form;
        } 
        
		//a default height and width.  this should be passed in every time, however

		this.config=config;
		
		
		if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}//avoid duplicate ids
		
		*/
        this.callParent(arguments); 
        
    }
 
});}
