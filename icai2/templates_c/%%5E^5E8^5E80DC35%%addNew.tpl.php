<?php /* Smarty version 2.6.14, created on 2015-08-18 09:49:25
         compiled from reconstitution/addNew.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'reconstitution/addNew.tpl', 7, false),array('block', 'field', 'reconstitution/addNew.tpl', 53, false),)), $this); ?>
<!-- BEGIN Main Content -->
 <?php echo '
 <script>
    function copy_data(val){
     var a = document.getElementById(val.id).value;
	'; ?>

	<?php if (count($this->_tpl_vars['indexdata']) > 0): ?>
	<?php $_from = $this->_tpl_vars['indexdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['point']):
?>
	<?php echo '
	 document.getElementById("startDate_';  echo $this->_tpl_vars['point']['id'];  echo '").value=a;
    '; ?>

	<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	<?php echo '
	}    
	
function toggleme(val) {

 $( "#startDate_"+val ).toggle();
 }

function toggleall(){
	';  if (count($this->_tpl_vars['indexdata']) > 0): ?>
	<?php $_from = $this->_tpl_vars['indexdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['point']):
?>
	<?php echo '
	toggleme(';  echo $this->_tpl_vars['point']['id'];  echo ');
	    '; ?>

	<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>

 
 <?php echo '

}
    </script>
 
 '; ?>

 
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'notice.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>          
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-table"></i> Select Index for Reconstitution</h3>
                                <!--<div style="text-align:right"> <a href="index.php?module=rebalancing&event=download">Download Tickers</a></div>-->
                            </div>
                          
                            	<div class="box-content">
                             <form action="" method="post" onsubmit="confirm('Please check that you have Prepared input file File of selected index.')" onsubmit="return ValidateForm();" enctype="multipart/form-data" class="form-horizontal">
                             
                              
    <?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p'] => $this->_tpl_vars['item']):
?>
               <p>   <?php $this->_tag_stack[] = array('field', array('data' => $this->_tpl_vars['item'],'value' => $this->_tpl_vars['postData'])); $_block_repeat=true;smarty_block_field($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_field($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
                </p>  <?php endforeach; endif; unset($_from); ?>
      
                 
             
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
                                                <?php $_from = $this->_tpl_vars['indexdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['point']):
?>
                                                <tr>
                                                    <td><input type="checkbox" id="checkboxid"  onclick="toggleme(<?php echo $this->_tpl_vars['point']['id']; ?>
);" name="checkboxid[]" value="<?php echo $this->_tpl_vars['point']['id']; ?>
" /></td>
                                                    <td><?php echo $this->_tpl_vars['point']['name']; ?>
</td>
                                                    <td><?php echo $this->_tpl_vars['point']['code']; ?>
</td>
                                                    <td><input style="display:none;" type="text" placeholder="YYYY-MM-DD" name="startDate_<?php echo $this->_tpl_vars['point']['id']; ?>
" id="startDate_<?php echo $this->_tpl_vars['point']['id']; ?>
" value="" class="date-pick date-picker"></td>
                                                    
                                                </tr>
                                                <?php endforeach; endif; unset($_from); ?>
                                             
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