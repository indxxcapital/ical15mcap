<?php /* Smarty version 2.6.14, created on 2015-09-01 12:01:38
         compiled from restoreindexlive/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'restoreindexlive/index.tpl', 13, false),)), $this); ?>
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
                                <h3><i class="icon-reorder"></i>Select Index to Restore</h3>
                            </div>
                            <div class="box-content">
                             <form action="" method="post" onsubmit="return ValidateForm();" class="form-horizontal">
                             
                      <?php if (count($this->_tpl_vars['liveindexdata']) > 0): ?>        
    <?php $_from = $this->_tpl_vars['liveindexdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p'] => $this->_tpl_vars['item']):
?>
           <p>     <label> <input type="checkbox" name='index_id[]' value="<?php echo $this->_tpl_vars['item']['id']; ?>
" /> <?php echo $this->_tpl_vars['item']['name']; ?>

                </label>  </p><?php endforeach; endif; unset($_from); ?>
 <p>
                 <label>&nbsp;</label>
                 <div class="form-actions">
                                       <button type="submit" class="btn btn-primary" name="submit" id="submit"><i class="icon-ok"></i> Save</button>
                                       <button type="button" class="btn" name="cancel" id="cancel" onClick="document.location.href='<?php echo $this->_tpl_vars['BASE_URL']; ?>
index.php?module=databaseusers';" >Back</button>
                                       
                                    </div>
                 <?php endif; ?>
                 
                  
                  </form>
                               
                            </div>
                        </div>
                    </div>
                </div>
                
 <!-- END Main Content -->