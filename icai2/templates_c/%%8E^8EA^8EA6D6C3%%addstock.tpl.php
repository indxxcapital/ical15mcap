<?php /* Smarty version 2.6.14, created on 2015-12-10 01:03:01
         compiled from replacerunning/addstock.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'field', 'replacerunning/addstock.tpl', 15, false),)), $this); ?>
 <!-- BEGIN Main Content -->
  
 <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notice.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-reorder"></i>Add Stock For Running Index</h3>
                            </div>
                            <div class="box-content">
                             <form action="" method="post" onsubmit="return ValidateForm();" class="form-horizontal">
                             
                              
    <?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p'] => $this->_tpl_vars['item']):
?>
               <p>   <?php $this->_tag_stack[] = array('field', array('data' => $this->_tpl_vars['item'],'value' => $this->_tpl_vars['postData'])); $_block_repeat=true;smarty_block_field($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_field($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
                </p>  <?php endforeach; endif; unset($_from); ?>
 <p>
 	
                 <label>&nbsp;</label>
                   <div id="Div" class="clearfix"></div>
                                        <table class="table table-advance" id="table1">
                                            <thead>
                                                <tr>
                                                   <th style="width:100px"><input type="checkbox" /></th>
                                                    <th>Name</th>
                                                    <th>Code</th>
                                                 <!-- <th title="Weight for Added Security with respect to Indxx ">Weight <sup>?</sup></th>-->
                                                 
                                                  <!--  <th>Go Live Date</th>
                                                    -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $_from = $this->_tpl_vars['indexdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['point']):
?>
                                                <tr>
                                                      <td><input type="checkbox" id="checkboxid"  name="checkboxid[]" value="<?php echo $this->_tpl_vars['point']['id']; ?>
" /></td>
                                                    <td><?php echo $this->_tpl_vars['point']['name']; ?>
</td>
                                                    <td><?php echo $this->_tpl_vars['point']['code']; ?>
</td>
                                                  
                                                     <td>
                                                          <!-- <input type="hidden" name="indxx_id[]" value="<?php echo $this->_tpl_vars['point']['id']; ?>
" />
                                                     <input type="text" name="weight_<?php echo $this->_tpl_vars['point']['id']; ?>
" /></td>-->
                                                 
                                                    <!--<td><?php echo $this->_tpl_vars['point']['dateStart']; ?>
</td>-->
                                                   
                                                    
                                                </tr>
                                                <?php endforeach; endif; unset($_from); ?>
                                             
                                            </tbody>
                                        </table>
                 <div class="form-actions">
                                       <button type="submit" class="btn btn-primary" name="submit" id="submit"><i class="icon-ok"></i> Save</button>
                                       <button type="button" class="btn" name="cancel" id="cancel" onClick="document.location.href='<?php echo $this->_tpl_vars['BASE_URL']; ?>
index.php?module=replacerunningsecurities';" >Back</button>
                                       
                                    </div>
                 
                 
                  
                  </form>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
 <!-- END Main Content -->