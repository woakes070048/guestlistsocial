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


<?echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
        ));
		echo $this->Form->input('Category', array(
		'type' => 'select',
		'name' => 'BankCategory',
		'options' => $categories,
		'selected' => $selectedCategories,
		'empty' => 'Select a Category'
		));
	echo $this->Form->end();
?>
<? 
$i = 0;
?><div class='recycleBody <?echo 'recycle' . $i;?>'><?
foreach ($tweetBanks as $key => $value) {
	$i++;?>
	<!--<div class='recycleHeader <?echo 'recycle' . $i;?>'>
		<? echo $key;?>
		<div class='arrowdown'></div>

	</div>-->
				<div class='rr1'><div><?echo $value['TweetBank']['body'];?></div><? echo ($value['TweetBank']['img_url']) ? $this->Html->image('/img/imageicon.png', array('class' => 'recycleBodyImage', 'data' => $value['TweetBank']['img_url'])) : '' ;?></div>
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
		image_url = $(this).closest('.rr1').find('img').attr('data');
		id = $(this).closest('.qtip-default').attr('id');
	    id = id.split('-')[1];
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('textarea').text(text);
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('#TweetImgUrl').val(image_url);
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('input[name=tosubmit]').val(true);
	    if (image_url) {
		    $('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('.file').after(function () {
		    	$('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('.imagecontainer').hide();
		    	return '<div class="imagecontainer"><img src="' + image_url + '" style="max-width:500px;"></div>';
		    });
	    } else {
	    	$('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('.imagecontainer').hide();
	    }
	});

	$('#TwitterAccountRecycleForm').change(function (e) {
	    var postData = $(this).serializeArray();
	    $('#editrefresh').css('opacity', 0.4);
	    $('#editloading').show();
	    $('#editrefresh').load('/editorial_calendars/recycle/137', postData);
	    $('#editloading').hide();
	    $('#editrefresh').css('opacity', 1);
	    e.preventDefault(); //STOP default action
	    e.unbind(); //unbind. to stop multiple form submit.
	});
});
</script>
</div>