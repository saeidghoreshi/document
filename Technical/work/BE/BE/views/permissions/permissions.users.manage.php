

<?#data storage hidden variables?>
<input type='hidden' value='<?=$org_type?>' id='active-org-type' />
<?#labels for buttons/tabs/columns/...?>
<input type='hidden' value="User List" id='lbl-tab-list' />
<input type='hidden' value="Current Roles" id='lbl-tab-current' />
<input type='hidden' value="Assign Roles" id='lbl-tab-assn' />

<input type='hidden' id='null-user-message' value='No Person Chosen' />


<input type='hidden' value='Assigned Roles'  id='lbl-table-roles'/>
<input type='hidden' value='Current Users'  id='lbl-table-users'/>

<input type='hidden' value=''  id='lbl-btn-newuser'/>
<input type='hidden' value=''  id='lbl-btn-search'/>
<input type='hidden' value=''  id='lbl-btn-all'/>
<input type='hidden' value='Delete Selected User'  id='lbl-btn-delete'/>
<input type='hidden' value='Edit Selected User'  id='lbl-btn-edit'/>
<input type='hidden' value='View Roles'  id='lbl-btn-roles'/>
<input type='hidden' value='Add Roles'  id='lbl-btn-addrole'/>
<input type='hidden' value='Delete Selected Role'  id='lbl-btn-delrole'/>
<input type='hidden' value='Create New'  id='lbl-btn-create'/>

<input type='hidden' value='Names'  id='lbl-col-nameheader'/>
<input type='hidden' value='First'  id='lbl-col-fname'/>
<input type='hidden' value='Last'  id='lbl-col-lname'/>
<input type='hidden' value='Birthday'  id='lbl-col-bdate'/>
<input type='hidden' value='Last Activity'  id='lbl-col-activity'/>
<input type='hidden' value='Actions'  id='lbl-col-actions'/>
<input type='hidden' value='Email'  id='lbl-col-email'/>


<input type='hidden' value='Role'  id='lbl-col-role'/>
<input type='hidden' value='Organization'  id='lbl-col-org'/>
<input type='hidden' value='Starts On'  id='lbl-col-start'/>
<input type='hidden' value='Expires On'  id='lbl-col-end'/>

			
<input type='hidden' value="Search" id='lbl-btn-search' />	
<input type='hidden' value="Show All" id='lbl-btn-showall' />	
<input type='hidden' value="Search" id='hdr-prompt-search' />	
<input type='hidden' value="Enter name(s) of user to search for,  (Example: 'John Doe') :" id='msg-prompt-search' />	


<input type='hidden' value='Role' id='default-role-filter' />
<input type='hidden' value='Organization' id='default-org-filter' />

<div width='100%'  id="ctr-manageusers" class="window">

		
		
	<div id='ctr-users' class='ctr '><div id="dt-users"></div></div>
	

	<div id='ctr-current' class='ctr dghidden'><div id="dt-current"></div></div>
	



        

    
    
</div>
