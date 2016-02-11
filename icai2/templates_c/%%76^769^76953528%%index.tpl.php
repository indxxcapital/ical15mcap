<?php /* Smarty version 2.6.14, created on 2015-08-18 08:44:22
         compiled from runindex/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'field', 'runindex/index.tpl', 12, false),)), $this); ?>
<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-reorder"></i>Run Your Index : <?php echo $this->_tpl_vars['indxx']['name']; ?>
 (<?php echo $this->_tpl_vars['indxx']['code']; ?>
)</h3>
                            </div>
                            <div class="box-content">
                             <form action="" method="post" onsubmit="return ValidateForm();" class="form-horizontal">
                             <input type='hidden' name='id' value ="<?php echo $_GET['id']; ?>
">
                              
    <?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p'] => $this->_tpl_vars['item']):
?>
               <p>   <?php $this->_tag_stack[] = array('field', array('data' => $this->_tpl_vars['item'],'value' => $this->_tpl_vars['postData'])); $_block_repeat=true;smarty_block_field($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_field($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
                </p>  <?php endforeach; endif; unset($_from); ?>
 <p>
                 <label>&nbsp;</label>
                 <div class="form-actions">
                                       <button type="submit" class="btn btn-primary" name="db"  value='db' id="submit"><i class="icon-ok"></i>Run Index</button>
                                       
                                       
                                    </div>
                 
                 
                  
                  </form>