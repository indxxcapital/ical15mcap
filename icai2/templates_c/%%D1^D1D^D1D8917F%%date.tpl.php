<?php /* Smarty version 2.6.14, created on 2015-08-18 08:44:22
         compiled from C:/Inetpub/vhosts/s184-168-147-126.secureserver.net/httpdocs/ical1.4/icai2/templates//formfields/date.tpl */ ?>
<script type="text/javascript" src="assets/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/assets/bootstrap-datepicker/css/datepicker.css" /> <div class="control-group">
<label class="control-label"><?php echo $this->_tpl_vars['formParams']['feild_label'];  if ($this->_tpl_vars['formParams']['is_required'] == '1'): ?> <sup class="redalert">*</sup><?php endif; ?>:</label>
<div class="controls"><input type="text" placeholder="YYYY-MM-DD" <?php echo $this->_tpl_vars['formParams']['feildValues']; ?>
 name="<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
" id="<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
" value="<?php echo $this->_tpl_vars['formParams']['value']; ?>
" class="date-pick<?php echo $this->_tpl_vars['formParams']['name'];  if ($this->_tpl_vars['formParams']['class'] != ""): ?> <?php echo $this->_tpl_vars['formParams']['class'];  endif; ?> date-picker"/>
<span id="error_<?php echo $this->_tpl_vars['formParams']['feild_code']; ?>
" <?php echo $this->_tpl_vars['formParams']['errorClass']; ?>
><?php echo $this->_tpl_vars['formParams']['errorMessage']; ?>
</span>
</div>
</div>
