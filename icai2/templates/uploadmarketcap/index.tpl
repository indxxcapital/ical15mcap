<!-- BEGIN Main Content -->
 {literal}
 <script>
    function copy_data(val){
     var a = document.getElementById(val.id).value;

	{/literal}{if $indexdata|@count>0}
	{foreach from=$indexdata item=point key=k}
	{literal}
	 document.getElementById("startDate_{/literal}{$point.id}{literal}").value=a;
    {/literal}
	{/foreach}
	{/if}
	{literal}
	}    
	
function toggleme(val) {

 $( "#startDate_"+val ).toggle();
 }

function toggleall(){
	{/literal}{if $indexdata|@count>0}
	{foreach from=$indexdata item=point key=k}
	{literal}
	toggleme({/literal}{$point.id}{literal});
	    {/literal}
	{/foreach}
	{/if}

 
 {literal}

}	
    </script>
 
 {/literal}
 
      {include file='notice.tpl'}          
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-table"></i> Select Index to Rebalance</h3>
                                <div style="text-align:right"> <a href="index.php?module=rebalancing&event=download">Download Tickers</a></div>
                            </div>
                          
                            	<div class="box-content">
                             <form action="" method="post" onsubmit="confirm('Please Wait for indxx add, you will get a csv to upload shares / weights')" onsubmit="return ValidateForm();" enctype="multipart/form-data" class="form-horizontal">
                             
                              
    {foreach from=$fields key=p item=item}
               <p>   {field data=$item value=$postData}{/field}
                </p>  {/foreach}
      
                 
             
                                <div class="btn-toolbar pull-right clearfix">  
                                </div>
                                <div id="Div" class="clearfix"></div>
                                        <table class="table table-advance" id="table1">
                                            <thead>
                                                <tr>
                                                    <th style="width:100px"><input onclick="toggleall();" type="checkbox" /></th>
                                                    <th>Name</th>
                                                    <th>Code</th>
                                                    <th>Pre-Closing Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {foreach from=$indexdata item=point key=k}
                                                <tr>
                                                    <td><input type="checkbox" id="checkboxid"  onclick="toggleme({$point.id});" name="checkboxid[]" value="{$point.id}" /></td>
                                                    <td>{$point.name}</td>
                                                    <td>{$point.code}</td>
                                                    <td><input style="display:none;" type="text" placeholder="YYYY-MM-DD" name="startDate_{$point.id}" id="startDate_{$point.id}" value="" class="date-pick date-picker"></td>
                                                    
                                                </tr>
                                                {/foreach}
                                             
                                            </tbody>
                                        </table>
                      
                      <label>&nbsp;</label>
                 				<div class="form-actions">
                                       <button type="submit" class="btn btn-primary" name="submit" id="submit"><i class="icon-ok"></i> Submit</button>    </form>    </div>
                                 
                          
                        </div>
                    </div>
                </div>
                </div>
                  <!-- END Main Content -->