<div id="register">
<h2>Sign up to TweetProof</h2>
<hr />
<?php echo $this->Form->create('User'); ?>
        <?php echo $this->Form->input('first_name', array('placeholder' => 'First name', 'label' => false));
        echo $this->Form->input('last_name', array('placeholder' => 'Last name', 'label' => false));
        echo $this->Form->input('email', array('value' => $email, 'label' => false, 'placeholder' => 'E-mail'));
        echo $this->Form->input('password', array('type' => 'password', 'label' => false, 'placeholder' => 'Password'));
        echo $this->Form->input('password2', array('type' => 'password', 'label' => false, 'placeholder' => 'Confirm Password'));
        echo $this->Form->input('GMT_offset', array(
    	'options' => array(
    		  '-12.0' => '(GMT -12:00) Eniwetok, Kwajalein',
		      '-11.0' => '(GMT -11:00) Midway Island, Samoa',
		      '-10.0' => '(GMT -10:00) Hawaii',
		      '-9.0' => '(GMT -9:00) Alaska',
		      '-8.0' => '(GMT -8:00) Pacific Time (US &amp; Canada)',
		      '-7.0' => '(GMT -7:00) Mountain Time (US &amp; Canada)',
		      '-6.0' => '(GMT -6:00) Central Time (US &amp; Canada), Mexico City',
		      '-5.0' => '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima',
		      '-4.0' => '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz',
		      '-3.5' => '(GMT -3:30) Newfoundland',
		      '-3.0' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
		      '-2.0' => '(GMT -2:00) Mid-Atlantic',
		      '-1.0' => '(GMT -1:00 hour) Azores, Cape Verde Islands',
		      '0.0' => '(GMT) London, Western Europe Time, Lisbon, Casablanca',
		      '1.0' => '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris',
		      '2.0' => '(GMT +2:00) Kaliningrad, South Africa',
		      '3.0' => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
		      '3.5' => '(GMT +3:30) Tehran',
		      '4.0' => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
		      '4.5' => '(GMT +4:30) Kabul',
		      '5.0' => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
		      '5.5' => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi',
		      '5.75' => '(GMT +5:45) Kathmandu',
		      '6.0' => '(GMT +6:00) Almaty, Dhaka, Colombo',
		      '7.0' => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
		      '8.0' => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong',
		      '9.0' => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
		      '9.5' => '(GMT +9:30) Adelaide, Darwin',
		      '10.0' => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
		      '11.0' => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
		      '12.0' => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka',),
    	'empty' => 'Select Timezone...',
    	'label' => false,
    	'default' => '0.0',
    	'type' => 'select',
));
    ?><br />
    <h1>By clicking submit you agree to our <a href="https://docs.google.com/document/d/1A8d9CURdtyMaj77xbQ_Dux2vuOGSIB4h-qjSynP74Hc/pub">terms and conditions</a></h1><br />
<?php echo $this->Form->end(__('Submit')); ?>
<script>
$(document).ready(function() {
	$('select').dropdown();
});
</script>
</div>