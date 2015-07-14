<?
echo $this->Form->create('Team');
echo $this->Form->input('id', array('type' => 'select', 'options' => $ddTeams));
echo $this->Form->end('Go');?>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>

<div class='teamsRow'>
	<div class='teamsContainer' style='min-width:0'>
		<div class='teamsContainerHeader'>
			Monthly Performance
		</div>
		<span class='teamsContainerSpan'><b><?echo $monthCount;?></b>tweets</span>
		<small> this month</small>
	</div>
	<div class='teamsContainer' style='min-width:0'>
		<div class='teamsContainerHeader'>
			Weekly Performance
		</div>
		<span class='teamsContainerSpan'><b><?echo $weekCount;?></b>tweets</span>
		<small> this week</small>
	</div>
	<div class='teamsContainer' style='min-width:0'>
		<div class='teamsContainerHeader'>
			Daily Performance
		</div>
		<span class='teamsContainerSpan'><b><?echo $dayCount;?></b>tweets</span>
		<small> today</small>
	</div>
</div>
<div class='teamsRow'>
	<div class='teamsContainer'>
	<div class='teamsContainerHeader'>
	<b>Team Overview:</b>
	</div>
	<div style='float: right; padding: 10px;'>
		<div id="howManyWrittenBlock1" style='float: none; display: inline-block; margin: 0 5px 0 10px;'></div>All Approved
		<div id="howManyWrittenBlock0" style='float: none; display: inline-block; margin: 0 5px 0 10px;'></div>Some still to be Approved
		<div id="howManyWrittenBlock2" style='float: none; display: inline-block; margin: 0 5px 0 10px;'></div>Some need Improving
		<div id="howManyWrittenBlock0" style='float: none; display: inline-block; margin: 0 5px 0 10px; background: none; border: 1px solid #e4e4e4;'></div>Some Tweets missing
	</div>
		<table id='teamOverview' style='border-spacing: 0; padding: 10px;'>
			<tr>
				<th>
				</th>
				<?for ($i=1; $i <= date('t'); $i++) {?>
					<th style='font-size: 8pt;'><?echo date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')));?></th>
				<?}?>
			</tr>
			<?foreach ($tableTweets1 as $key => $value) {?>
				<tr>
					<td class='screenName' style='display:block'><?echo $screen_names[$key];?></td>
					<?for ($i=1; $i <= date('t'); $i++) {?>
							<?
							if (!empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][0])) {								if ($totalCount1[$key]['calendarCount'] == $value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][0]) {
									$class = 'notAllApproved';
								} else {
									$class = '';
								}
							} elseif (!empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1])) {
								if ($totalCount1[$key]['calendarCount'] == $value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1]) {
									$class = 'allApproved';
								} else {
									$class = '';
								}
							} elseif (!empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][2])) {
								if ($totalCount1[$key]['calendarCount'] == $value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][2]) {
									$class = 'improveApproved';
								} else {
									$class = '';
								}
							} else {
									$class = '';
							}?>
						<td class='<?echo $class;?>'>
						</td>
					<?}?>
				</tr>
			<?}?>
		</table>
	</div>
</div>
<div class='teamsRow'>
<?
if (!empty($totalCount1)) {
	foreach ($totalCount1 as $key => $value) {
	if (empty($value[0])) {
		$value[0] = 0;
	}
	if (empty($value[1])) {
		$value[1] = 0;
	}
	if (empty($value[2])) {
		$value[2] = 0;
	}?>	
		<div class='teamsContainer'>
			<span class='screenName'><? echo $value['screen_name'];?></span><br />
			<? $total = $value[0] + $value[1] + $value[2];
			if ($total == 0) {
				$total = 1;
			}?>
			<span class='howManyWritten'><b><?echo $value[0] + $value[1] + $value[2];?>/<?echo $value['calendarCount'] * date('t');?></b> WRITTEN</span>
			<div class='multiProgressBar' style='display:none;'>
				<hr style='width: <? echo ($value[1] / $total) * 300;?>px; background-color: #21a750;' />
				<hr style='width: <? echo ($value[0] / $total) * 300;?>px; background-color: #ffcc00;' />
				<hr style='width: <? echo ($value[2] / $total) * 300;?>px; background-color: #ff0000;' />
			</div>
			<div class='lowerTeamsWrapper'>
				<div class='topTweeters'>
				Top Tweeters:
					<? if (!empty($tweetCount1[$key])) {
						foreach ($tweetCount1[$key] as $key1 => $value1) {?>
							<div style='width: 150px; height: 85px; margin: 5px;'>
								<?echo $this->Html->image($value1['profile_pic'], array('style' => 'height: 25px; vertical-align: middle'));?><span class='topTweetersName'><?echo $value1['name'];?></span>
								<div class='topTweetersNumbers'>
									<div class='topTweetersNumbers1'>
										<div id="howManyWrittenBlock1" style='float: none; display: inline-block;'>
										</div>
										<?
										if (!empty($value1[1])) {
											echo $value1[1];
										} else {
											echo '0';
										}?>
									</div>
									<div class='topTweetersNumbers1'>
										<div id="howManyWrittenBlock0" style='float: none; display: inline-block;'>
										</div>
										<?
										if (!empty($value1[0])) {
											echo $value1[0];
										} else {
											echo '0';
										}?>
									</div>
									<div class='topTweetersNumbers1'>
										<div id="howManyWrittenBlock2" style='float: none; display: inline-block;'>
										</div>
										<?
										if (!empty($value1[2])) {
											echo $value1[2];
										} else {
											echo '0';
										}?>
									</div>
								</div>
							</div>
						<?}?>
					<?}?>
				</div>
				<div class='howManyWritten1'>
					<div><b><?echo $value[1]; ?></b> APPROVED<div id='howManyWrittenBlock1'></div></div>
					<div><b><?echo $value[0]; ?></b> AWAITING APPROVAL<div id='howManyWrittenBlock0'></div></div>
					<div><b><?echo $value[2]; ?></b> NEED IMPROVING<div id='howManyWrittenBlock2'></div></div>
					<div></div>
				</div>
			</div>
		</div>
	<?}?>
<?}?>
</div>

<script>
$(document).ready(function() { 
	$('.multiProgressBar').show('slide');
	$('tr td:nth-child(n + <?echo date("d");?>), tr th:nth-child(n + <?echo date("d");?>)').css('opacity', '1');
});
</script>