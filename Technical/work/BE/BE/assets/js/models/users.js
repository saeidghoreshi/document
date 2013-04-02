//define model, password vtype,
var user_model='User';
if(!App.dom.definedExt(user_model)){
Ext.define(user_model, 
{
	extend: 'Ext.data.Model',
	fields: 
	[//form.loadRecord will only add fields that are in the model
	   {name: 'full_name',      type: 'string'},
	   {name: 'home_display',      type: 'string'},
	   {name: 'mobile_display',      type: 'string'},
	   {name: 'work_display',      type: 'string'},
	   {name: 'person_fname',      type: 'string'},
	   {name: 'person_lname',      type: 'string'},
	   {name: 'person_gender',     type: 'string'},
	   {name: 'postal_value',      type: 'string'},
	   {name: 'login',      	   type: 'string'},
	   {name: 'email',      	   type: 'string'},	           
	   {name: 'address_city',      type: 'string'},	           
	   {name: 'address_id',      type: 'string'},	           
	   {name: 'address_country',   type: 'string'},	           
	   {name: 'address_region',    type: 'string'},	           
	   {name: 'address_street',    type: 'string'},	           
	   {name: 'country_abbr',      type: 'string'},	           
	   {name: 'region_abbr',      type: 'string'},	           
	   {name: 'home-pre',      type: 'string'},	           
	   {name: 'home-ac',      type: 'string'},	           
	   {name: 'home-num',      type: 'string'},	           
	   {name: 'mobile-num',      type: 'string'},	           
	   {name: 'mobile-ac',      type: 'string'},	           
	   {name: 'mobile-pre',      type: 'string'},	           
	   {name: 'work-num',      type: 'string'},	           
	   {name: 'work-ac',      type: 'string'},	           
	   {name: 'work-pre',      type: 'string'},	           
	   {name: 'work-ext',      type: 'string'},	           
	   {name: 'address_street',      type: 'string'},	           
	   {name: 'person_birthdate',  type: 'date', dateFormat: 'Y/m/d'},
	   {name: 'last_login_date',   type: 'string' },
	   {name: 'user_id',  type: 'int'},
	   {name: 'person_id',  type: 'int'}
	]
});}


//define my custom password type
Ext.apply(Ext.form.field.VTypes, 
{
	password: function(val, field) 
	{
		if (field.initialPassField) 
		{
			var pwd = field.up('form').down('#' + field.initialPassField);
			if(val == pwd.getValue())
			{
			    
				return true;//this is disabled for 2.0 release
				
				
			    var count=new Array();
			    var score=new Array();
			    var first=new Array();
			    
			    var types=['repeats','upper','lower','numeric','special'];
			    
			    first['special']=20;
			    first['numeric']=15;
			    first['upper']=15;
			    first['lower']=5;
			    first['repeats']=-5;//subtract less for the first rep
			    
			    score['special']=10;
			    score['numeric']=10;
			    score['upper']=10;
			    score['lower']=5;
			    score['repeats']=-10;//subtract more for each extra rep
			    for(i in types)
			    {
			        count[types[i]]=0;
			    }
			    
			    var count_repeats=0;
			    var count_upper=0;
			    var count_lower=0;
			    var count_numeric=0;
			    var count_special=0;
			    var pwd_chars=val.split('');
			    var len=pwd_chars.length;
			    var c,d;
			    for(i in pwd_chars)
			    {
			        c=pwd_chars[i];
			        //loop through the password one character at a time
			        
			        //count lowercase
			        if(c.match(/[a-z]/g))
			        {
			            count['lower']++;
			        }
			        //count the number of uppercase characters
			        else if(c.match(/[A-Z]/g))
			        {
			            count['upper']++;
			        }
			        //count digits
			        else if(c.match(/[0-9]/g))
			        {
			            count['numeric']++;
			        }
			        //must escape using a backslash \ the following special characters that have meaning in regex
			        //   .|*?+(){}[]^$\                  all other characters match themselves, from @ onward
			        else if(c.match(/[\.\|\*\?\+\(\)\{\}\[\]\\@,#&-_%=£!:;,~<>'"]/g))
			        {
			            count['special']++;
			        }
			        else
			        {
			            //.log('INVALID CHARACTER=',c);
			            return false;
			        }
			        for(j in pwd_chars)
			        {
			            d=pwd_chars[j];
			            if(i!=j && c==d)
			            {
			                //.log('repeat character found',c,d,i,j);
			                count['repeats']++;
			                
			            }
			            
			        }
			    }//end of loop
			    
			    //.log(count);
			    var total=0;//nScore = parseInt(pwd.length * nMultLength);
			    for(i in types)
			    {                        
			        var t=types[i];
			        //extra points for the first time
			        total+=count[t]*first[t];//still zero if count is zero
			        if(count[t]>1)
			        {//but multiples get the regular score
			            total+=(count[t]-1)*score[t];                                
			        }
			    }    
			    
			    //.log(total);
			    
			    //restrict total to within this range
			    total=Math.min(total,100);
			    total=Math.max(total,0);
			    //.log('final:',total);
			    //var perc = 
			    
			    
			    
			    return true;
			}
		}
		return true;
	},

	passwordText: 'Passwords do not match'
});  