<?php /* Smarty version 2.6.14, created on 2015-09-04 10:20:25
         compiled from C:/Inetpub/vhosts/ip-198-12-158-159.secureserver.net/httpdocs/testing/ical1.4backup/icai2/templates//formfields/select.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'C:/Inetpub/vhosts/ip-198-12-158-159.secureserver.net/httpdocs/testing/ical1.4backup/icai2/templates//formfields/select.tpl', 4, false),)), $this); ?>
<div class="control-group"><label class="control-label"><?php echo $this->_tpl_vars['formParams']['feild_label'];  if ($this->_tpl_vars['formParams']['is_required'] == '1'): ?> <sup style="color:#F00;">*</sup><?php endif; ?>:</label>
<div class="controls"><select class="input-xlarge" style=" width:283px !important" tabindex="1"  name="<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
" id="<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
" <?php echo $this->_tpl_vars['Form_Params']['feildValues']; ?>
>
	
	<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['formParams']['options'],'selected' => $this->_tpl_vars['formParams']['value']), $this);?>

	
	
	 

</select></div>
<span id="extra_<?php echo $this->_tpl_vars['formParams']['name']; ?>
" style="display:none"></span>
<span id="error_<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
" <?php echo $this->_tpl_vars['formParams']['errorClass']; ?>
><?php echo $this->_tpl_vars['Form_Params']['errorMessage']; ?>
</span></div>