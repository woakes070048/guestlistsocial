<html>
<?
$cakeDescription = __d('cake_dev', 'social.guestlist.net');
?>
<!DOCTYPE html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('loginlayout');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<link href='https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
</li>
</head>

<div id="loading" style="display: none;">
<? echo $this->Html->image('ajax-loader.gif'); ?>
</div>

<body>
	<div id="container">
		<div id='signinwrapper'>
		<span class='haveanaccount'>Have an account?</span>
		<? echo $this->Form->button('Sign In', array('type' => 'button', 'class' => 'loginButton'));?>
		<? echo $this->Form->create('User');
        echo $this->Form->input('email', array('placeholder' => 'Email Address', 'label' => false));
        echo $this->Form->input('password', array('placeholder' => 'Password', 'label' => false));?>
		<h1><?echo $this->Html->link('Forgotten password?', array('action' => 'forgotpw'));?></h1>
        <?echo $this->Form->end('Sign In');?>
        </div>
		<span class='title'>tweet<b>PROOF</b></span>
		<span class='title1'>Social Media Management.</span>
		<div id="content">
	
			<?php echo $this->Session->flash(); ?>
	
			<?php echo $this->fetch('content'); ?>
		</div>
</body>
</html>
