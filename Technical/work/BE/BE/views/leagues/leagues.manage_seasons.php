<div class=window>  
<table width="100%" cellpadding="1" cellspacing="1">  
<tr>
    <td>
     <div id="MS-seasons-menu-btn-DIV" class="form-field">
        <div id="MS-seasons-menu-btn"></div>
     </div>
    </td>
</tr>
<hr>
<tr>
    <td>
    this process flush and recreate necessary tables for all league slave sites per chosen season
    </td>
</tr>
<tr>
    <td>
    <div id="MS-seasons-menu-btn-DIV" class="form-field">
    <div id="MS-s-btn-run-reg"></div>
    </div>
    </td>
</tr> 
<hr>
<h1>Set active season</h1>
<tr>
    <td>
    <div id="MS-seasons-setactive-btn"></div>
    <div id="MS-seasons-active-season-DIV" class="form-field">
        <div id="MS-seasons-active-season-input" class="input"  >
            <input type="text" id="MS-seasons-active-season" disabled />
        </div>
    </div>               
    </td>
</tr>
</table>
</div>
<?='<select id="MS-seasons-menu-btn-data" name="MS-seasons-menu-btn-data">' ?>
<? foreach($season_list as $i=>$v): ?>
<?= '<option value="'.$v["season_id"].'">'.$v["season_name"].'</option>'?>
<? endforeach;?>
<?= '</select>'?>