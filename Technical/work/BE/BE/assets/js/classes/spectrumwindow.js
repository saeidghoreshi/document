
//the MAIN windows base class.  All windows should extend this.  

if(!App.dom.definedExt('Ext.spectrumwindow')){//workaround for IE and sometimes chrome
Ext.define('Ext.spectrumwindow',
{
    extend: 'Ext.window.Window', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    config:null,
    constructor     : function(config) 
    {   
        if(Ext.getCmp(config.id))Ext.getCmp(config.id).destroy();//make sure id is unique
        //these params we CAN override
        if(config.closable==null   || typeof config.closable      == 'undefined') config.closable=true;
	    if(config.modal==null      || typeof config.modal         == 'undefined') config.modal=true;
	    if(config.draggable==null  || typeof config.draggable     == 'undefined') config.draggable=true;
	    if(config.plain==null      || typeof config.plain         == 'undefined') config.plain=true;
	    if(config.headerPosition==null||typeof config.headerPosition== 'undefined') config.headerPosition= 'top';
	    if(config.layout==null     ||typeof config.layout        == 'undefined') config.layout='fit';
	    if(config.frame==null     ||typeof config.frame          == 'undefined') config.frame=false;
	    if(config.resizable==null     ||typeof config.resizable  == 'undefined') config.resizable=false;
	    
	    
	    //these we CANNOT override
	    config.bodyStyle   = ' background-color: white ; border:0;';//cannot override this one

        config.draggable        =true;
        
        config.frame            =false;
        
        
        //if it was passed using final form style
        if(config.final_form)
        { 
	        config.items            =[config.final_form];
	        
	        config.width            =parseInt(config.final_form.width)+10;
	        config.height           =parseInt(config.final_form.height)+32;
	        
	        config.closeAction      ='destroy';
        }
        else
        {  //otherwise config.items was used
            
            //SB: user person form components need this 
			config.closeAction      ='hide';
        }
                
        this.config=config;                           
        this.callParent(arguments); 
    },
    
    Hide:function()
    {
        this.hide();  
        if(this.config.final_form)          
            this.config.final_form.destroy();    
    }
        
});
}

