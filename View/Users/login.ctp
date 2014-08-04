<?php 
echo $this->Session->flash('auth');
?>
<div id="logo">
	<? echo $this->Html->image('loginlogo.png', array('id' => 'loginlogo')); ?>
</div>
<span class='title'>SOCIAL.GUESTLIST.NET</span>
<?
echo $this->Form->create('User');
        echo $this->Form->input('email', array('placeholder' => 'e-mail address', 'label' => false));
        echo $this->Form->input('password', array('placeholder' => 'password', 'label' => false));
?> <div id="buttons"> <?
    	echo $this->Form->end(__('LOG IN'));
    	echo $this->Form->button('REGISTER', array('type' => 'button', "onclick" => "location.href='/users/register'"));
?>
</div>
<h1><?echo $this->Html->link('Forgotten password?', array('action' => 'forgotpw'));?></h1>