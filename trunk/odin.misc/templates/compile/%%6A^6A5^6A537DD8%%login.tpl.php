<?php /* Smarty version 2.6.6, created on 2005-12-07 16:25:00
         compiled from login.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="userBox">
<h2>Logowanie</h2><p>
<?php if ($this->_tpl_vars['result']['LoginAction'] == true): ?>
Zostałeś zalogowany!
<?php else: ?>
Błąd. Wpisałeś zły login i/lub hasło.
<?php endif; ?>
</p>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>