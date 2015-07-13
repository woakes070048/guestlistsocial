<span class='recycleHead'>TweetBank</span>
<div class='recycleTweets'>
<span>Topic: <b2><?echo $topic;?></b2></span>
<? 
$i = 0;
foreach ($test as $key => $value) {
	$i++;?>
	<div class='recycleHeader <?echo 'recycle' . $i;?>'>
		<? echo $key;?>
		<div class='arrowdown'></div>

	</div>
		<div class='recycleBody <?echo 'recycle' . $i;?>' style='display: none;'>
			<? foreach ($value as $value1) {?>
				<div class='rr1'><div><?echo $value1['body'];?></div><? echo ($value1['img_url']) ? $this->Html->image('/img/imageicon.png', array('class' => 'recycleBodyImage', 'data' => $value1['img_url'])) : '' ;?></div>
			<?} ?>
		</div>
<?}?>
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
});
</script>