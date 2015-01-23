<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>

<!--<div id='team'>
<table>
<th>My Team</th>
	<? foreach ($teamMembers as $key) { ?>
	<tr>
		<td>
			<? if ($key['group_id'] == 1 || $key['group_id'] == 5) {
				$admin = $this->Html->image('user_purple.png', array('url' => array('action' => 'removeadmin', $key['id']), 'style' => 'float:right; margin-left: 5px', 'title' => 'Remove as admin'));
			} elseif ($key['group_id'] == 7) {
				$admin = $this->Html->image('user_light_purple.png', array('url' => array('action' => 'makeadmin', $key['id']), 'style' => 'float: right; margin-left: 5px', 'title' => 'Make admin'));
			} else {
				$admin = $this->Html->image('user_grey.png', array('url' => array('action' => 'makeproofer', $key['id']), 'style' => 'float: right; margin-left: 5px', 'title' => 'Make proofer'));
			}
			$x = $this->Html->image('false1.png', array('url' => array('action' => 'removeFromTeam', $key['id']), 'style' => 'float: right; margin-left: 5px', 'title' => 'Remove from team', 'onclick' => 'confirm("Are you sure you want to remove this person from your team?");'));
			echo $key['first_name'] . $x . $admin . '<br />';?>
		</td>
	</tr>
	<? } ?>
	<?php if ($this->Session->read('Auth.User.Team.id') == 0) {echo '<tr><td>' . $this->Html->link('Part of a marketing team?', '/teams/manage') . '</td></tr>';}?>
</table>
<? if ($this->Session->read('Auth.User.Team.id') != 0) {
	if ($this->Session->read('Auth.User.group_id') == 1 || $this->Session->read('Auth.User.group_id') == 5) {
		echo '<small>' . $this->Html->link('Invite', '/teams/invite') . '</small> <br />';
		echo '<small>' . $this->Html->link('Manage Team', '/teams/manageteam') . '</small> <br />';
		echo '<small>' . $this->Html->link('Manage Tweets', '/twitter/index') . '</small>';
	}
	}?>
</div>-->

<div id='leftpanel'>
<h2 class='smallerheader'>MANAGE YOUR TEAMS</h2>
<?
echo $this->Form->create('filterAccount');
echo $this->Form->input('account', array(
	'label' => false,
	'onchange' => 'this.form.submit()',
	'options' => array('empty' => 'Select by Twitter Account', array_combine($dropdownaccounts,$dropdownaccounts))));
echo $this->Form->end();

echo $this->Form->create('filterTeam');
echo $this->Form->input('team', array(
	'label' => false,
	'onchange' => 'this.form.submit()',
	'options' => array('empty' => 'Select by Team', $dropdownteams)));
echo $this->Form->end();
?>

<? echo $this->Html->link('<p> ADD TEAM </p>', '/teams/manage', array('class' => 'addteam', 'escape' => false));?>

<? echo $this->Form->create('Teams', array('action' => 'permissionSave'));?>
<div id='selectall'>
<? echo $this->Form->input('Select All', array('type' => 'checkbox', 'class' => 'selectAll', 'label' => 'Select All')); ?>
</div>

<?if (isset($accountTable)) { //If they are filtering by account show this table?>
<h1 id='manageteamtext'> <? echo strtoupper($currentAccount); ?> </h1>
<table class='permissionstable'>

<? foreach ($users as $key) {?>
	<tr>
		
	<? 
		if (in_array($twitter_account_id, $key['permissions'])) {
			$checked = 'checked';
			$value = $twitter_account_id;
		} else {
			$checked = '';
			$value = $twitter_account_id;
			//$value = 0;
		}

		echo '<td>' . $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', $checked, 'label' => $key['name'], 'name' => 'data[Teams]['.$key['team_id'].'][permissions]['.$value.']', 'value' => $key['team_id'])) . '</td>';
		echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $key['team_id'], 'name' => 'data[Teams]['.$key['team_id'].'][team_id]'));?>
	</tr><?
	
}
?>
</table>

<?} elseif (isset($teamTable)) { //If they are filtering by team show this table?>
<h1 id='manageteamtext'> ACCOUNTS </h1> <p class='floatleft'>| </p><h5 id='manageteamtext1'>USERS</h5>
<table class='permissionstable' id='permissionstable'>
<? 
foreach ($accounts as $key) {?>
	<tr>
		
	<?
		if (in_array($key['TwitterAccount']['account_id'], $users['permissions'])) {
			$checked = 'checked';
			$value = $key['TwitterAccount']['account_id'];
		} else {
			$checked = '';
			$value = $key['TwitterAccount']['account_id'];
			//$value = 0;
		}

		echo '<td>' . $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', $checked, 'label' => $key['TwitterAccount']['screen_name'], 'name' => 'data[Teams]['.$value.'][permissions]['.$value.']', 'value' => $users['team_id'])) . '</td>';
		echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $users['team_id'], 'name' => 'data[Teams]['.$value.'][team_id]'));?>
	</tr><?
}?>
</table>
<table class='permissionstable' id='teamMembers'>
<? foreach ($teamMembers as $key) { ?>
	<tr>
		<td>
		<? if ($key['TeamsUser']['group_id'] == 1 || $key['TeamsUser']['group_id'] == 5) {
				$admin = $this->Html->image('user_purple.png', array('url' => array('action' => 'removeadmin', $key['id'], $currentTeamId), 'style' => 'float:right; margin-left: 5px', 'title' => 'Remove as admin'));
			} elseif ($key['TeamsUser']['group_id'] == 7) {
				$admin = $this->Html->image('user_light_purple.png', array('url' => array('action' => 'makeadmin', $key['id'], $currentTeamId), 'style' => 'float: right; margin-left: 5px', 'title' => 'Make admin'));
			} else {
				$admin = $this->Html->image('user_grey.png', array('url' => array('action' => 'makeproofer', $key['id'], $currentTeamId), 'style' => 'float: right; margin-left: 5px', 'title' => 'Make proofer'));
			}
			$x = $this->Html->image('false1.png', array('url' => array('action' => 'removeFromTeam', $key['id'], $currentTeamId), 'style' => 'float: right; margin-left: 5px', 'title' => 'Remove from team', 'onclick' => 'confirm("Are you sure you want to remove this person from your team?");'));
			echo $key['first_name'] . $x . $admin . '<br />';?>
		</td>
	</tr>
<?}?>
</table>
<?
echo $this->Html->link('<p class="ta"> ADD TWITTER ACCOUNT </p>', '/twitter/connect', array('class' => 'addteam', 'escape' => false));
echo $this->Html->link('<p class="iu"> INVITE USER </p>', '/teams/invite', array('class' => 'addteam', 'escape' => false));
}
?>
<hr style='width:95%; background-color:#bbb; display: block; margin: auto;'></hr>
<? echo $this->Form->submit('Submit changes', array('class' => 'addteam')); ?>
</div>
<hr style='width: 1px; height: 1500px; float: left; margin: 0; background-color: #dcdcdc'>

<div id='rightpanel'>
<div id='teamspanel'>
	Top Tweeters
	<table>
	<th>Name</th>
	<th><? echo date('D', strtotime('-6 day'));?></th>
	<th><? echo date('D', strtotime('-5 day'));?></th>
	<th><? echo date('D', strtotime('-4 day'));?></th>
	<th><? echo date('D', strtotime('-3 day'));?></th>
	<th><? echo date('D', strtotime('-2 day'));?></th>
	<th><? echo date('D', strtotime('-1 day'));?></th>
	<th><? echo date('D', strtotime('-0 day'));?></th>
	<th>7 day total</th>

	<? foreach ($counts as $key) {?>
	<tr>
		<td><? echo $key['name'] ?> </td>
		<td><? echo $key[6] ?> </td>
		<td><? echo $key[5] ?> </td>
		<td><? echo $key[4] ?> </td>
		<td><? echo $key[3] ?> </td>
		<td><? echo $key[2] ?> </td>
		<td><? echo $key[1] ?> </td>
		<td><? echo $key[0] ?> </td>
		<td><? echo $key['sum'] ?> </td>
	</tr>
	<?}?>
	</table>
</div>
</div>


<script>
$(document).ready(function () { 
	$('#teamMembers').hide();
	$('#leftpanel').on('click', '#manageteamtext1', function() {
		$('#teamMembers').toggle();
		$('#permissionstable').toggle();
		$('#manageteamtext').replaceWith('<h5 id="manageteamtext2" style="float:left">' + 'ACCOUNTS' + '</h5>');

		$('#manageteamtext1').replaceWith('<h1 id="manageteamtext">' + 'USERS' + '</h1>');
	});

	$('#leftpanel').on('click', '#manageteamtext2', function() {
		$('#teamMembers').toggle();
		$('#permissionstable').toggle();
		$('#manageteamtext').replaceWith('<h5 id="manageteamtext1">' + 'USERS' + '</h5>');

		$('#manageteamtext2').replaceWith('<h1 id="manageteamtext">' + 'ACCOUNTS' + '</h1>');
	});


	
	$('#selectall').on('change', '.selectAll', function(e) {
	  if(this.checked) {
	      // Iterate each checkbox
	      $(".aCheckbox").prop('checked', this.checked);
	  }
	  else {
	    $(".aCheckbox").prop('checked', this.checked);
	  }
	});
});
</script>
