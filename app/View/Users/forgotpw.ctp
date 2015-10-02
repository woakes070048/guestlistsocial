<div id="forgotpw">
	<h2>Forgot your password?</h2>
	Enter your e-mail and we will send you a message with a link to reset your password.
	<hr />
	<?
	echo $this->Form->create('User');
	echo $this->Form->input('email', array('placeholder' => 'e-mail address', 'label' => false));
	?>
	<div id="buttons1">
		<?
	    echo $this->Form->end('SUBMIT', array('class' => 'forgotpwsubmit'));
		?>
	</div>
</div>