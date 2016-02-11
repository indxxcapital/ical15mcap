<?php /* Smarty version 2.6.14, created on 2015-08-18 09:48:54
         compiled from rebalance/download.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'rebalance/download.tpl', 7, false),)), $this); ?>
<!-- BEGIN Main Content -->
 <?php echo '
 <script>
    function copy_data(val){
     var a = document.getElementById(val.id).value;
  
	';  if (count($this->_tpl_vars['indexdata']) > 0): ?>
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
                                                <?php $_from = $this->_tpl_vars['indexdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['point']):
?>
                                                <tr>
                                                    <td><input type="checkbox" id="checkboxid" <?php if ($this->_tpl_vars['point']['selected']): ?>checked<?php endif; ?> name="checkboxid[]" value="<?php echo $this->_tpl_vars['point']['id']; ?>
" /></td>
                                                    <td><?php echo $this->_tpl_vars['point']['name']; ?>
</td>
                                                    <td><?php echo $this->_tpl_vars['point']['code']; ?>
</td>
                                                    <td><?php echo $this->_tpl_vars['point']['dateStart']; ?>
</td>
                                                   
                                                    
                                                </tr>
                                                <?php endforeach; endif; unset($_from); ?>
                                             
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