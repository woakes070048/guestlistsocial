<?php 
echo $this->Session->flash('auth');
?>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<div class='form'>
<span>TweetPROOF is the ultimate social media management tool. Perfect for single users or large organizations<br /><br /><br /><br />

<b>Sign up for a free 14 day free trial</b></span>
<?
echo $this->Form->create('Register');
        echo $this->Form->input('email', array('placeholder' => 'Email Address', 'label' => false));
?> <div id="buttons"> <?
    	echo $this->Form->end(__('Sign up for free'));
    	//echo $this->Form->button('REGISTER', array('type' => 'button', "onclick" => "location.href='/users/register'"));
        //echo $this->Form->input('password', array('placeholder' => 'password', 'label' => false));
?>
</div>
</div>
<script>
	$(document).ready(function() {
		$('.loginButton').on('click', function() {
			$('#UserLoginForm').toggle();
		});
	});
</script>