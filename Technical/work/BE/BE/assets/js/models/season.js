if(!App.dom.definedExt('SeasonModel')){
Ext.define('SeasonModel', 
{
	extend: 'Ext.data.Model',
	fields: 
	[
 
	


		'reg_needed'
		,"season_id"
		,"season_name"
		,"league_id"
		,"league_name"
		,"_effective_range_start"
		,"_effective_range_end"
        ,"effective_range_start"
        ,"effective_range_end"
        ,"isactive"
        ,"isactive_icon"
        ,"schedule_count"
        ,"is_enabled"
        ,"is_enabled_icon"
        ,"_reg_range_start"
        ,"_reg_range_end"
        ,"reg_range_start"
        ,"reg_range_end"
        ,"display_start"
        ,"display_end"
         ,'deposit_status'
         ,'deposit_amount'
         ,'fees_status'
         ,'fees_amount'
         ,'isactive_display'
         ,'is_enabled_display'
         ] 
         
         
});}
 
