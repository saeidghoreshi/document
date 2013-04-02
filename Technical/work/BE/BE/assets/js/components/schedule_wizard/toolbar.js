//toolbar is for WIZARD only
var toolbar =
{
items:
[
	{
		xtype:'button'
		,text:'Global Rules'
		,iconCls:'timeline_marker'
		,disabled:true
		,id:'wiz_tb_btn_rules'
		,handler:function()
		{
 			o_sch_wizard.rules_global.get();//get will get rules with ajax, then display the window form right away on callback
			//o_sch_wizard.rules_global.show();// show windowform
			
			
		}
	}
	,'-'
	,{
		xtype:'button'
		,text:'Timeslots'
		,iconCls:'cog'
		,disabled:true
		,id:'wiz_tb_btn_scheduler'
		,handler:function()
		{

			o_sch_wizard.timeslots.get();
			o_sch_wizard.timeslots.show();
			o_sch_wizard.saves.save();
			
			
		}
	}
	,{
		xtype:'button'
		,text:'Division Matches'
		,iconCls:'table_relationship'
		,disabled:true
		,id:'wiz_tb_btn_matches'
		,handler:function()
		{

    		o_sch_wizard.games.abort();
			o_sch_wizard.matches.get();
			o_sch_wizard.matches.show();
			
			o_sch_wizard.saves.save();

		}
	}
	,'-'
	,{
		xtype:'button'
		,text:'Audit Reports'
		,iconCls:'chart_curve'
		,disabled:true//enabled by controller when reports exist
		,id:'wiz_tb_btn_audit'
		,menu:null//menu loaded by controller
	}
	,"-"
	,{
		xtype:'button'
		,text:'Finalize Schedule'
		,iconCls:'disk'
		,disabled:true
		,id:'wiz_tb_btn_final'
		,handler:function()
		{
    		o_sch_wizard.games.abort();
			o_sch_wizard.finalize.show();
			//o_sch_wizard.saves.save();
		}
	}
 
 
]	
	
};





/**
* all functions / variables with underscore _ to start are private!! do not use them
*/
var messages =// reccomended tags are h3 and p? for inserting content
{
	valid: ['info','warning','error','success'],
	_root : '_bar_root_container',//shouldnt ever need to reference this
	_info:'_bar_msg_info_',
	_warning:'_bar_msg_warn_',
	_error:'_bar_msg_err_',
	_success:'_bar_msg_succ_',
	
	_clickMsg:"Click here to close this message",
	
	/**
	* call this once to set up everything
	*/
	init:function()
	{
		//set up click handlers
		$('.message').click(function()
		{                        
			 var init_h = -$(this).outerHeight();
 			 
              $(this).animate({top:init_h }, 500);
       });   
	  	var start_h; // new initial height
		var len = messages.valid.length;
         for (i=0; i<len; i++)
         {
              start_h = -$('.' + messages.valid[i]).outerHeight(); // fill array

              $('.' + messages.valid[i]).css('top', start_h); //move element outside viewport
         }          
	},
 
 /**
 * private methods
 */
	_show:function(type)
	{
	 	 $('.'+type).animate({top:"80"}, 500); // used to be 0 
	  
	},
 
	_setText:function(type,html)
	{
		html = "<h3>"+html+"</h3>";
		html += "<p>"+messages._clickMsg+"</p>";
		switch(type)
		{
			case messages.valid[0]://info
				YAHOO.util.Dom.get(messages._info).innerHTML = html;
			
			break;
			case messages.valid[1]://warn
				YAHOO.util.Dom.get(messages._warning).innerHTML = html;
			
			break;
			case messages.valid[2]://_error
				YAHOO.util.Dom.get(messages._error).innerHTML = html;
			
			break;
			case messages.valid[3]://succ
				YAHOO.util.Dom.get(messages._success).innerHTML = html;
			
			break;
		}
	},
	_display:function(type,html)
	{
		messages._setText(type,html)
		messages._show(type);
	},
	
	/**
	* the four show methods are the public methods to display the bar with a given message
	*/
	showInfo:function(html)
	{
		messages._display( 'info',html);
	},		
	showWarning:function(html)
	{   
		messages._display( 'warning',html);
	},
	showError:function(html)
	{  
		messages._display( 'error',html);
	},
	

	showSuccess:function(html)
	{  
		messages._display( 'success',html);
		
	}
};
	

messages.init();
