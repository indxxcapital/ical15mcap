<?php /* Smarty version 2.6.14, created on 2015-12-09 00:24:18
         compiled from C:/xampp/htdocs/2016new/icai2/templates//formfields/radio.tpl */ ?>
<!--
<?php $_from = $this->_tpl_vars['Form_Params']['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['item']):
?>
	 
	<label><input style="width:10px" type="radio" name="<?php echo $this->_tpl_vars['Form_Params']['name']; ?>
" id="<?php echo $this->_tpl_vars['Form_Params']['name']; ?>
_<?php echo $this->_tpl_vars['k']; ?>
" value="<?php echo $this->_tpl_vars['k']; ?>
" class="<?php echo $this->_tpl_vars['Form_Params']['class']; ?>
"
    <?php if ($this->_tpl_vars['Form_Params']['value'] == $this->_tpl_vars['k']): ?> checked="checked"<?php endif; ?>/> <?php echo $this->_tpl_vars['item']; ?>
</label>
	<?php endforeach; endif; unset($_from); ?>

<span id="error_<?php echo $this->_tpl_vars['Form_Params']['name']; ?>
" <?php echo $this->_tpl_vars['Form_Params']['errorClass']; ?>
><?php echo $this->_tpl_vars['Form_Params']['errorMessage']; ?>
</span>-->


<label class="control-label"><?php echo $this->_tpl_vars['formParams']['feild_label'];  if ($this->_tpl_vars['formParams']['is_required']): ?><sup class="redalert">*</sup><?php endif; ?></label>

<?php $_from = $this->_tpl_vars['formParams']['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['item']):
?>
	<label></label><div class="controls"><input style="width:10px" type="radio" name="<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
" id="<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
_<?php echo $this->_tpl_vars['k']; ?>
" value="<?php echo $this->_tpl_vars['k']; ?>
" class="<?php echo $this->_tpl_vars['formParams']['class']; ?>
" <?php if ($this->_tpl_vars['formParams']['value'] == $this->_tpl_vars['k']): ?> checked="checked"<?php endif; ?> <?php echo $this->_tpl_vars['formParams']['feildValues']; ?>
/> <?php echo $this->_tpl_vars['item']; ?>
</div>
	<?php endforeach; endif; unset($_from); ?>
    
    </span>

<span id="error_<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
" <?php echo $this->_tpl_vars['formParams']['errorClass']; ?>
><?php echo $this->_tpl_vars['formParams']['errorMessage']; ?>
</span>