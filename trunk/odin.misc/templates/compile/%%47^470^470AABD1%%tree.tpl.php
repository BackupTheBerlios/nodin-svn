<?php /* Smarty version 2.6.6, created on 2005-12-07 21:22:52
         compiled from tree.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "user_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="p-kategorie">
<?php if ($this->_tpl_vars['result']['TreeAction']['type'] == 'cat'): ?>
<h2>Tematy prac</h2>
<?php elseif ($this->_tpl_vars['result']['TreeAction']['type'] == 'titles'): ?>
<h2>Prace</h2>
<?php endif; ?>

<?php if ($this->_tpl_vars['result']['TreeAction']['type'] != 'text'): ?>
<ul>
<?php if (count($_from = (array)$this->_tpl_vars['result']['TreeAction']['result'])):
    foreach ($_from as $this->_tpl_vars['con']):
?>
<li><a href="index.php/prace/<?php echo $this->_tpl_vars['con']['id']; ?>
"><?php echo $this->_tpl_vars['con']['name']; ?>
</a></li>
<?php endforeach; unset($_from); endif; ?>
</ul>
<?php else: ?>
<h2><?php echo $this->_tpl_vars['result']['TreeAction']['result']['name']; ?>
</h2>
<p class="autor">Autor: <?php echo $this->_tpl_vars['result']['TreeAction']['result']['firstname']; ?>
 <?php echo $this->_tpl_vars['result']['TreeAction']['result']['lastname']; ?>
</p>
<?php echo $this->_tpl_vars['result']['TreeAction']['result']['content']; ?>

<?php endif; ?>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>