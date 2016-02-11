<!-- BEGIN Main Content -->
{include file="notice.tpl"}

 {literal}
<style>
.table-advance tbody>tr:nth-child(odd)>td, .table-advance tbody>tr:nth-child(odd)>th {
background: #293b50 url('assets/New/img/pattern.png') repeat !important;
}
.table-advance tbody>tr:nth-child(even)>td, .table-advance tbody>tr:nth-child(even)>th {
background: #e5e9f4 url('Assets/New/img/pattern2.png') repeat !important;
}
.table-advance thead {
background: #e5e9f4 url('Assets/New/img/pattern2.png') repeat !important;
border-left: 4px solid #e5e9f4 url('Assets/New/img/pattern2.png') repeat !important;
}
.table{
	border-collapse:inherit !important;	
}

.table.fill-head thead{
background: #e5e9f4 url('Assets/New/img/pattern2.png') repeat !important;
border-left: 4px solid #e5e9f4 url('Assets/New/img/pattern2.png') repeat !important;	
}
.table-striped > tbody > tr:nth-child(odd) > td, .table-striped > tbody > tr:nth-child(odd) > th {
background: #293b50 url('assets/New/img/pattern.png') repeat !important;	
color:#888 !important;
}
</style>
{/literal}
 
 {literal}
<script type='text/javascript'>
 
 
 function confirmdelete(id)
 {

 var temp=confirm("Are you sure you want to delete this record ")
  if(temp)
   {	
	
	window.location.href='index.php?module=adjbenchmarkindex&event=delete&id='+id;
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
    url : "index.php?module=benchmarkindex&event=deleteindex",
    type: "POST",
    data : parameters,
    success: function(data, textStatus, jqXHR)
    {
        //data - response from server
		
		
		window.location.href='index.php?module=adjbenchmarkindex';
		
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
 
 <br><br><br><br><br><br><br><br>  
 <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="thumb-pad2">
                <div class="thumbnail"> 
                <div class="caption">
                      
                    </div>
                   
                   
                   
                    
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="thumb-pad2">
                <div class="thumbnail"> 
                <div class="caption">
                        <p class="title">Local Benchmark Index</p>
                    </div>
              
                   
                    
                </div>
            </div>
        </div>
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            
                            <div class="box-content" style="background: #293b50 url({$BASE_URL}assets/New/img/pattern3.png) repeat !important;">
                                <div class="btn-toolbar pull-right clearfix">
                                    <div class="btn-group">
                                      {if $sessData.User.type==1}  <a class="btn btn-circle show-tooltip" title="Add new record" href="index.php?module=adjbenchmarkindex&event=addNew"><i class="icon-plus"></i></a>
                                         <a class="btn btn-circle show-tooltip" title="Delete selected" id="deleteSelected" href="#"><i class="icon-trash"></i></a>{/if}
                                          <a class="btn btn-circle show-tooltip" title="Export to Excel" href="index.php?module=adjbenchmarkindex&event=exportExcel"><i class="icon-table"></i></a>
                                         
                                        <!--<a class="btn btn-circle show-tooltip" title="Print" href="#"><i class="icon-print"></i></a>
                                        <a class="btn btn-circle show-tooltip" title="Export to Excel" href="index.php?module=users&event=exportExcel"><i class="icon-table"></i></a>-->
                                    </div>
                                        <!--<a class="btn btn-circle show-tooltip" title="Edit selected" href="#"><i class="icon-edit"></i></a>
                                        <a class="btn btn-circle show-tooltip" title="Delete selected" href="#"><i class="icon-trash"></i></a>-->
                                    </div>
                                    <div class="btn-group">
                                        
                                        <!--<a class="btn btn-circle show-tooltip" title="Export to PDF" href="#"><i class="icon-file-text-alt"></i></a> <a class="btn btn-circle show-tooltip" title="Print" href="#"><i class="icon-print"></i></a>
                                        <a class="btn btn-circle show-tooltip" title="Export to Excel" href="#"><i class="icon-table"></i></a>
                                        
                                    <!--<div class="btn-group">
                                        <a class="btn btn-circle show-tooltip" title="Refresh" href="#"><i class="icon-repeat"></i></a>
                                    </div>-->
                                </div>
                                <div class="clearfix"></div>
<table class="table table-advance" id="table1">
    <thead>
        <tr>
            <th style="width:18px"><input type="checkbox" /></th>
            <th>Name</th>
            <th>Ticker</th>
            <th>Currency</th>
            <th style="width:100px">Action</th>
        </tr>
    </thead>
    <tbody>
    	{foreach from=$userdata1 item=point key=k}
        <tr>
            <td><input type="checkbox" id="checkboxid"  name="checkboxid" value="{$point.id}" /></td>
            <td>{$point.name}</td>
            <td>{$point.ticker}</td>
            <td>{$point.curr}</td>
            <td style="width:145px !important;">
                <div class="btn-group">
                    <!--<a class="btn btn-small show-tooltip" title="View" href="#"><i class="icon-zoom-in"></i></a>-->
                  {if $sessData.User.type==1}      <a class="btn btn-small show-tooltip" title="Edit" href="index.php?module=adjbenchmarkindex&event=edit&id={$point.id}"><i class="icon-edit"></i></a>
                    <a class="btn btn-small btn-danger show-tooltip" title="Delete" href="#" id="a1" onclick="confirmdelete({$point.id})"><i class="icon-trash"></i></a>
                    {/if}
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