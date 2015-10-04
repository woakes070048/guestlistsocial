<div id='editrefresh'>
<span class='recycleHead'>TweetBank</span>
<div class='recycleTweets'>
<div id="editloading" style="display: none;">
<? //echo $this->Html->image('ajax-loader.GIF'); ?>
<div class="loader">
    <svg class="circular">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10"/>
    </svg>
</div>
</div>

<?$random = rand(10000, 99999);?>


<?echo $this->Form->create('TwitterAccount', array('class' => 'recycleFilter'));
        ?>
        <div class="recycleSelectedAccount">
        	<?echo '@' . $selected;?>
	        <i class="fa fa-chevron-down"></i>
        </div>
        <div class="selectAccount scrollbar-macosx">
	        <?
	        echo $this->Form->input('Select Account:', array(
	        'name' => 'accountSubmit',
	        'type' => 'radio',
	        'default' => $selected,
	        'options' => array_combine($accounts,$accounts),
	        'legend' => false,
	        'id' => $random
	        ));?>
        </div>

		<div class="recycleSelectedCategory">
			<?
			if (empty($selectedCategories)) {
				$selectedCategories = 0;
				$categories[$selectedCategories] = 'No category selected';
			}
			?>
        	<?echo $categories[$selectedCategories];?>
	        <i class="fa fa-chevron-down"></i>
        </div>
        <div class="selectCategory scrollbar-macosx">
	        <?
	        echo $this->Form->input('Category', array(
	        'name' => 'BankCategory',
	        'type' => 'radio',
	        'default' => $selectedCategories,
	        'options' => $categories,
	        'legend' => false,
	        'id' => $random
	        ));?>
        </div>
		<?
	echo $this->Form->end();
?>
<hr style="border: 0; height: 1px; width: 75%; display: block; margin: 15px auto; background-color: #ccc;">
<? 
$i = 0;
?><div class='recycleBody <?echo 'recycle' . $i;?>'><?
foreach ($tweetBanks as $key => $value) {
	$i++;?>
	<!--<div class='recycleHeader <?echo 'recycle' . $i;?>'>
		<? echo $key;?>
		<div class='arrowdown'></div>

	</div>-->
				<div class='rr1'>
					<? if (!empty($value['TweetBank']['img_url'])) {
						echo $this->Html->image($value['TweetBank']['img_url'], array('width' => '30px', 'height' => '30px', 'class' => 'recycleBodyImage'));
					} else {?>
						<div style="height: 30px; width: 30px; border: 0; padding: 0; background-color: rgba(255, 255, 255, 0); margin: 0"></div>
					<?}?>
					<div><?echo $value['TweetBank']['body'];?></div>
					 <?
					 if (!empty($tweet_bank_counts[$value['TweetBank']['id']][0]['COUNT(tweet_bank_id)'])) {
					 	$count = $tweet_bank_counts[$value['TweetBank']['id']][0]['COUNT(tweet_bank_id)'];
					 } else {
					 	$count = '0';
					 }
					 ?>
					<div class="tweetBankCount" data-count="<? echo $count;?>">
						<? echo $count;?>
					</div>
				</div>
<?}?>
</div>
</div>

<script>
// wait for the DOM to be loaded 
$(document).ready(function () {
	$('.recycleHeader').click(function () {
		x = $(this).attr('class');
		x = x.split(' ');
		$(this).closest('.recycleTweets').find('.recycleBody.' + x[1]).slideToggle();
	});

	$('.recycleBodyImage').qtip({
		content: {
			text: function(event, api) {
				return '<img src=\"' + $(this).attr('src') + '\" width="400px">';
			}
		},
		position: {
			my: "right top",
			at: "left center",
			target: "event"
		}
	});

	$('.recycleBody .rr1 div').click(function (e) {
		text = $(this).text();
		image_url = $(this).closest('.rr1').find('img').attr('src');
		id = $(this).closest('.qtip-default').attr('id');
	    id = id.split('-')[1];
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('textarea').text(text);
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('#TweetImgUrl').val(image_url);
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('input[name=tosubmit]').val(true);
	    if (image_url) {
		    $('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('.calendar.verified').after(function () {
		    	$('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('.imagecontainer').hide();
		    	return '<div class="imagecontainer"><img src="' + image_url + '" style="max-width:496px;"></div>';
		    });
	    } else {
	    	$('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('.imagecontainer').hide();
	    }
	});

	$('.recycleFilter').on("change", function (e) {
	    e.preventDefault(); //STOP default action
		calendar_id = $(this).find('.selectCategory input[type="radio"]:checked').val();
	    var postData = $(this).serializeArray();
	    $(this).closest('#editrefresh').css('opacity', 0.4);
	    $(this).closest('#editloading').show();
	    $(this).closest('#editrefresh').load('/editorial_calendars/recycle/' + calendar_id, postData);
	    $(this).closest('#editloading').hide();
	    $(this).closest('#editrefresh').css('opacity', 1);
	    $(this).unbind(); //unbind. to stop multiple form submit.
	});

	$('.recycleSelectedAccount').click(function () {
		$(this).closest('#editrefresh').find('.selectAccount').toggle();
	});

	$('.recycleSelectedCategory').click(function () {
		$(this).closest('#editrefresh').find('.selectCategory').toggle();
	});

    $('.scrollbar-macosx').scrollbar();

    $('.tweetBankCount').qtip({
		content: {
			text: function(event, api) {
				count = $(this).attr('data-count');
				return 'This tweet has been used ' + count + ' times.';
			}
		},
		position: {
			my: "top center",
			at: "bottom center",
			target: "event"
		}
    });
});
</script>
</div>