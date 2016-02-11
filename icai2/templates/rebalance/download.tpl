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
    </script>
 
 {/literal}
 
         {include file='notice.tpl'}       
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-table"></i> Select Index to Download</h3>
                                <div style="text-align:right">
                               
                                 </div>
                            </div>
                          
                            	<div class="box-content">
                             <form action="" method="get" onsubmit="return ValidateForm();" class="form-horizontal">
                             
      <input type="hidden" name="module" value="rebalancing" />
      <input type="hidden" name="event" value="download" />
      
                 
             
                                <div class="btn-toolbar pull-right clearfix">  
                                </div>
                                <div id="Div" class="clearfix"></div>
                                        <table class="table table-advance" id="table1">
                                            <thead>
                                                <tr>
                                                    <th style="width:100px"><input type="checkbox" /></th>
                                                    <th>Name</th>
                                                    <th>Code</th>
                                                    <th>Go Live Date</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {foreach from=$indexdata item=point key=k}
                                                <tr>
                                                    <td><input type="checkbox" id="checkboxid" {if $point.selected}checked{/if} name="checkboxid[]" value="{$point.id}" /></td>
                                                    <td>{$point.name}</td>
                                                    <td>{$point.code}</td>
                                                    <td>{$point.dateStart}</td>
                                                   
                                                    
                                                </tr>
                                                {/foreach}
                                             
                                            </tbody>
                                        </table>
                      
                      <label>&nbsp;</label>
                 				<div class="form-actions">
                                       <button type="submit" class="btn btn-primary" name="submit" value='submit' id="submit"><i class="icon-ok"></i> Download</button>    </form>    </div>
                                 
                          
                        </div>
                    </div>
                </div>
                </div>
                  <!-- END Main Content -->