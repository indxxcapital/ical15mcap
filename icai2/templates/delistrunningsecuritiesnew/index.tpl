<!-- BEGIN Main Content -->

{if $smarty.get.calcindxx_id}
 <script type='text/javascript'>
window.open('http://97.74.65.118/icai/index.php?module=calcindxxclosingid&id='+{$smarty.get.calcindxx_id},'Share','toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,history=yes,resizable=yes');
</script>

{/if}

 {literal}
 <script type='text/javascript'>
 
 
 function confirmdelete(id)
 {

 var temp=confirm("Are you sure you want to delete this record ")
  if(temp)
   {	
	
	window.location.href='index.php?module={/literal}{$currentClass}{literal}&event=delete&id='+id;
	}
	else{
	return false;
	}
 }
 
 



$(document).ready(function(){
 $("#deleteSelected").click(function(){
	 
	 var temp=confirm("Are you sure you want to delete this record ")
  if(temp)
   {	
	 
	 
	 
 var checkedArray=Array();
 var i=0;
  $('input[name="checkboxid"]:checked').each(function() {
i++;
checkedArray[i]=$(this).val();
});
var parameters = {
  "array1":checkedArray
};


$.ajax({
    url : "index.php?module={/literal}{$currentClass}{literal}&event=deleteindex",
    type: "POST",
    data : parameters,
    success: function(data, textStatus, jqXHR)
    {
	  window.location.href='index.php?module={/literal}{$currentClass}{literal}';
	    //data - response from server
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
 
    }
});

}
	else{
	return false;
	}


});
	 
	 
	
	 
 
}); 
 
</script>
 
 {/literal}
               {include file='notice.tpl'}
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-table"></i> Index</h3>
                            </div>
                            <div class="box-content">
                                <div class="btn-toolbar pull-right clearfix">
                                    <div class="btn-group">
                                        <a class="btn btn-circle show-tooltip" title="Add new record" href="index.php?module={$currentClass}&event=addNew" ><i class="icon-plus"></i></a>
                                        <!--<a class="btn btn-circle show-tooltip" title="Edit selected" href="#"><i class="icon-edit"></i></a>-->
                                        <a class="btn btn-circle show-tooltip" title="Delete selected" id="deleteSelected" href="#"><i class="icon-trash"></i></a>
                                    </div>
                                    <!--<div class="btn-group">
                                        <a class="btn btn-circle show-tooltip" title="Print" href="#"><i class="icon-print"></i></a>
                                        <a class="btn btn-circle show-tooltip" title="Export to PDF" href="#"><i class="icon-file-text-alt"></i></a>
                                        <a class="btn btn-circle show-tooltip" title="Export to Exel" href="#"><i class="icon-table"></i></a>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn btn-circle show-tooltip" title="Refresh" href="#"><i class="icon-repeat"></i></a>
                                    </div>-->
                                </div>
                                <div id="Div" class="clearfix"></div>
<table class="table table-advance" id="table1">
    <thead>
        <tr>
            <th style="width:18px"><input type="checkbox" /></th>
            <th>Name</th>
            <th>Start Date</th>
            <th>Notified to admin</th>
            <th>Admin Status</th>
      
            
            <th style="width:100px">Action</th>
        </tr>
    </thead>
    <tbody>
    	{foreach from=$indexdata item=point key=k}
        <tr>
            <td><input type="checkbox" id="checkboxid"  name="checkboxid" value="{$point.id}" /></td>
            <td>{$point.name}</td>
            <td>{$point.startdate}</td>
            
            <td>{if $point.status==0}<span class="label label-important">No</span>{else}<span class="badge badge-success">Yes</span>{/if}</td>
            
            <td>{if $point.adminapprove==0}<span class="label label-important">No</span>{else}<span class="badge badge-success">Yes</span>{/if}</td>
             
            <td>
                <div class="btn-group">
                
                
                
                    <a class="btn btn-small show-tooltip" title="View" href="index.php?module={$currentClass}&event=viewupcoming&id={$point.id}"><i class="icon-zoom-in"></i></a>
                   <!-- <a class="btn btn-small show-tooltip" title="Edit" href="index.php?module=delistrunningsecurities&event=editfornext&id={$point.id}"><i class="icon-edit"></i></a>-->
                    
                   <!-- index.php?module=caindex&event=delete&id={$point.id}-->
                    <a class="btn btn-small btn-danger show-tooltip " title="Delete" href="#" id="a1" onclick="confirmdelete({$point.id})"><i class="icon-trash"></i></a>
                </div>
            </td>
        </tr>
        {/foreach}
     
    </tbody>
</table>
                            </div>
                        </div>
                    </div>
                </div>
                
                  <!-- END Main Content -->