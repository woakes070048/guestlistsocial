<?php 
echo $this->Session->flash('auth');
?>
<div id="logo">
	<? echo $this->Html->image('loginlogo.png', array('id' => 'loginlogo')); ?>
</div>
<span class='title'>SOCIAL.GUESTLIST.NET</span>
<h1>Reset your password</h1>
<? echo $this->Form->create('User');
    echo $this->Form->input('password', array('type' => 'password', 'label' => false, 'placeholder' => 'new password'));
    echo $this->Form->input('password2', array('type' => 'password', 'label' => false, 'placeholder' => 'confirm password'));?>
<div id="buttons1">
   <? echo $this->Form->end(__('Reset Password')) ?>
</div>
