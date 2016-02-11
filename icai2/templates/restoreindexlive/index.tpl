 <!-- BEGIN Main Content -->

 {include file="notice.tpl"}
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-reorder"></i>Select Index to Restore</h3>
                            </div>
                            <div class="box-content">
                             <form action="" method="post" onsubmit="return ValidateForm();" class="form-horizontal">
                             
                      {if $liveindexdata|@count>0}        
    {foreach from=$liveindexdata key=p item=item}
           <p>     <label> <input type="checkbox" name='index_id[]' value="{$item.id}" /> {$item.name}
                </label>  </p>{/foreach}
 <p>
                 <label>&nbsp;</label>
                 <div class="form-actions">
                                       <button type="submit" class="btn btn-primary" name="submit" id="submit"><i class="icon-ok"></i> Save</button>
                                       <button type="button" class="btn" name="cancel" id="cancel" onClick="document.location.href='{$BASE_URL}index.php?module=databaseusers';" >Back</button>
                                       
                                    </div>
                 {/if}
                 
                  
                  </form>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
 <!-- END Main Content -->