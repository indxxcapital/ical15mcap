<?php /* Smarty version 2.6.14, created on 2015-08-18 08:39:41
         compiled from header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'block', 'header.tpl', 35, false),)), $this); ?>
<div id="navbar" class="navbar">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <!-- BEGIN Brand -->
                    <a href="<?php echo $this->_tpl_vars['BASE_URL']; ?>
" class="brand">
                        <small>
                            <i class="icon-desktop"></i>
                            ICAI Admin
                        </small>
                    </a>
                    <!-- END Brand -->

                    <!-- BEGIN Responsive Sidebar Collapse -->
                    <a href="#" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
                        <i class="icon-reorder"></i>
                    </a>
                    <!-- END Responsive Sidebar Collapse -->

                    <!-- BEGIN Navbar Buttons -->
                    
                    <ul class="nav flaty-nav pull-right">
                        <!-- BEGIN Button Tasks -->
                        
                        <!-- END Button Tasks -->

                        <!-- BEGIN Button Notifications -->
                        
                       
                        <!-- END Button Notifications -->

                        <!-- BEGIN Button Messages -->
                        
                        <!-- BEGIN Button User -->
                        
                        <?php $this->_tag_stack[] = array('block', array('file' => 'logintab','class' => 'block_logintab')); $_block_repeat=true;smarty_block_block($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_block($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
                        
                        
                        <!-- END Button User -->
                    </ul>
                    <!-- END Navbar Buttons -->
                </div><!--/.container-fluid-->
            </div><!--/.navbar-inner-->
        </div>