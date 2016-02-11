 <!-- BEGIN Main Content -->
  
 {include file="notice.tpl"}
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-reorder"></i>Add Stock For Upcomming Index</h3>
                            </div>
                            <div class="box-content">
                             <form action="" method="post" onsubmit="return ValidateForm();" class="form-horizontal">
                             
                              
    {foreach from=$fields key=p item=item}
               <p>   {field data=$item value=$postData}{/field}
                </p>  {/foreach}
 <p>
                 <label>&nbsp;</label>
                 <div id="Div" class="clearfix"></div>
                                        <table class="table table-advance" id="table1">
                                            <thead>
                                                <tr>
                                                     <th style="width:100px"><input type="checkbox" /></th>
                                                    <th>Name</th>
                                                    <th>Code</th>
                                                   <!-- <th title="Weight for Added Security with respect to Indxx ">Weight</th>-->
                                                  <!--  <th>Go Live Date</th>
                                                    -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {foreach from=$indexdata item=point key=k}
                                                <tr>
                                                    <td><input type="checkbox" id="checkboxid" {if $point.selected}checked{/if} name="checkboxid[]" value="{$point.id}" /></td>
                                                    <td>{$point.name}</td>
                                                    <td>{$point.code}</td>
                                                 <!--   <td><input type="text" name="weight_{$point.id}" /></td>-->
                                                 
                                                    <!--<td>{$point.dateStart}</td>-->
                                                   
                                                    
                                                </tr>
                                                {/foreach}
                                             
                                            </tbody>
                                        </table>
                 <div class="form-actions">
                                       <button type="submit" class="btn btn-primary" name="submit" id="submit"><i class="icon-ok"></i> Save</button>
                                       <button type="button" class="btn" name="cancel" id="cancel" onClick="document.location.href='{$BASE_URL}index.php?module=replacerunningsecurities';" >Back</button>
                                       
                                    </div>
                 
                 
                  
                  </form>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
 <!-- END Main Content -->