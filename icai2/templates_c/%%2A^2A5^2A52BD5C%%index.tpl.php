<?php /* Smarty version 2.6.14, created on 2015-12-10 04:44:45
         compiled from caindex/index.tpl */ ?>
<!-- BEGIN Main Content -->
 <?php echo '
 <script type=\'text/javascript\'>
 
 
 function confirmdelete(id)
 {

 var temp=decision();
  if(temp)
   {	
	
	window.location.href=\'index.php?module=caindex&event=delete&id=\'+id;
	}
	else{
	return false;
	}
 }
 
 



$(document).ready(function(){
 $("#deleteSelected").click(function(){
	 
	 var temp=decision();
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
    url : "index.php?module=caindex&event=deleteindex",
    type: "POST",
    data : parameters,
    success: function(data, textStatus, jqXHR)
    {
       
	   window.location.href=\'index.php?module=caindex\';
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
 function decision(){
if(confirm("Are you sure to delete?")) {
	var text=makeid();
	var replytext= prompt("Please fill text : "+text,"")
	if(replytext!= null && replytext==text)
	{
	return true;
	}else{
	alert("Input Text Not Match, Please Try Again.")
return	decision();
	}
	
}}
function makeid()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}
</script>
 
 '; ?>

               <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notice.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-table"></i> Index</h3>
                            </div>
                            <div class="box-content">
                                <div class="btn-toolbar pull-right clearfix">
                                    <div class="btn-group">
                                        <!-- <a class="btn btn-circle show-tooltip" title="Add new record" href="index.php?module=caindex&event=addNew" ><i class="icon-plus"></i></a>
                                       <a class="btn btn-circle show-tooltip" title="Edit selected" href="#"><i class="icon-edit"></i></a>-->
                                        <?php if ($this->_tpl_vars['sessData']['User']['type'] == 1): ?>
      <a class="btn btn-circle show-tooltip" title="Delete selected" id="deleteSelected" href="#">Delete Selected</a>
                                    
                                    <?php endif; ?>
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
             <th>Client</th>
               <th>Total Tickers</th>
             <th>Currency</th>
            <th>Live Date</th>
  	        <th>Dividend Adj.</th>
<th>Index Type</th>
            <th>Submitted</th>
            <th>DB Status</th>
              <th>User Status </th>
            
            
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
            <td><?php echo $this->_tpl_vars['point']['clientname']; ?>
</td>
          <td><?php echo $this->_tpl_vars['point']['total_ticker']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['curr']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['dateStart']; ?>
</td>
                  <td><?php if ($this->_tpl_vars['point']['cash_adjust'] == '1'): ?>Stock<?php else: ?>Divisor<?php endif; ?></td>
                    <td><?php if ($this->_tpl_vars['point']['ireturn'] == '1'): ?>PR<?php elseif ($this->_tpl_vars['point']['ireturn'] == '2'): ?>Dividend Placeholder<?php else: ?>TR<?php endif; ?></td>
            <td><?php if ($this->_tpl_vars['point']['status'] == 0): ?><span class="label label-important">No!</span><?php else: ?><span class="badge badge-success">Yes</span><?php endif; ?></td>
             <td><?php if ($this->_tpl_vars['point']['dbusersignoff'] == 0): ?><span class="label label-important">No!</span><?php else: ?><span class="badge badge-success">Yes</span><?php endif; ?></td>
              <td><?php if ($this->_tpl_vars['point']['usersignoff'] == 0): ?><span class="label label-important">No!</span><?php else: ?><span class="badge badge-success">Yes</span><?php endif; ?></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-small show-tooltip" title="View" href="index.php?module=caindex&event=view&id=<?php echo $this->_tpl_vars['point']['id']; ?>
">View</a>&nbsp;|&nbsp;
                    <a class="btn btn-small show-tooltip" title="Edit" href="index.php?module=caindex&event=editfornext&id=<?php echo $this->_tpl_vars['point']['id']; ?>
">Edit</a>
                    &nbsp;|&nbsp;
                   <!-- index.php?module=caindex&event=delete&id=<?php echo $this->_tpl_vars['point']['id']; ?>
-->
                    <a class="btn btn-small btn-danger show-tooltip " title="Delete" href="#" id="a1" onclick="confirmdelete(<?php echo $this->_tpl_vars['point']['id']; ?>
)">Delete</a>
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