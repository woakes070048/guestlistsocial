<div id='comment'>
	<? echo $this->Html->image('/img/loader.gif', array('class' => 'loading', 'style' => 'position: absolute;top: 50%;right: 50%;display: none;')); ?>
	<table class='commentTable'>
		<? foreach ($comments as $key) {?>
			<tr>
				<td class='commentName'>
					<div style='margin:0'>
						<?echo $this->Html->image($key['User']['profile_pic'], array('style' => 'height: 24px'));?>
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

                idx = $(this).closest('.qtip-default').attr('id');
	            idx = idx.split('-')[1];
	            comment = '.comments[data-hasqtip=' + idx + ']'

	            if ($(comment).hasClass('badge0')) {
	            	$(comment).removeClass('badge0').addClass('badge1');
	            } else if ($(comment).hasClass('badge1')) {
	            	$(comment).removeClass('badge1').addClass('badge2');
	            } else if ($(comment).hasClass('badge2')) {
	            	$(comment).removeClass('badge2').addClass('badge3');
	            } else if ($(comment).hasClass('badge3')) {
	            	$(comment).removeClass('badge3').addClass('badge4');
	            } else if ($(comment).hasClass('badge4')) {
	            	$(comment).removeClass('badge4').addClass('badge5');
	            } else if ($(comment).hasClass('badge5')) {
	            	$(comment).removeClass('badge5').addClass('badge6');
	            } else if ($(comment).hasClass('badge6')) {
	            	$(comment).removeClass('badge6').addClass('badge7');
	            } else if ($(comment).hasClass('badge7')) {
	            	$(comment).removeClass('badge7').addClass('badge8');
	            } else if ($(comment).hasClass('badge8')) {
	            	$(comment).removeClass('badge8').addClass('badge9');
	            } else if ($(comment).hasClass('badge9')) {
	            	$(comment).removeClass('badge9').addClass('badge9plus');
	            } 
            }});
        });
});
</script>