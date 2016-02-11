<?php /* Smarty version 2.6.14, created on 2015-08-20 15:00:48
         compiled from caindex/view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'caindex/view.tpl', 127, false),)), $this); ?>
<!-- BEGIN Main Content -->
 <?php echo '
 <script type=\'text/javascript\'>
 
   function confirmdelete(id)
 {

 var temp=confirm("Are you sure you want to delete this record ! All index related data will be deleted!")
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
    url : "index.php?module=caindex&event=deleteindex",
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
                                <h3><i class="icon-table"></i>Index Details </h3>
                            </div>
                            
                           
                            
                            
                            
                            
                            
                            <div class="box-content">
                            
                            <table class="table table-striped table-hover fill-head">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Investment Ammount</th>
                                            <th>Initial Divisor</th>
                                            <th>Currency</th>
                                            <th>Type</th>
                                            <th>Start Date</th>
                                           
                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                    <?php $_from = $this->_tpl_vars['viewindexdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['point']):
?>
        <tr>
             <td></td>
            <td><?php echo $this->_tpl_vars['point']['name']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['code']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['investmentammount']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['divisor']; ?>
</td>
                <td><?php echo $this->_tpl_vars['point']['curr']; ?>
</td>
                    <td><?php echo $this->_tpl_vars['point']['indexname']; ?>
</td>
                     <td><?php echo $this->_tpl_vars['point']['dateStart']; ?>
</td>
          
            
        </tr>
        <?php endforeach; endif; unset($_from); ?>
                                    
                                    
                                       
                                    </tbody>
                                </table>
                            
                            <?php if (count($this->_tpl_vars['lastCloseData']) > 0): ?>
                             <div class="clearfix"></div>
                                 <div class="box">
                                 <div class="box-title">
                                <h3><i class="icon-table"></i>Last Close Index Data</h3>
                            </div>
                              </div>  
                                
                                <div class="clearfix"></div>
                           <table class="table table-striped table-hover fill-head">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Market Value</th>
                                            <th>Index Value</th>
                                            <th>Divisor</th>
                                            <th>Date</th>
                                            
                                            
                                          
                                        </tr>
                                    </thead>
                                    <tbody><tr>
                                      <td><?php echo $this->_tpl_vars['lastCloseData']['code']; ?>
</td>
                                            <td><?php echo $this->_tpl_vars['lastCloseData']['market_value']; ?>
</td>
                                            <td><?php echo $this->_tpl_vars['lastCloseData']['indxx_value']; ?>
</td>
                                            <td><?php echo $this->_tpl_vars['lastCloseData']['newdivisor']; ?>
</td>
                                            <td><?php echo $this->_tpl_vars['lastCloseData']['date']; ?>
</td>
                                    </tr></tbody>
                                    </table>
                            <?php endif; ?>
                                
                                <div class="clearfix"></div>
                                 <div class="box">
                                 <div class="box-title">
                                <h3><i class="icon-table"></i>Total <?php echo $this->_tpl_vars['totalindexSecurityrows']; ?>
 securities found</h3>
                            </div>
                              </div>  
                                
                                <div class="clearfix"></div>
<table class="table table-advance">
    <thead>
        <tr>
           <th>#</th>
             <th>Name</th>
              <th>Ticker</th>
              <th>ISIN</th>   
                    <th>Sedol</th>  
                          <th>Cusip</th>  
                                <th>Country</th>      
               <th>Weight</th>
			   <th>Last Converted Price</th>
			   <th>Share</th>
              <th>Currency</th>  <th>Div Currency</th><!--<th style="width:100px">Submit adjustment factor</th>-->
              
            <!--<th>Effective Date</th>
            <th>Announce Date</th>-->
         </tr>
    </thead>
    <tbody>
    
    <?php if (count($this->_tpl_vars['indexSecurity']) > 0): ?>
    	<?php $_from = $this->_tpl_vars['indexSecurity']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['point']):
?>
        <tr>
          <td><?php echo $this->_tpl_vars['k']+1; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['name']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['ticker']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['isin']; ?>
</td>
              <td><?php echo $this->_tpl_vars['point']['sedol']; ?>
</td>
                <td><?php echo $this->_tpl_vars['point']['cusip']; ?>
</td>
                  <td><?php echo $this->_tpl_vars['point']['countryname']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['weight']; ?>
</td>
			<td><?php echo $this->_tpl_vars['point']['price']; ?>
</td>
			<td><?php echo $this->_tpl_vars['point']['share']; ?>
</td>
            <td><?php echo $this->_tpl_vars['point']['curr']; ?>
</td>
             <td><?php echo $this->_tpl_vars['point']['divcurr']; ?>
</td>
            <!-- <td>
             <div class="btn-group">
                    
                    <a class="btn btn-small show-tooltip" title="Adj Factor" href="index.php?module=caindex&event=subadjfactor&id=<?php echo $this->_tpl_vars['point']['id']; ?>
&indxx_id=<?php echo $_GET['id']; ?>
"><i class="icon-edit"></i></a>
                    
                  
                </div></td>
-->
        </tr>
        <?php endforeach; endif; unset($_from); ?>
        <?php else: ?>
        <tr>
        <td colspan="5" align="center">There is No Securities in this indxx.</td>
        </tr>
        <?php endif; ?>
    
    </tbody>
</table>

 <table class="table table-advance">   <tr><td>
                                  
  <a href="index.php?module=caindex&event=exportlive&id=<?php echo $this->_tpl_vars['viewindexdata']['0']['id']; ?>
"><button class="btn btn-warning">Export Index</button></a>
        <a href="index.php?module=caindex"><button class="btn btn-inverse">Back</button></a>
                                    
                                     
                            </td>
                                     </tr>
                                    
                                    
                                    </table>

                            
                            
                           
                             
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                  <!-- END Main Content -->