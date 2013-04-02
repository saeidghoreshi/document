
var classname = "Spectrum.getstarted.sign";
if(!App.dom.definedExt(classname)){
Ext.define(classname,
{
	initComponent:function(config){},
	//form:null,
	grid:null,

	load:function()
	{
		this.grid.store.load();
	},
	constructor:function(config)
	{
		this.grid = Ext.create('Ext.spectrumgrids.sa_assignment',{
			title:'Signing Authorities',
			collapsible:false,hide_role:true,hide_created:true,
			
			renderTo:'grid-gs-sign',
			width:355//,disabled:true 
			,disable_motions:true//hide existing buttons and make my own. special case since we skip the motion process here
			,bbar:
			[
				{iconCls:'add',tooltip:'Add Signer',scope:this,handler:function()
				{
					App.GS.startLoading();//block navigation
					var form=Ext.create('Spectrum.forms.user_create',{});
					var win=Ext.create('Spectrum.windows.user',{items:form});

					win.on('hide',function()
					{
						//.log('todo: test if person was finished, so we will assign to the role');
						//var user_id=form.get_user_id();
						var person_id=form.get_person_id();
						var person_name=form.get_person_name();
						//.log(person_id);
						
						//copied from below button: they have similar jobs
						if(!person_id || person_id<0) 
						{
							//.log('person_id error');
							App.GS.stopLoading();//allow navigation 
							return;
						}
						
						
						var url="index.php/permissions/post_create_assignment/"+App.TOKEN;
						var post='person_id='+person_id+"&role_id=16"+"&org_id=a";//active org flag
						//16 is SA. from perm.lu_role
						YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,failure:App.error.xhr,success:function(o)
						{
							App.GS.activeGrid[App.GS.slideEnum.SIGN].load();//refresh grid data
							App.GS.stopLoading();//allow navigation 
							
							//.log(o);
							//.log('todo: refresh');
						}},post);

					},this)	;
					win.show();
				}}
				,'->'
				,{iconCls:'delete',tooltip:'Remove Signer',scope:this,handler:function()
				{
					
					
					//.log('todo: delete selected');
					var rows=App.GS.activeGrid[App.GS.slideEnum.SIGN].grid.getSelectionModel().getSelection();
					
					if(!rows.length){return;}
					//COPIED FROM ORG_USERS ROLE 
					var row=rows[0];
					var full_name=row.get('person_fname')+" "+row.get('person_lname');
				
					var role = row.get('role_name');
					
					var msg="Remove the role of  '"+role+"' from '"+full_name+"' ?";
					Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 			{
		 				if(btn_id!='yes')return;
						var rowsSelected=App.GS.activeGrid[App.GS.slideEnum.SIGN].grid.getSelectionModel().getSelection();
		 				var assn_id    = rowsSelected[0].get('assignment_id');
		                var url='index.php/permissions/post_delete_assignment/'+App.TOKEN;
		                var post='assn_id='+assn_id;
		                var callback={socpe:this,success:function(o)
		                {
							App.GS.activeGrid[App.GS.slideEnum.SIGN].load();
		                },scope:this};
		                YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
		 				 
					},this);
					
				}}
			]
		});
		
		//.log('creating sa_assignment grid, before load()');
		//loaad active org into the grid, and load the records, if any
		this.load();
		
	}
	
});
	
}
App.GS.activeGrid[App.GS.slideEnum.SIGN] = Ext.create(classname,{});
//.log('end of file gs SIGN.js');