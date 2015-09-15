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
	        'legend' => false
	        ));?>
        </div>

		<div class="recycleSelectedCategory">
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
	        'legend' => false
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
				<div class='rr1'><div><?echo $value['TweetBank']['body'];?></div><? echo ($value['TweetBank']['img_url']) ? "<i class='fa fa-camera fa-fw recycleBodyImage' data='" . $value['TweetBank']['img_url'] . "'>" : '' ;?></div>
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
				return '<img src=\"' + $(this).attr('data') + '\" width="400px">';
			}
		}
	});

	$('.recycleBody .rr1 div').click(function (e) {
		text = $(this).text();
		image_url = $(this).closest('.rr1').find('i').attr('data');
		id = $(this).closest('.qtip-default').attr('id');
	    id = id.split('-')[1];
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('textarea').text(text);
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('#TweetImgUrl').val(image_url);
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('input[name=tosubmit]').val(true);
	    if (image_url) {
		    $('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('.file').after(function () {
		    	$('.calendar_topic[data-hasqtip=' + id + ']').closest('.tweet').find('.imagecontainer').hide();
		    	return '<div class="imagecontainer"><img src="' + image_url + '" style="max-width:500px;"></div>';
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
	    $('.recycleFilter').unbind(); //unbind. to stop multiple form submit.
	});

	$('.recycleSelectedAccount').click(function () {
		$(this).closest('#editrefresh').find('.selectAccount').toggle();
	});

	$('.recycleSelectedCategory').click(function () {
		$(this).closest('#editrefresh').find('.selectCategory').toggle();
	});

    $('.scrollbar-macosx').scrollbar();
});
</script>
</div>