<?php 
echo $this->Session->flash('auth');
?>
<div id="logo">
	<? echo $this->Html->image('loginlogo.png', array('id' => 'loginlogo')); ?>
</div>
<span class='title'>SOCIAL.GUESTLIST.NET</span>
<h1>Forgot your password? Enter your e-mail and we will send you a message with a link to reset your password.</h1>
<?
echo $this->Form->create('User');
        echo $this->Form->input('email', array('placeholder' => 'e-mail address', 'label' => false));
?> <div id="buttons1"> <?
    	echo $this->Form->end('SUBMIT', array('class' => 'forgotpwsubmit'));
?>
</div>