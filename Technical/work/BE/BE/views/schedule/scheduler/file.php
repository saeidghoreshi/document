<div id='file-tab'>







<div id='file-save-content' >

<h1>File Management</h1>
<br/>


	<fieldset>
	<legend><span class='label'>Save</span></legend>

	<table>
	<tr>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
	</tr>
	<tr>
		<td valign="top">
			<span class='label'>Memo:</span>
		</td>
		<td valign="top" colspan='6'>
			<textarea rows='2' cols='38' id='save-user-memo' ></textarea>
		</td>	
		<td valign="top" colspan='3'>
			Use the memo to remind yourself about the details of this save.
		</td>
	</tr>
	<tr>
		<td valign="top">
			<span class='label'>Private:</span>
		</td>
		<td valign="top">
			<input type='checkbox' id='save-private' />
		</td> 
		<td valign="top" colspan='8'>
			A private save can only be Loaded by you, not by other users. 
		</td>
	</tr>
	<tr>
		<td>
			<button id='btn-file-save'>Save</button> 
		</td>
	</tr>


	</table>
	</fieldset>




</div>
<div id='file-load-content' class='hidden'>
<h1>File Management</h1>
<br/>
	<fieldset>
	<legend><span class='label'>Load</span></legend>


	
	
	
	
	<div class='datatable'>
		<div id='dt-file-load'></div>
	</div>
	<div id='dt-file-load-pag'></div>
	
	<button id='btn-file-load'>Load</button> 
	</fieldset>

</div>
<div id='file-import-content' class='hidden'>

	<h1>Import Schedule</h1>
	

	
	<ul>
		<li>Copy and paste the contents of the exported csv here.</li>
		<li>Name: <input type='text' id='txt-csv-name' value='Imported schedule'/></li>
	</ul>
	<br />
	<textarea id='txt-csv-contents'></textarea>
	<br />
	
	<button id='btn-import-csv'>Import From CSV</button>
	<form enctype="multipart/form-data" class='hidden'>
		<input name='file-import-csv' id='file-import-csv' type='file' size='60' />
	</form>
	

</div>
<div id='file-export-content' class='hidden'>


	<h1>Export Schedule</h1>
	
	<table>
		<tr>
			<td width='70%'>For Spreadsheets (MS Excell) and other statistics programs.</td>
			<td width="30%">
				<button id='btn-export-csv'>Export to CSV</button>
			</td>
		</tr>
		<tr>
			<td width='70%'>A readable and printable copy of the schedule.</td>
			<td width="30%">
				<button id='btn-export-pdf' >Export to PDF</button>
			</td>
		</tr>
	</table>
	
	
	
	
	
	

</div>








</div>

