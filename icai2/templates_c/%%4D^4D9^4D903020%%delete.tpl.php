<?php /* Smarty version 2.6.14, created on 2015-08-19 15:04:17
         compiled from caindex/delete.tpl */ ?>
 <!-- BEGIN Main Content -->

<div class="row-fluid">
                    <div class="span12">
                        <div class="box">
                            <div class="box-title">
                                <h3><i class="icon-bell-alt"></i></h3>
                                       </div>
                            <div class="box-content">
                                <div class="row-fluid">
                                    
                                    <div class="span6">
                                        
                                        <div class="alert alert-success">
                                            <button class="close" data-dismiss="alert">&times;</button>
                                            <h4>Success!</h4>
                                            <p>The index <?php echo $this->_tpl_vars['deleteddata']['name']; ?>
 has been Deleted.</p>
                                        </div>
                                        
                                          <form action="" method="post" onsubmit="return ValidateForm();" class="form-horizontal">
                 
                                      
                                       <button type="button" class="btn" name="back" id="back"  onClick="document.location.href='<?php echo $this->_tpl_vars['BASE_URL']; ?>
index.php?module=caindex';">Back</button>
                                    
                 
                 
                  
                  </form>
                                    </div>
                                    
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
 <!-- END Main Content -->