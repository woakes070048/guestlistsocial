<table id='notificationTable'>
	<? foreach ($notifications as $key) { ?>
	<tr class='notification<?echo $key['Notification']['read'];?>'>
		<td class='notificationImage'>
			<img src='<?echo $key['Notification']['icon'];?>' style='vertical-align: middle; height: 30px'>
		</td>
		<td class='notificationBody'>
			<div class='empty'>
			<? echo $key['Notification']['notification']; ?>  <span style='display:block;float:right;'><small><time class='timeago' datetime='<? echo date('c', $key['Notification']['timestamp']); ?>'></time></small></span>
			</div>
		</td>
	</tr>
	<? } ?>
	<tr>
		<? if ($notificationCount > 5) { ?>
		<td class='morenotifications' colspan='2'>
			+ <? echo $notificationCount - 5 ?> more
		</td>
		<? } ?>
	</tr>
	<tr>
		<td class='morenotifications' colspan='2'>
			See all notifications
		</td>
	</tr>
</table>

<script>
$(document).ready(function() {
	$('time.timeago').timeago();
});
</script>