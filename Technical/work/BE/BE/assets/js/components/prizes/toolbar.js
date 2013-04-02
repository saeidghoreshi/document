var toolbar=
{
items:
[
	{xtype:'button',text:'Prizes',iconCls:'timeline_marker',handler:function()
		{
			
			o_prizes.prizes.get();
			o_prizes.prizes.show();
			
			o_prizes.categories.hide();
			o_prizes.warehouses.hide();
		}
	}
	,'-'
	,{xtype:'button',text:'Categories',iconCls:'cog',handler:function()
		{
			o_prizes.categories.get();
			o_prizes.categories.show();
			o_prizes.prizes.hide();
			o_prizes.warehouses.hide();
		}
	}
	,{xtype:'button',text:'Warehouses',iconCls:'table_relationship',handler:function()
		{
			o_prizes.warehouses.get();
			o_prizes.warehouses.show();
			o_prizes.prizes.hide();
			o_prizes.categories.hide();
		}
	}


]	
	
};