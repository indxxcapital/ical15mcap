<?php /* Smarty version 2.6.14, created on 2015-09-04 12:18:25
         compiled from replacetempupcoming/add3.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'field', 'replacetempupcoming/add3.tpl', 19, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notice.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<div class="row-fluid">
                    <div class="span22">
                        <div class="box box-magenta">
                            <div class="box-title">
                                <h3><i class="icon-reorder"></i>Add New Security for index <?php echo $this->_tpl_vars['sessData']['NewIndxxName']; ?>
</h3>
                                <div class="box-tool">
                              <a href="<?php echo $this->_tpl_vars['BASE_URL']; ?>
index.php?module=replacetempupcoming&event=uploadSecuritiesforreplacement&indxx_id=<?php echo $_GET['indxx_id']; ?>
&reqid=<?php echo $_GET['reqid']; ?>
">Upload Securities</a>
                                   </div>
                            </div>
                            <form class="form-wizard" method="post"> 
                            <div class="box-content done"  id="p_scents">
                                
                               <?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p'] => $this->_tpl_vars['item']):
?>
             <?php if ($this->_tpl_vars['p']%12 == 0): ?>  <div class="controls step controls-row">
                                    <span class="formnumber"><?php echo $this->_tpl_vars['p']/12+1; ?>
</span>
            <?php endif; ?>                         <?php $this->_tag_stack[] = array('field', array('data' => $this->_tpl_vars['item'],'value' => $this->_tpl_vars['postData'])); $_block_repeat=true;smarty_block_field($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_field($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
                                      
                <?php if ($this->_tpl_vars['p']%12 == 11): ?>  </div><?php endif; ?>                    
                 <?php endforeach; endif; unset($_from); ?>    
                            </div>
                            
                            <div class="box-content" >
                            <div class="form-actions">
                                        <button class="btn btn-primary"  id="addScnt" type="submit"><i class="icon-plus"></i> Add More</button>
                                          <button class="btn btn-primary" name='submit' value="submit" type="submit"><i class="icon-ok"></i> Save</button>
                                       <button class="btn" type="button">Cancel</button>
                                    </div>
                            </div>
                          <!--  <h2><a href="#" id="addScnt">Add Another Input Box</a></h2>-->
                        <input type="hidden" id="totalfields" name="totalfields" value="<?php echo $this->_tpl_vars['totalfields']; ?>
" />
                        </form>
                        </div>
                    </div>
                </div>
                
                
                
                
<?php echo '
<script type="text/javascript">

$(function() {
        var scntDiv = $(\'#p_scents\');
        var i = $(\'#p_scents div\').size() + 1;
        $(\'#addScnt\').click( function() {
             $(\'<div class="controls step  controls-row"> <span class="formnumber">\'+i+\'</span><input type="text" placeholder="Security Name" class="span1" name="name[\'+i+\']"><input type="text" name="isin[\'+i+\']" placeholder="ISIN" class="span1"><input  name="ticker[\'+i+\']" type="text" placeholder="Ticker" class="span1"><input name="curr[\'+i+\']" type="text" placeholder="Currency" class="span1"><input type="text" placeholder="DivCurrency"  name="divcurr[\'+i+\']" class="span1"><input name="sedol[\'+i+\']" type="text" placeholder="Sedol" class="span1"><input name="cusip[\'+i+\']" type="text" placeholder="Cusip" class="span1"><input name="countryname[\'+i+\']" type="text" placeholder="Country Name" class="span1"><input name="sector[\'+i+\']" type="text" placeholder="Sector" class="span1"><input name="industry[\'+i+\']" type="text" placeholder="Inustry" class="span1"><input name="subindustry[\'+i+\']" type="text" placeholder="SubInustry" class="span1">\').appendTo(scntDiv);
                $(\'#totalfields\').val(i);
				i++;
                return false;
        });

});
</script>
'; ?>
                