
<?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'edit')), 'id' => 'edit'));?>
<table id="table">
	<thead class='mainheader'>
		<th>Schedule</th>
		<th>Writer</th>
		<th>Tweet</th>
		<th>Status</th>
	</thead>
	<?php foreach ($tweets as $key) { ?>
	<?php if ($key['Tweet']['verified'] == 1) {
			$checked = 'checked';
			$value = $key['Tweet']['time'];
			$color = 'Green';
		} elseif ($key['Tweet']['verified'] == 1 && $key['Tweet']['client_verified'] == 1) {
			$color = 'Green';
		} else {
			$checked = '';
			$value = '';
			$color = 'Red';} 

			if ($this->Session->read('Auth.User.group_id') == 2) {
				$disabled = 'disabled';
			} else {
				$disabled = '';
			}?>
	<tr>
	  <td class= 'time scheduled' id='time<?php echo $key['Tweet']['id']?>'> 
	  	<div class='notediting'><?php if($key['Tweet']['time'] && $key['Tweet']['published'] == 1) {
	  		echo $key['Tweet']['time'] . '<small>[Published]</small>';
	  		} elseif ($key['Tweet']['time']) {
	  			echo $key['Tweet']['time'];
	  		} else {
	  			echo 'Click to schedule';
	  			} ?>
	  	</div>
	  	<?php if($key['Tweet']['published'] == 0) {
	  		echo $this->Form->input('timestamp', array(
	  		'type' => 'text', 
	  		'label' => false, 
	  		'class' => 'schedule',
	  		'value' => $key['Tweet']['time'], 
	  		'id' => 'schedule'.$key['Tweet']['id'], 
	  		'name' => 'data[Tweet]['.$key['Tweet']['id'].'][timestamp]',
	  		'style' => 'display: none'
	  		));
	  		}
	  		if($key['Tweet']['verified'] == 0 && strtotime($key['Tweet']['time']) > time()) {
	  				echo "<span style='color: red'>*Tweet will not be sent until verified</span>";
	  			}?>
	  </td>
	  <td class='writtenBy'>
	  	<?php echo $key['Tweet']['first_name']; ?>
	  </td>
	  <td class='tweetbody nopadding' id=<?php echo $key['Tweet']['id']?>>
	  	<div class='notediting'><?php echo $key['Tweet']['body']; ?></div>
	  	<?php echo $this->Form->textarea('body', array(
	  		'class' => 'editing', 
	  		'value' => $key['Tweet']['body'], 
	  		'name' => 'data[Tweet]['.$key['Tweet']['id'].'][body]', 
	  		'label' => false, 
	  		'style' => 'display: none',
			'maxlength' => '140')); ?>
            
            <div class="tweetButtons">
            <? echo $this->Form->button('Shorten URLs', array('class' => 'urlSubmit1 shortsingle', 'type' => 'button')); ?>
            <? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$value1.'][img_url1]', 'label' => false)); ?>
            <? echo $this->Form->button('Delete', array('type' => 'button', 'class' => 'delete', 'id' => $key['Tweet']['id'])); ?>
            <? echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'smallSaveButton'));?>
            <? if ($img) {
                    echo $this->Html->image($img, array('style' => 'max-width:500px'));
                }?>
            </div> 
	  </td>
	  <td class='verified'>
	  	<?php echo $this->Form->input('verified', array(
	  	'type' => 'radio', 
	  	'options' => array(
	  			1 => 'APPROVED', 
	  			0 => 'AWAITING APPROVAL', 
	  			2 => 'IMPROVE'
	  			), 
	  	'legend' => false, 
	  	'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified]', 
	  	'class' => 'TwitterVerified', 
	  	'id' => $key['Tweet']['id'], 
	  	'default' => $key['Tweet']['verified'], 
	  	$disabled));?> 

	  	<? if ($key['Tweet']['verified'] == 1) {?>
	  	<i><small>-<? echo $key['Tweet']['verified_by'];?></small></i>
	  	<?}?>
	  </td>
	  <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $key['Tweet']['id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][id]'));
	  		echo $this->Form->input('verfied_by', array(
	  		'type' => 'hidden', 
	  		'value' => $this->Session->read('Auth.User.first_name'), 
	  		'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified_by]', 
	  		'class' => 'verifiedby', 
	  		'id' => $key['Tweet']['id'] . '_' . $this->Session->read('Auth.User.first_name')));?>
	</tr>
	<?php } ?>
</table>

<?php echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save')); ?>

<script>
            $(".verifiedby").prop('disabled', true);
//Hiding and showing tweet body input on click
			$("#table").on("click", ".tweetbody", function() {
				id = $(this).attr('id');
				$("#" + id + " .notediting").hide();
				$("#" + id + " .editing").show();
				$("#" + id + " .editing").focus();
			});

			$("#table").on("click", ".time", function() {
				id = $(this).attr('id');
				if ($("#" + id + " .schedule").length) {
						$("#" + id + " .notediting").hide();
				}
				$("#" + id + " .schedule").show();
				$("#" + id + " .schedule").focus();
				$("#" + id + " .schedule").css("margin-bottom", "1em");
			});

			$(".editing").blur(function(){
				id = $(this).parent().attr('id');
				$("#" + id + " .editing").hide();
				value = $("#" + id + " .editing").val();
				$("#" + id + " .notediting").text(value);
				$("#" + id + " .notediting").show();
			});

			$("#table").on("change", ".schedule", function() {
				id = $(this).parent().parent().attr('id');
				value = $("#" + id + " .schedule").val();
				$("#" + id + " .notediting").text(value);
			});

			$(".schedule").blur(function(){
				id = $(this).parent().parent().attr('id');
				$("#" + id + " .schedule").hide();
				value = $("#" + id + " .schedule").val();
				$("#" + id + " .notediting").text(value);
				$("#" + id + " .notediting").show();
			});

			$('.schedule').each(function(){
				$(this).datetimepicker({
    				dateFormat: 'dd-mm-yy',
    				altFormat: '@',
				});
    		});

    		warnMessage = "You have unsaved changes on this page, if you leave your changes will be lost.";
            $(".editing").on('change', function () {
                window.onbeforeunload = function () {
                    if (warnMessage != null) return warnMessage;
                }
            });

            $('input:submit').on('click', function() {
                warnMessage = null;
            });

            $(".shortsingle").click(function () {
                regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
                textbox = $(this).closest('.nopadding').children('.editing');
                var longUrlLink = textbox.val().match(regex);
                    jQuery.urlShortener({
                        longUrl: longUrlLink,
                        success: function (shortUrl) {
                            textbox.val(textbox.val().replace(longUrlLink, shortUrl));
                        },
                        error: function(err) {
                            $("#shortUrlInfo").html(JSON.stringify(err));
                        }
                    });
            });
</script>