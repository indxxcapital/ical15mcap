<?php /* Smarty version 2.6.14, created on 2015-08-18 09:54:31
         compiled from commodityindxxtemp/index.tpl */ ?>
<!-- BEGIN Main Content -->
 <?php echo '
 <script type=\'text/javascript\'>
 
 
 function confirmdelete(id)
 {

 var temp=confirm("Are you sure you want to delete this record ")
  if(temp)
   {	
	
	window.location.href=\'index.php?module=commodityindxxtemp&event=delete&id=\'+id;
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
  $(\'input[name="checkboxid"]:checked\').each(function() {
i++;
checkedArray[i]=$(this).val();
});
var parameters = {
  "array1":checkedArray
};


$.ajax({
    url : "index.php?module=commodityindxxtemp&event=deleteindex",
    type: "POST",
    data : parameters,
    success: function(data, textStatus, jqXHR)
    {
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
 
 '; ?>

               
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-table"></i>Upcomming Index</h3>
                            </div>
                            <div class="box-content">
                                <div class="btn-toolbar pull-right clearfix">
                                    <div class="btn-group">
                                        <a class="btn btn-circle show-tooltip" title="Add new record" href="index.php?module=commodityindxxtemp&event=addNew" ><i class="icon-plus"></i></a>
                                     <!--  <a class="btn btn-circle show-tooltip" title="Edit selected" href="#"><i class="icon-edit"></i></a>-->
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
            <th>Code</th>
            <th>Approved</th>
            
            <th style="width:100px">Action</th>
        </tr>
    </thead>
    <tbody>
    	<?php $_from = $this->_tpl_vars['indexdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['point']):
?>
        <tr>
            <td><input type="checkbox" id="checkboxid"  name="checkboxid" value="<?php echo $this->_tpl_vars['point']['id']; ?>
" /></td>
            <td><?php echo $this->_tpl_vars['point']['name']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['code']; ?>
</td>
            <td><?php if ($this->_tpl_vars['point']['status'] == 0): ?><span class="label label-important">Not approved!</span><?php else: ?><span class="badge badge-success">Approved</span><?php endif; ?></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-small show-tooltip" title="View" href="index.php?module=commodityindxxtemp&event=view&id=<?php echo $this->_tpl_vars['point']['id']; ?>
"><i class="icon-zoom-in"></i></a>
                    <a class="btn btn-small show-tooltip" title="Edit" href="index.php?module=commodityindxxtemp&event=edit&id=<?php echo $this->_tpl_vars['point']['id']; ?>
"><i class="icon-edit"></i></a>
                    
                   <!-- index.php?module=commodityindxxtemp&event=delete&id=<?php echo $this->_tpl_vars['point']['id']; ?>
-->
                    <a class="btn btn-small btn-danger show-tooltip " title="Delete" href="#" id="a1" onclick="confirmdelete(<?php echo $this->_tpl_vars['point']['id']; ?>
)"><i class="icon-trash"></i></a>
                </div>
            </td>
        </tr>
        <?php endforeach; endif; unset($_from); ?>
     
    </tbody>
</table>
                            </div>
                        </div>
                    </div>
                </div>
                
                  <!-- END Main Content -->