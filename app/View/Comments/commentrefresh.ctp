<div id='comment'>
	<? echo $this->Html->image('/img/loader.gif', array('class' => 'loading', 'style' => 'position: absolute;top: 50%;right: 50%;display: none;')); ?>
	<table class='commentTable'>
		<? foreach ($comments as $key) {?>
			<tr>
				<td class='commentName'>
					<div style='margin:0'>
						<?echo $this->Html->image($key['User']['profile_pic'], array('style' => 'height: 19px'));?>
					<span><?echo $key['User']['first_name'] . ': ';?></span>
				</div>
				</td>
				<td class='commentBody'>
					<div>
						<?echo $key['Comment']['body'];?>
					</div>
				</td>
			</tr>
		<?}?>
	</table>
	<?	echo $this->Form->create('Comment', array('action' => 'commentSave', 'class' => 'Comment' . $tweet_id));
		echo $this->Form->input('body', array('label' => false, 'maxlength' => 500));
		echo $this->Form->input('tweet_id', array('value' => $tweet_id, 'type' => 'hidden'));
		echo $this->Form->submit('/img/edit.png', array('id' => 'commentSaveButton'));
		echo $this->Form->end();
	?>
</div>

<script>
$(document).ready(function() {
        $('.Comment' + <? echo $tweet_id;?>).submit(function(event) {
            event.preventDefault();
            $(this).closest('#comment').css('opacity', '.4');
            $(this).closest('#comment').find('.loading').toggle();
            $(this).ajaxSubmit({context: this, success: function() {
                id = $(this).find('#CommentTweetId').val();
                $(this).closest('.qtip-content').load('/comments/commentrefresh/' + id);
            }});
        });
});
</script>