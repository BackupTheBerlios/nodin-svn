<?php /* Smarty version 2.6.6, created on 2005-12-07 16:23:33
         compiled from user_menu.tpl */ ?>
<div id="userBox">
<?php if (! $this->_tpl_vars['user']->isAuthenticated()): ?>
<h2>Zaloguj</h2>
<form action="index.php/zaloguj" method="post" id="f-login">
<fieldset>
	<label for="login">Login</label><input id="login" name="login" type="text" />
	<label for="password">Hasło</label><input id="password" name="password" type="password" />
	<input type="submit" name="zaloguj" value="Zaloguj!"/>
</fieldset>
</form>
<?php else: ?>
<h2>Panel użytkownika</h2>
<ul>
	<li>Witaj, <?php echo $this->_tpl_vars['user']->getName(); ?>
</li>
	<li><a href="index.php/wyloguj">Wyloguj</a></li>
</ul>
<?php endif; ?>
</div>