if(typeof App != 'undefined' && !App.dom.definedExt('Ext.spectrumforms'))//very important workaround for IE and sometimes chrome
{
    Ext.define('Ext.spectrumforms',
    {                            
        extend  : 'Ext.form.Panel', 
        alias   : 'widget.spectrumforms', 
        
        initComponent: function(config) 
        {
            this.callParent(arguments);
        }, 
        constructor     : function(config)
        {    
        	if(Ext.getCmp(config.id))Ext.getCmp(config.id).destroy();
        	
        	if(config.centerAll===true)
        	{
                config.height='100%';
        		config.width='100%';
        		config.layout={type:'vbox',align:'stretch'};
        	}
 			config.resizable =false;
 			if(!config.bodyStyle)config.bodyStyle='';//keep input style string and just append
            config.bodyStyle   += 'padding: 5px; background-color: white; ';//border:0;
			
			if(!config.force_border)config.bodyStyle+='border:0';
            if(config.bottomItems!=null)
            {
            	if(!config.dockedItems)config.dockedItems=[];
                config.dockedItems.push(
                {
                    xtype   : "toolbar",
                    dock    : "bottom",
                    items   : config.bottomItems
                });          
			}
            //Adding CSS Effects to DockedItems buttons of window
            //for each toolbar of type 'bottom', set up the borders a specific way
            var i,j;
			if(config.dockedItems)
			for(i in config.dockedItems)if(config.dockedItems[i] && config.dockedItems[i].dock=='bottom')
            {
				//set the style of the toolbar(s)
				//do not override entire style object
				if(!config.dockedItems[i].style)config.dockedItems[i].style={};
				//just lock these specific ones
				delete config.dockedItems[i].style.border;//override other settings, if border:0 or false was passed in
				/*config.dockedItems[i].style['border-left-width']   = '0px';
				config.dockedItems[i].style['border-right-width']  = '0px';
				config.dockedItems[i].style['border-bottom-width'] = '0px';
				config.dockedItems[i].style['border-top-width']    = '1px';*/
                
				for(j in config.dockedItems[i].items)if(config.dockedItems[i].items[j])
				{

					
					
					for(j in config.dockedItems[i].items)if(config.dockedItems[i].items[j])
					{
						if(typeof config.dockedItems[i].items[j].text !='undefined' 
						 && config.dockedItems[i].items[j].text.toUpperCase() == 'SAVE')
						 {
					 		 config.dockedItems[i].items[j].text='Save';
					 		 config.dockedItems[i].items[j].pressed=false;
							 config.dockedItems[i].items[j].cls='x-btn-default-small';
							 config.dockedItems[i].items[j].iconCls='disk';
						 }
					 		
					}
 
					//allow creator to skip the border format: useful if not in a window for example
					if(config.force_border) continue;
					
					//set the style of the toolbar(s)
					//do not override entire style object
					if(!config.dockedItems[i].style)config.dockedItems[i].style={};
					//just lock these specific ones
					
					delete config.dockedItems[i].style.border;//override other settings, if border:0 or false was passed in
					config.dockedItems[i].style['border-left-width']   = '0px';
					config.dockedItems[i].style['border-right-width']  = '0px';
					config.dockedItems[i].style['border-bottom-width'] = '0px';
					config.dockedItems[i].style['border-top-width']    = '1px';
				}
			
			}
 
 
            this.callParent(arguments); 
        }
    });  
}                        


if(typeof App == 'undefined')//For Global website
{
    Ext.define('Ext.spectrumforms',
    {                            
        extend: 'Ext.form.Panel', 
        initComponent: function(config) 
        {
            this.callParent(arguments);
        }, 
        constructor     : function(config)
        {   
            if(config.bottomItems!=null)
                config.dockedItems  =
                {
                    xtype   : "toolbar",
                    dock    : "bottom",
                    items   : config.bottomItems
                }          
            this.callParent(arguments); 
        }
    });      
}