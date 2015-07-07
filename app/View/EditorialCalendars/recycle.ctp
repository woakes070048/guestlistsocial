<span class='recycleHead'>Recycle old tweets: <b>Approved</b> and <b>Published</b> <br /> Topic: <b2><?echo $topic;?></b2></span>
<div class='recycleTweets'>
<? foreach ($test as $key => $value) {?>
	<div class='recycleHeader'>
		<? echo $key;?>
		<div class='arrowdown'></div>

	</div>
		<div class='recycleBody'>
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
		$(this).closest('.recycleTweets').find('.recycleBody').slideToggle();
	});

	$('.recycleBodyImage').qtip({
		content: {
			text: function(event, api) {
				return '<img src=\"' + $(this).attr('data') + '\">';
			}
		}
	});

	$('.recycleBody .rr1 div').click(function (e) {
		text = $(this).text();
		image_url = $(this).closest('div').find('img').attr('data');
		id = $(this).closest('.qtip-default').attr('id');
	    id = id.split('-')[1];
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('textarea').text(text);
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('#TweetImgUrl').val(image_url);
	    $('.calendar_topic[data-hasqtip=' + id + ']').closest('tr').find('input[name=tosubmit]').val(true);
	});
});
</script>