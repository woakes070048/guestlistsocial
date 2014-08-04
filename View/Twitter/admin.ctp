	<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
	<script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
	<?php echo $this->Html->script('charCount');
	echo $this->Html->script('jquery-ui-1.10.3.custom'); 
	echo $this->Html->script('jquery-ui-timepicker-addon');
	echo $this->Html->script('jquery.urlshortener');
	echo $this->Html->css('calendar'); ?>

<?php
echo $this->Session->flash('auth');
?>

<?
//Select Twitter Account
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
    	)); 
echo $this->Form->end();?>

<div id="editinfobar">
<?
if (isset($info[0]['TwitterAccount']['infolink'])) {
echo $this->Html->link($this->Html->image('star.png', array('title' => 'Useful Link')), $info[0]['TwitterAccount']['infolink'], array('target' => '_blank', 'escape' => false));
}
if ($this->Session->read('access_token.screen_name')) {
	//echo $this->Html->link('Edit Info | ', '/twitter/info');
	//echo $this->Html->link('Editorial Calendar', '/twitter/calendar/0');
	echo $this->Html->image('calendar.png', array('url' => '/twitter/calendar/0', 'title' => 'Editorial Calendar'));
}
echo $this->Html->image('twitter_add.png', array('url' => '/twitter/connect', 'title' => 'Add Twitter Account'));
?>
</div>

<div id="teamwrapper">
<div id='teamicon'>
<? echo $this->Html->image('group.png'); ?>
</div>
<div id='team'>
<table>
<th>My Team</th>
	<? foreach ($teamMembers as $key) { ?>
	<tr>
		<td>
			<? if ($key['User']['group_id'] == 1 || $key['User']['group_id'] == 5) {
				$admin = '<i> - admin </i>';
			} else {
				$admin = '';
			}
			echo $key['User']['first_name'] . $admin;?>
		</td>
	</tr>
	<? } ?>
	<?php if ($this->Session->read('Auth.User.Team.id') == 0) {echo '<tr><td>' . $this->Html->link('Part of a marketing team?', '/teams/manage') . '</td></tr>';}?>
</table>
<div id="teambuttons">
<? if ($this->Session->read('Auth.User.Team.id') != 0) {
		echo $this->Html->image('groupadd.png', array('url' => '/teams/invite', 'title' => 'Invite'));
	}
	echo $this->Html->image('twitter_add.png', array('url' => '/twitter/connect', 'title' => 'Add Twitter Account'));?>
</div>
</div>
</div>

<h2>Write and Schedule Tweets</h2>
<hr>

<div id='addTweetWrapper' style="display: none;">
<?php
//Add Tweet
echo $this->Form->create('Tweet', array('url' => array('controller' => 'twitter', 'action' => 'testing'), 'id' => 'submitTweet'));
		//URL Shortener
		echo $this->Form->button('Shorten all URLs', array('id' => 'shortIt', 'class' => 'urlSubmit', 'type' => 'button'));

		echo $this->Form->textarea('body', array('label' => false, 'type' => 'post', 'class' => 'ttt'));
echo $this->Form->end(array('id' => 'tweetsubmit', 'value' => 'AddTweet', 'label' => 'ADD TO QUEUE')); // add new form with hidden input fields to tweet now
?>
</div>

<!--Table goes here -->
<div id='table'>
</div>
<div id='table1'>
</div>
<?php
//Select Twitter Account
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
    	)); 
echo $this->Form->end();?>

<!-- SCRIPTS -->
<script> 
        // wait for the DOM to be loaded 
        $(document).ready(function () { 
        	
			<? if ($this->Session->read('Auth.User.calendar_activated') == 1) {
				if ($this->Session->read('access_token.account_id') !== null) {?>
				$('#container').css('opacity', '.4');
				$('#loading').show();
				$('#table1').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
					$('#addTweetWrapper').load("/editorial_calendars/editorialrefresh");
					$('#addTweetWrapper').show();
					$('#container').css('opacity', '1');
					$('#loading').hide();
				});
				<?}?>
			<?} else {?>
				$('#container').css('opacity', '.4');
				$('#loading').show();
				$('#table').load('/twitter/tablerefresh', function() {
					$('#addTweetWrapper').show();
					$('#container').css('opacity', '1');
					$('#loading').hide();
				});
			<?}?>
			
            // bind 'myForm' and provide a simple callback function 
            $('#submitTweet').ajaxForm(function(options) { 
 				$("#TweetBody").val("");
 				$("#table").load("/twitter/tablerefresh", function() {
 					$('.schedule').each(function(){
						$(this).datetimepicker({
    						dateFormat: 'dd-mm-yy',
    						altFormat: '@',
						});
    				});

 				});
			});

        	$("#table").on("change", ".TwitterVerified" , function() {
        		$("#table").css('opacity', '.4');
        		if (this.checked == 'checked') {
        			id = $(this).attr('id');
        			$("#" + id + "_" + "<? echo $this->Session->read('Auth.User.first_name'); ?>").prop('disabled', false);
        		}
        		$('#edit').ajaxSubmit();
        		setTimeout(refresh, 100);//delaying the table refresh so that the form can successfully submit into the databases
        		function refresh() {
        			$('#table').load('/twitter/tablerefresh', function() {
  					$("#table").css('opacity', '1');
  					$('.schedule').each(function(){
						$(this).datetimepicker({
    						dateFormat: 'dd-mm-yy',
    						altFormat: '@',
						});
    				});
				});
        		};
        		
 			});
        	//Submit table form on delete button click
        	$("#table").on("click", ".delete", function() {
        		$("#table").css('opacity', '.4');
        		id = $(this).attr('id');
        		$.ajax({url: "/twitter/delete/" + id});
        		setTimeout(refresh1, 100);
        		function refresh1() {
	     			$('#table').load('/twitter/tablerefresh', function() {
	  					$("#table").css('opacity', '1');
	  					$('.schedule').each(function(){
							$(this).datetimepicker({
	    						dateFormat: 'dd-mm-yy',
	    						altFormat: '@',
							});
	    				});
					});
     			};
			});

			$("#TweetBody").charCount();
			//Hiding and showing tweet body input on click
			$("#table").on("click", ".tweetbody", function() {
				id = $(this).attr('id');
				$("#" + id + " .notediting").hide();
				$("#" + id + " .editing").show();
				$("#" + id + " .editing").focus();
			});

			$("#table").on("click", ".time", function() {
				id = $(this).attr('id');
				if ($("#" + id + " .schedule").length) {
						$("#" + id + " .notediting").hide();
				}
				$("#" + id + " .schedule").show();
				$("#" + id + " .schedule").focus();
				$("#" + id + " .schedule").css("margin-bottom", "1em");
			});

			$(".editing").blur(function(){
				id = $(this).parent().attr('id');
				$("#" + id + " .editing").hide();
				value = $("#" + id + " .editing").val();
				$("#" + id + " .notediting").text(value)
				$("#" + id + " .notediting").show();
			});

			$(".schedule").blur(function(){
				id = $(this).parent().parent().attr('id');
				$("#" + id + " .schedule").hide();
				value = $("#" + id + " .schedule").val();
				$("#" + id + " .notediting").text(value)
				$("#" + id + " .notediting").show();
			});


			$('#AddTweet').attr('disabled','disabled');
			//disabing addtweet button if tweet is empty
			$('#TweetBody').bind('keyup', function() { 
				var nameLength = $("#TweetBody").val().length;

				if (0 < nameLength) {
				   $('#AddTweet').removeAttr('disabled');
				   if (nameLength > 140) {
				   	$('#AddTweet').attr('disabled','disabled');
				   }
				}
			});

    		jQuery.urlShortener.settings.apiKey = 'AIzaSyC27e05Qg5Tyghi1dk5U7-nNDC0_wift08';
			$("#shortIt").click(function () {
    			//$("#shortUrlInfo").html("<img src='images/loading.gif'/>");
    			regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
    			var longUrlLink = $("#TweetBody").val().match(regex);
    			//split = longUrlLink.split(",");
    			//alert(split[1]);
    			jQuery.urlShortener({
        			longUrl: longUrlLink,
        			success: function (shortUrl) {
            			$("#TweetBody").val($("#TweetBody").val().replace(longUrlLink, shortUrl));
        			},
        			error: function(err) {
        				$("#shortUrlInfo").html(JSON.stringify(err));
        			}
    			});
			});

			$("#team").hide();
			$("#teamicon").on('click', function() {
				$("#team").toggle( "slide", {direction:"right"} );
			});
        });

</script>