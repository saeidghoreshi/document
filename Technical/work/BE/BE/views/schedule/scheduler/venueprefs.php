<div id="venueprefs-tab">


<form name="venueprefs">

<div  id="current-label">
<h3>
Currently Editing:  <span id="vp-current" ></span>
</h3>
</div>


<br />
<div class="datatable">
<div id='dt-vp'>
</div>
</div>

<div id='dt-pag-vp'></div>

<div align="center"><p><b>Rank: </b><i>Rank '1' is the most important; rank '5' is the least important.</i></p></div>
<!---
<table width="100%" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td >
            <div id="vp-label">Preference</div>
        </td>
        <td align="center" width="20%">
            <div id="p-label">Value</div>         
        </td>
        <td align="center">
            <div id="p-label">Priority</div>        
        </td>
        <td align="center">
            <div id="p-label">Description</div>        
        </td>
    </tr>    
    <tr>
        <td >
            <div id="fence-label">Fence Height</div>
        </td>
        <td >                
            <div id="fence-input" class="input">
                <input type="text" id="fence-value" name="fence-height" onfocus=" this.value=''" value="0"/>
            </div>
        </td>
        <td align="center">
 oldpri

        </td>    
        <td align="center">
            <div id="p-label">fenceDescription</div>        
        </td>    
    </tr>
    <tr>
        <td >
            <div id="warmup-label">Lighting</div>
        </td>
        <td align="center">
            <select id="light-value">
            <option value="blank"></option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
            </select>        
        </td>
        <td align="center">
            <select id="light-priority" >
            <option value="blank"></option>
            <option value="must">Must Have</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
            </select> 
        </td>
        <td align="center">
            <div id="p-label">lightDescription</div>        
        </td>
    </tr>
    <tr>
        <td>
             <div id="cooldown-label">Field Quality</div>
        </td>
        <td align="center">
            <select id="field-value">
            <option value="blank"></option>
            <option value="good">Good</option>
            <option value="average">Average</option>
            <option value="poor">Poor</option>
            </select>               
        </td>
        <td align="center">
            <select id="field-priority">
            <option value="blank"></option>
            <option value="must">Must Have</option> 
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
            </select> 
        </td>       
        <td align="center">
            <div id="p-label">field Description</div>        
        </td> 
    </tr>
    <tr>
        <td >
            <div id="cooldown-label">Seating Quantity</div>
        </td>
        <td >                
            <div id="seat-input" class="input">
                  <input type="text" id="seat-value" name="seat-value" onfocus=" this.value=''" value="0"/>
            </div>
        </td>
        <td align="center">
            <select id="seat-priority">
            <option value="blank"></option>
            <option value="must">Must Have</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
            </select> 
        </td>        
        <td align="center">
            <div id="p-label">seatDescription</div>        
        </td>
    </tr>
    <tr>
        <td >
            <div id="cooldown-label">Bench Type</div>
        </td>
        <td align="center">                
            <select id="bench-value">
            <option value="blank"></option>
            <option value="dugout">Dugout</option>
            <option value="covered">Covered</option>
            <option value="other">other</option>
            </select> 
        </td>
        <td align="center">
            <select id="bench-priority">
            <option value="blank"></option>
            <option value="must">Must Have</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
            </select> 
        </td>
        <td align="center">
            <div id="p-label">benchDescription</div>        
        </td>
    </tr>
</table>
          -->
<br />
<hr />
<br />

<div class="btnset">

<div id="add1-input" class="btn" style="float:left" >
    <button type="button" id="btn-vp-up" title="Move this to a higher priority rank" >Up</button>
    </div>
    
    <div id="add1-input" class="btn"  style="float:left" >
    <button type="button" id="btn-vp-down" value="Move this to a lower priority rank" >Down</button>
    </div>
    
    <div id="copy-contain" class="btn"  style="z-index:1100">
    <div id="m-copy-div"></div>  
    </div>
    
    <!--
    <div id="add1-input" class="btn" align="center">
    <input type="button" id="btn-vp-reset" value="Reset Form"/>
    </div>

    <div id="add2-input" class="btn" align="right">
    <input type="button" id="btn-vp-all" name="Save for all division" value="Apply to All"/>
    </div>

    <div id="add3-input" class="btn" align="right">
    <input type="button" id="btn-vp-save" name="Save for this division" value="Save"/>
    </div>-->
</div>

</form>

<div class="hidden datatable" >

    <div id="dt-vs"></div>
</div>




</div>