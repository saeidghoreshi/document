if(!App.dom.definedExt('Ext.spectrumforms.mixer')){//do nto define it twice eh
Ext.define('Ext.spectrumforms.mixer',
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
       
    constructor     : function(config,form_list,type_list)
    {                            
        if(!config.force_border)
        {
	        config.border       = false;//if using force_border, then skip this, we are doing it a different way
       		 config.frame        = false;

		}
 		config.layout='border';
        if(config.gen == null)
            config.gen=Math.random();
 
 
 		
 		//was an error in seasons if form_list is undefined or null
 		if(!form_list){form_list=config.items;config.items=null}
 		 

        if(form_list.length==3)
        {   
            config.items    =
            [
                {
                    id          : 'mixer_north'+config.gen,
                    region      : 'north',
                    title       : '',
                    split       : true,
                    width       : "100%",
                    collapsible : false,
                    animCollapse: true,
                    layout      : 'fit',
                    items       : form_list[0],
                    bodyStyle : 'border:0px; padding: 0px; '   ,  
                    //bodyStyle   :((type_list[0]=='grid')?'padding: 0px; background-color: white;border:0px' : 'padding: 0px; background-color: white;border:1px solid #99BCE8;'),
                    border      : false
                },  
                {
                    id          : 'mixer_center'+config.gen,
                    region      : 'center', 
                    layout      : 'fit',
                    width       : "100%",
                    autoHeight  : false,
                    bodyStyle : 'border:0px; padding: 0px; '   ,  
                    //bodyStyle   :((type_list[1]=='grid')?'padding: 0px; background-color: white;border:0px' : 'padding: 0px; background-color: white;border:1px solid #99BCE8;'),
                    items       : form_list[1] ,
                    
                    border:false
                },  
                {
                    id          : 'mixer_south'+config.gen,
                    region      : 'south', 
                    layout      : 'fit',
                    width       : "100%",
                    autoHeight  : true,
                    bodyStyle : 'border:0px; padding: 0px; '   ,  
                    //bodyStyle   :((type_list[2]=='grid')?'padding: 0px; background-color: white;border:0px' : 'padding: 0px; background-color: white;border:1px solid #99BCE8;'),
                    items       : form_list[2] ,
                    border:false
                }
            ];    
        }                           
                        
        if(form_list.length==2 && config.formStyle == null)
        {            
            config.items    =
            [
                {
                    id          : 'mixer_left'+config.gen,
                    region      : 'west',
                    title       : '',
                    split       : true,
                    width       : ((config.rwidth == null)?"100%":config.rwidth),
                    collapsible : (config.collapsible==null)?true:false,
                    minWidth    : 270,
                    animCollapse: true,
                    layout      : 'fit',
                    bodyStyle : 'border:0px; padding: 0px; '   ,  
                    items       : form_list[0]     ,                                                          
                    //bodyStyle   :((type_list[0]=='grid')?'padding: 0px; background-color: white;border:0px' : 'padding: 0px; background-color: white;border:1px solid #99BCE8;'),
                    border      : false
                },  
                {
                    id          : 'mixer_right'+config.gen,
                    region      : 'center', 
                    layout      : 'fit',
                    width       : ((config.lwidth == null)?"100%":config.lwidth),
                    autoHeight  : true,   
                    bodyStyle : 'border:0px; padding: 0px; '   ,                                                       
                    //bodyStyle   :((type_list[1]=='grid')?'padding: 0px; background-color: white;border:0px' : 'padding: 0px; background-color: white;border:1px solid #99BCE8;'),
                    items       : form_list[1] 
                }
            ];    
        }
        if(form_list.length==2 && config.formStyle == 'vertical2')
        {            
            config.items    =
            [
                {
                    id          : 'mixer_left'+config.gen,
                    region      : 'north',
                    title       : '',
                    split       : true,
                    width       : "100%",
                    collapsible : (config.collapsible==null)?true:false,
                    minWidth    : 270,
                    animCollapse: true,
                    layout      : 'fit',
                    bodyStyle   : 'border:0px; padding: 0px; '   ,  
                    items       : form_list[0]     ,                                                          
                    //bodyStyle   :((type_list[0]=='grid')?'padding: 0px; background-color: white;border:0px' : 'padding: 0px; background-color: white;border:1px solid #99BCE8;'),
                    border      : false
                },  
                {
                    id          : 'mixer_right'+config.gen,
                    region      : 'center', 
                    layout      : 'fit',
                    width       : "100%",
                    autoHeight  : true,   
                    bodyStyle   : 'border:0px; padding: 0px; '   ,                                                       
                    //bodyStyle   :((type_list[1]=='grid')?'padding: 0px; background-color: white;border:0px' : 'padding: 0px; background-color: white;border:1px solid #99BCE8;'),
                    items       : form_list[1] 
                }
            ];    
        }
        if(form_list.length==1)
        {
            config.items    =
            [
                {
                    region      : 'center',
                    layout      : 'fit',
                    autoWidth   : true,
                    autoHeight  :true,
                    border:false,
                    bodyStyle : 'border:0px; padding: 0px; '   , 
                //    bodyStyle   :((type_list[0]=='grid')?'padding: 0px; background-color: white;border:0px' : 'padding: 0px; background-color: white;border:1px solid #99BCE8;'),//type_list does not exist in some website publishing forms
                //also styles should come from froms base class NOT here
                    items       : form_list[0] 
                }
            ];    
        }    
        
        this.callParent(arguments); 
    }
});

}
