
<?
echo $this->Html->script('Chart.min');
?>
<div class='slick'>
    <?
    $base = strtotime(date('Y-m',time()) . '-01 00:00:01'); 
    $monthsarray = array(
        0 => date('F Y', strtotime('+0 month', $base)),
        1 => date('F Y', strtotime('+1 month', $base)),
        2 => date('F Y', strtotime('+2 month', $base)),
        3 => date('F Y', strtotime('+3 month', $base)),
        4 => date('F Y', strtotime('+4 month', $base)),
        5 => date('F Y', strtotime('+5 month', $base))
        );
    foreach ($monthsarray as $key => $value) {?>
        <div data-month=<?echo $key;?>>
            <?echo $value;?>
        </div>
    <?}?>
</div>
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
	}?>
<div class='teamsRow'>
		<div class='teamsContainer' style='min-width:0'>
			<div class='teamsContainerHeader'>
				Monthly Performance
			</div>
			<span class='teamsContainerSpan'>
				<b>
					<? if (!empty($monthCount)) {?>
						<?echo $monthCount;?>
					<?} else {
						echo 0;
					}?>
				</b>
			tweets</span>
			<small> this month</small>
		</div>
		<div class='teamsContainer' style='min-width:0'>
			<div class='teamsContainerHeader'>
				Weekly Performance
			</div>
			<span class='teamsContainerSpan'>
				<b>
					<? if (!empty($weekCount)) {?>
						<?echo $weekCount;?>
					<?} else {
						echo 0;
					}?>
				</b>
			tweets</span>
			<small> this week</small>
		</div>
		<div class='teamsContainer' style='min-width:0'>
			<div class='teamsContainerHeader'>
				Daily Performance
			</div>
			<span class='teamsContainerSpan'>
				<b>
					<? if (!empty($dayCount)) {?>
						<?echo $dayCount;?>
					<?} else {
						echo 0;
					}?>
				</b>
			tweets</span>
			<small> today</small>
		</div>
</div>
<?}?>
<? if (!empty($tableTweets1)) {?>
	<div class='teamsRow'>
		<div class='teamsContainer'>
		<div class='teamsContainerHeader'>
		<b>Team Overview:</b>
		</div>

		<div style='float: right; padding: 10px; font-size: 12px; padding-left: 0;'>
			<div id="howManyWrittenBlock1" style='float: none; display: inline-block; margin: 0 5px 0 10px;'></div>All Approved
			<div id="howManyWrittenBlock0" style='float: none; display: inline-block; margin: 0 5px 0 10px;'></div>Some still to be Approved
			<div id="howManyWrittenBlock2" style='float: none; display: inline-block; margin: 0 5px 0 10px;'></div>Some need Improving
			<div id="howManyWrittenBlock0" style='float: none; display: inline-block; margin: 0 5px 0 10px; background: none; border: 1px solid #e4e4e4;'></div>Some Tweets missing<br />
		</div>
		<small style='float:right; font-size: 10px;'>(Click a box to be redirected to the day-by-day view for that day)</small>
			<table id='teamOverview' style='border-spacing: 1px; padding: 10px;'>
				<tr>
					<th>
					</th>
					<?for ($i=1; $i <= date('t'); $i++) {?>
						<th style='font-size: 7pt;'><?echo date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')));?><div></div></th>
					<?}?>
				</tr>
				<?foreach ($tableTweets1 as $key => $value) {?>
					<tr>
						<td style='display:block' data-scroll='0' data-account-id='<?echo $key;?>'><?echo $this->Html->image($totalCount1[$key]['profile_pic'], array('width' => '30px', 'style' => 'border-radius: 15px;', 'data-name' => $totalCount1[$key]['screen_name']));?></td>
						<?for ($i=1; $i <= date('t'); $i++) {?>
								<?
								if (empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1])) {
									$value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1] = 0;
								}
								if (empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][2])) {
									$value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][2] = 0;
								}
								if (empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][0])) {
									$value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][0] = 0;
								}

								if ($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1] == $totalCount1[$key]['calendarCount']) {
									$class = 'allApproved';
								} elseif (($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1] + $value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][0]) == $totalCount1[$key]['calendarCount']) {
									$class = 'notAllApproved';
								} elseif ($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][2]) {
									$class = 'improveApproved';
								} else {
									$class = '';
								}
								/*if (!empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][0])) {								if ($totalCount1[$key]['calendarCount'] == ($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][0] + $value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1])) {
										$class = 'notAllApproved';
									} else {
										$class = '';
									}
								} else {
									$class = '';
								}
								if (!empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1])) {
									if ($totalCount1[$key]['calendarCount'] == $value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][1]) {
										$class = 'allApproved';
									} else {
										$class = '';
									}
								} else {
									$class = '';
								}
								if (!empty($value[date('jS', strtotime($i . '-' . date('m') . '-' . date('Y')))][2])) {
										$class = 'improveApproved';
								} else {
										$class = '';
								}*/?>
							<td class='<?echo $class;?>' data-scroll='<?echo $i;?>' data-account-id='<?echo $key;?>'>
							</td>
						<?}?>
					</tr>
				<?}?>
			</table>
		</div>
	</div>

	<div class='teamsRow'>
		<div class='teamsContainer' style='min-width: 0'>
			<div class='teamsContainerHeader'>
			<b>User's Performance</b>
			</div>
			<canvas id="barChart" width="660" height="200"></canvas>
		</div>
	</div>
<?}?>
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
		$('tr td:nth-child(n + <?echo date("d") + 1;?>), tr th:nth-child(n + <?echo date("d");?>)').css('opacity', '1');
	<?} else {?>
		$('tr td').css('opacity', '1');
	<?}?>
	$('tr td:nth-child(n + <?echo date("d") + 1;?>)').hover(function () {
		$(this).css('opacity', '0.5');
	}, function () {
		$(this).css('opacity', '1');
	});

	$('tr td:nth-child(n + 1)').click(function () {
		scroll = $(this).attr('data-scroll');
		account_id = $(this).attr('data-account-id');
		window.location.replace("/tweets?s=" + scroll + "&m=" + <?echo $months;?> + "&accid=" + account_id);
	});
	
	<? if (!empty($barChartData)) {?>
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
	<?}?>

	$('#teamOverview img').qtip({
		content: {
			text: function(event, api) {
				name = $(this).attr('data-name');
				return name;
			}
		},
        position: {
            my: 'bottom center',
            at: 'top center', 
            target: 'event'
        }
	});

	$('.slick').slick({
        prevArrow: "<div class='slick-arrowleft'></div>",
        nextArrow: "<div class='slick-arrowright'></div>",
        initialSlide: <? echo $this->Session->read('Auth.User.monthSelector');?>
    });

    $('.slick-arrowright, .slick-arrowleft').click(function () {
        $('#table').css('opacity', '.4');
        $('#loading').show();
        var month = $(this).closest('.slick').find('.slick-current').attr('data-month');
        $('#table').load('/teams/manageteam?m=' + month, function () {
            $('#table').css('opacity', '1');
            $('#loading').hide();
        });
    });
});
</script>