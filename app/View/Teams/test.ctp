<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<?
echo $this->Html->script('Chart.min');
echo $this->Html->script('jquery.qtip.min');
echo $this->Html->css('jquery.qtip.min');
?>

<?
$base = strtotime(date('Y-m-d',time()) . '-01 00:00:01');
echo $this->Form->create('Team');
echo $this->Form->input('id', array('type' => 'select', 'options' => $ddTeams));
echo $this->Form->input('Select Month', array(
    'options' => array(
        0 => date('F Y', strtotime('+0 month', $base)),
        1 => date('F Y', strtotime('+1 month', $base)),
        2 => date('F Y', strtotime('+2 month', $base)),
        3 => date('F Y', strtotime('+3 month', $base)),
        4 => date('F Y', strtotime('+4 month', $base)),
        5 => date('F Y', strtotime('+5 month', $base))
        ),
    'selected' => $months,
    'id' => 'monthSelector',
    'onchange' => 'this.form.submit()'
    ));
echo $this->Form->end('Go');?>
<?if (!empty($totalCount1)) {
	foreach ($totalCount1 as $key => $value) {
		if (empty($value[0])) {
			$value[0] = 0;
		}
		if (empty($value[1])) {
			$value[1] = 0;
		}
		if (empty($value[2])) {
			$value[2] = 0;
		}

		$total = $value[0] + $value[1] + $value[2];
		if ($total == 0) {
			$total = 1;
		}
		$value['total'] = $total;
		$totalCount1[$key] = $value;
	}
}?>
<div class='teamsRow'>
	<? if (!empty($monthCount)) {?>
		<div class='teamsContainer' style='min-width:0'>
			<div class='teamsContainerHeader'>
				Monthly Performance
			</div>
			<span class='teamsContainerSpan'><b><?echo $monthCount;?></b>tweets</span>
			<small> this month</small>
		</div>
	<?}?>
	<? if (!empty($weekCount)) {?>
		<div class='teamsContainer' style='min-width:0'>
			<div class='teamsContainerHeader'>
				Weekly Performance
			</div>
			<span class='teamsContainerSpan'><b><?echo $weekCount;?></b>tweets</span>
			<small> this week</small>
		</div>
	<?}?>
	<? if (!empty($dayCount)) {?>
		<div class='teamsContainer' style='min-width:0'>
			<div class='teamsContainerHeader'>
				Daily Performance
			</div>
			<span class='teamsContainerSpan'><b><?echo $dayCount;?></b>tweets</span>
			<small> today</small>
		</div>
	<?}?>
</div>
<? if (!empty($tableTweets1)) {?>
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
						<td class='screenName' style='display:block'><?echo $totalCount1[$key]['screen_name'];?></td>
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
<?}?>
<div class='teamsRow'>
	<div class='teamsContainer' style='min-width: 0'>
		<div class='teamsContainerHeader'>
		<b>User's Performance</b>
		</div>
		<canvas id="barChart" max-width="900" height="300"></canvas>
	</div>
</div>
<!--<div class='teamsRow'>
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
		<div class='teamsContainer byAccount'>
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
			<canvas class="myChart" width="120" height="120" data-approved= "<? echo $value[1]?>" data-not-approved= "<? echo $value[0]?>" data-improve= "<? echo $value[2]?>" data-empty="<?echo$total - $value['calendarCount'];?>"></canvas>
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
</div>-->

<script>
$(document).ready(function() { 
	$('.multiProgressBar').show('slide');
	<? if ($months == 0) {?>
		$('tr td:nth-child(n + <?echo date("d");?>), tr th:nth-child(n + <?echo date("d");?>)').css('opacity', '1');
	<?} else {?>
		$('tr td').css('opacity', '1');
	<?}?>
	$(".myChart").each(function () {
		var ctx = $(this).get(0).getContext("2d");
		var doughnutData = [
					    {
					        value: $(this).attr('data-approved'),
					        color:"#21a750",
					        highlight: "#5ee18c",
					        label: "Approved"
					    },
					    {
					        value: $(this).attr('data-not-approved'),
					        color: "#ffcc00",
					        highlight: "#efd35d",
					        label: "Awaiting Approval"
					    },
					    {
					        value: $(this).attr('data-improve'),
					        color: "#ff0000",
					        highlight: "#e73b3b",
					        label: "Need Improving"
					    },
					    {
					        value: $(this).attr('data-empty'),
					        color: "#fff",
					        highlight: "#dcdcdc",
					        label: "Not written"
					    }
					]
		var doughnutOptions = {segmentShowStroke : true, animationEasing : "easeOutCubic"}

		var myDoughnutChart = new Chart(ctx).Doughnut(doughnutData, doughnutOptions);
	});
	$("#barChart").each(function () {
		var ctx1 = $(this).get(0).getContext("2d");
		var barData = {
			labels: <?echo $barChartLabels;?>,
			datasets: [{
				label: "Tweets",
				fillColor: "rgba(151,187,205,0.5)",
	            strokeColor: "rgba(151,187,205,0.8)",
	            highlightFill: "rgba(151,187,205,0.75)",
	            highlightStroke: "rgba(151,187,205,1)",
	            data: <?echo $barChartData;?>
			}]
		}
		barOptions = {};
		var myBarChart = new Chart(ctx1).Bar(barData, barOptions);
	});

	$('.screenName').each(function () {
		$(this).qtip({ 
	        content: {
	            text: 
	            	$(this).find('canvas')
	        },
	        position: {
	            my: 'bottom center',
	            at: 'top center', 
	            target: 'event'
	        }
	    });
	});
});
</script>